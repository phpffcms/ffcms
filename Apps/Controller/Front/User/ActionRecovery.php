<?php
/**
 * Created by PhpStorm.
 * User: zenn1
 * Date: 11.01.2018
 * Time: 21:00
 */

namespace Apps\Controller\Front\User;

use Apps\ActiveRecord\UserRecovery;
use Apps\Model\Front\User\FormLogin;
use Apps\Model\Front\User\FormPasswordChange;
use Apps\Model\Front\User\FormRecovery;
use Ffcms\Core\App;
use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionRecovery
 * @package Apps\Controller\Front\User
 * @property View $view
 * @property Request $request
 * @property Response $response
 */
trait ActionRecovery
{
    /**
     * Recovery form and recovery submit action
     * @param int|null $id
     * @param string|null $token
     * @return string
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function recovery($id = null, $token = null)
    {
        if (App::$User->isAuth()) {
            throw new ForbiddenException(__('You are always authorized on website, recovery is rejected'));
        }

        // check if recovery token and user_id is passed and validate it
        if (Any::isInt($id) && Str::length($token) >= 64) {
            $rObject = UserRecovery::where('id', $id)
                ->where('token', $token)
                ->where('archive', false);

            // check if recovery row exist
            if ($rObject->count() !== 1) {
                throw new NotFoundException(__('This recovery data is not found'));
            }

            /** @var UserRecovery $rData */
            $rData = $rObject->first();
            // check if user with this "user_id" in recovery row exist
            $rUser = App::$User->identity($rData->user_id);
            if ($rUser === null) {
                throw new NotFoundException(__('User is not found'));
            }

            // email link valid, show new password set form
            $modelPwd = new FormPasswordChange($rUser);
            // process new password submit
            if ($modelPwd->send() && $modelPwd->validate()) {
                // new password is valid, update user data
                $modelPwd->make();
                // set password change token as archived row
                $rData->archive = true;
                $rData->save();
                // add event notification
                // add success event
                App::$Event->run(static::EVENT_USER_RECOVERY_SUCCESS, [
                    'model' => $modelPwd
                ]);
                // add notification
                App::$Session->getFlashBag()->add('success', __('Your account password is successful changed!'));

                // lets open user session with recovered data
                $loginModel = new FormLogin();
                $loginModel->openSession($rUser);
                $this->response->redirect('/'); // session is opened, refresh page
            }

            return $this->view->render('password_recovery', [
                'model' => $modelPwd
            ]);
        }

        // initialize and process recovery form data
        $model = new FormRecovery(true);
        if ($model->send()) {
            if ($model->validate()) {
                $model->make();
                App::$Session->getFlashBag()->add('success', __('We send to you email with instruction to recovery your account'));
            } else {
                App::$Session->getFlashBag()->add('error', __('Form validation is failed'));
            }
        }

        // render visual form content
        return $this->view->render('user/recovery', [
            'model' => $model
        ]);
    }
}
