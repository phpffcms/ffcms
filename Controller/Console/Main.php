<?php

namespace Controller\Console;

class Main
{
    // php console.php main/index
    public function actionIndex($id = null)
    {
        return 'Hello, console! Id: ' . $id;
    }
}