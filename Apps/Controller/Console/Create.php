<?php

namespace Apps\Controller\Console;

use Apps\Model\Console\ArchBuilder;

class create
{
    public function actionModel($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Model');
        return $builder->message;
    }

    public function actionController($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Controller');
        return $builder->message;
    }

    public function actionAr($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'ActiveRecord');
        return $builder->message;
    }
    
    public function actionWidget($name)
    {
        $builder = new ArchBuilder();
        $builder->createObject($name, 'Widget');
        return $builder->message;
        
    }
}