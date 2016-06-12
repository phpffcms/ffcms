<?php

namespace Apps\Controller\Console;

use Apps\Model\Console\ArchBuilder;

/**
 * Class Create. Create basic cmf entities for app building.
 * @package Apps\Controller\Console
 */
class Create
{
    /**
     * Create app model.
     * @param string $name
     * @return string
     */
    public function actionModel($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Model');
        return $builder->message;
    }

    /**
     * Create app controller.
     * @param string $name
     * @return string
     */
    public function actionController($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Controller');
        return $builder->message;
    }

    /**
     * Create app active record table relation.
     * @param string $name
     * @return string
     */
    public function actionAr($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'ActiveRecord');
        return $builder->message;
    }

    /**
     * Create widget main class skeleton.
     * @param string $name
     * @return string
     */
    public function actionWidget($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Widget');
        return $builder->message;
        
    }
}