<?php

namespace Apps\Model\Basic;

use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\File;

class Antivirus
{
    const EXECUTE_LIMIT = 5; // in seconds
    const DEPRECATED_TIME = 3600; // actuality of scan list
    // file extensions to be scanned
    public $affectedExt = [
        '.php', '.php3', '.phtml',
        '.htm', '.html',
        '.txt', '.js', '.pl', '.cgi', '.py', '.bash', '.sh', '.ssi', '.inc', '.pm', '.tpl'
    ];

    private $signatures;
    private $beginTime;
    private $scanFiles;

    private $infected;

    public function __construct()
    {
        if (!File::exist('/Private/Antivirus/Signatures.xml')) {
            throw new NativeException('Antivirus signatures is not founded in: {root}/Private/Antivirus/Signatures.xml');
        }

        $this->beginTime = time();

        $this->signatures = new \DOMDocument();
        $this->signatures->load(root . '/Private/Antivirus/Signatures.xml');

        // list of files is not prepared, 1st iteration
        if (!File::exist('/Private/Antivirus/ScanFiles.json')) {
            $this->prepareScanlist();
        }

        if (!File::exist('/Private/Antivirus/ScanFiles.json')) {
            throw new SyntaxException('Directory /Private/Antivirus/ can not be writed!');
        }

        $this->scanFiles = json_decode(File::read('/Private/Antivirus/ScanFiles.json'));
        if (File::exist('/Private/Antivirus/Infected.json')) {
            $this->infected = (array)json_decode(File::read('/Private/Antivirus/Infected.json'));
        }
    }

    /**
     * Make scan of files with preparation and time limit
     * @return array
     */
    public function make()
    {
        foreach ($this->scanFiles as $idx => $file) {
            $now = time();
            // calculate time limit for scan per file
            if ($now - $this->beginTime >= static::EXECUTE_LIMIT) {
                break;
            }

            $this->scanContent($file);
            unset($this->scanFiles->$idx);
        }

        // write infected info
        File::write('/Private/Antivirus/Infected.json', json_encode($this->infected));
        // refresh file list to scan ;)
        File::write('/Private/Antivirus/ScanFiles.json', json_encode($this->scanFiles));

        return [
            'left' => count(get_object_vars($this->scanFiles)),
            'detect' => count($this->infected)
        ];
    }

    /**
     * Scan signle file via defined $path
     * @param string $path
     * @return bool
     */
    private function scanContent($path)
    {
        // get file content plain
        $content = File::read($path);

        // nothing to check
        if ($content === null || $content === false) {
            return false;
        }

        $normalized = $this->normalizeContent($content);

        // list malware signatures
        $db = $this->signatures->getElementsByTagName('signature');
        $detected = false;
        foreach ($db as $sig) {
            $sigContent = $sig->nodeValue;
            $attr = $sig->attributes;
            $attrId = $attr->getNamedItem('id')->nodeValue;
            $attrFormat = $attr->getNamedItem('format')->nodeValue;
            $attrTitle = $attr->getNamedItem('title')->nodeValue;
            $attrSever = $attr->getNamedItem('sever')->nodeValue;

            switch ($attrFormat) {
                case 're':
                    if ((preg_match('#(' . $sigContent . ')#smi', $content, $found, PREG_OFFSET_CAPTURE)) ||
                        (preg_match('#(' . $sigContent . ')#smi', $normalized, $found, PREG_OFFSET_CAPTURE))
                    ) {
                        $detected = true;
                        $pos = $found[0][1];
                        $this->infected[$path][] = [
                            'pos' => (int)$pos,
                            'sigId' => $attrId,
                            'sigRule' => $sigContent,
                            'sever' => $attrSever,
                            'title' => $attrTitle
                        ];
                    }

                    break;
                case 'const':
                    if ((($pos = strpos($content, $sigContent)) !== false) ||
                        (($pos = strpos($normalized, $sigContent)) !== false)
                    ) {
                        $this->infected[$path][] = [
                            'pos' => (int)$pos,
                            'sigId' => $attrId,
                            'sigRule' => $sigContent,
                            'sever' => $attrSever,
                            'title' => $attrTitle
                        ];
                        $detected = true;
                    }

                    break;
            }
        }
        return $detected;
    }

    /**
     * Prepare scan list on first run. Scan directory's and save as JSON
     */
    private function prepareScanlist()
    {
        $files = (object)File::listFiles(root, $this->affectedExt);
        File::write('/Private/Antivirus/ScanFiles.json', json_encode($files));
    }

    /**
     * Prepare content, replacing any encoding of it, based on Hex, OctDec or other offset's
     * @param string $content
     * @return mixed
     */
    private function normalizeContent($content)
    {
        //$content = @preg_replace_callback('/\\\\x([a-fA-F0-9]{1,2})/i', 'escapedHexToHex', $content); // strip hex ascii notation
        //$content = @preg_replace_callback('/\\\\([0-9]{1,3})/i', 'escapedOctDec', $content); // strip dec ascii notation
        $content = preg_replace('/[\'"]\s*?\.\s*?[\'"]/smi', '', $content); // concat fragmented strings
        $content = preg_replace('|/\*.*?\*/|smi', '', $content); // remove comments to detect fragmented pieces of malware

        return $content;
    }
}
