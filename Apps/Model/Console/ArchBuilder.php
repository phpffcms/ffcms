<?php

namespace Apps\Model\Console;

use Ffcms\Core\Helper\Directory;
use Ffcms\Core\Helper\File;
use Ffcms\Core\Helper\String;

class ArchBuilder
{
    public $message;

    public function createObject($name, $type)
    {
        $singleName = false;
        if (!String::contains('/', $name)) {
            if ($type === 'ActiveRecord') {
                $singleName = true;
            } else {
                $this->message = 'Command dosn\'t contains valid name. Example: Front/SomeForm, Admin/SomePkg/SomeInput';
                return false;
            }
        }

        $objectDirPath = null;
        $objectNamespace = null;
        $objectName = null;

        $objectTemplate = null;

        if ($singleName) {
            $objectDirPath = root . '/Apps/' . $type . '/';
            $objectNamespace = 'Apps\\' . $type;
            $objectName = ucfirst($name);
        } else {
            $split = explode('/', $name);
            $workground = ucfirst(strtolower(array_shift($split)));
            $objectName = ucfirst(array_pop($split));

            $subName = false;
            if (count($split) > 0) { // some sub-namespace / folder path
                foreach ($split as $part) {
                    if (String::length($part) > 0) {
                        $subName[] = ucfirst(strtolower($part));
                    }
                }
            }

            $objectDirPath = root . '/Apps/' . $type . '/' . $workground;
            $objectNamespace = 'Apps\\' . $type . '\\' . $workground;
            if (false !== $subName) {
                $objectDirPath .= '/' . implode('/', $subName);
                $objectNamespace .= '\\' . implode('\\', $subName);
            }


            // try to find workground-based controller
            if (File::exist('/Private/Carcase/' . $workground . '/' . $type . '.tphp')) {
                $objectTemplate = File::read('/Private/Carcase/' . $workground . '/' . $type . '.tphp');
            }
        }

        if (!File::exist($objectDirPath) && !Directory::create($objectDirPath)) {
            $this->message = 'Directory could not be created: ' . $objectDirPath;
            return false;
        }

        if ($objectTemplate === null) {
            $objectTemplate = File::read('/Private/Carcase/' . $type . '.tphp');
            if (false === $objectTemplate) {
                $this->message = 'Php template file is not founded: /Private/Carcase/' . $type . '.tphp';
                return false;
            }
        }

        $objectContent = String::replace(['%namespace%', '%name%'], [$objectNamespace, $objectName], $objectTemplate);
        $objectFullPath = $objectDirPath . '/' . $objectName . '.php';
        if (File::exist($objectFullPath)) {
            $this->message = $type . ' is always exist: ' . $objectFullPath;
            return false;
        }
        File::write($objectFullPath, $objectContent);
        $this->message = $type . ' template was created: ' . $objectName;
        return true;
    }
}