<?php

namespace Apps\Controller\Front;

use Extend\Core\Arch\Controller;

/**
 * Class Main. Default website entry point
 * @package Apps\Controller\Front
 */
class Main extends Controller
{
    /**
     * Before action method call injection
     */
    public function before()
    {
        //self::$layout = 'other.php';
    }

    /**
     * Default index action
     * @return null|string
     */
    public function actionIndex()
    {
        $this->wtf = 'Test global variable';

        return $this->view->render('main/index', ['t1' => 'test1', 't2' => 'test2']);
    }

    /**
     * Pass id inside example
     * @param int $id
     */
    public function actionRead($id)
    {
        echo "Action read called" . $id;
    }
}
