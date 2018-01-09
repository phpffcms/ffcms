<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Blacklist;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Apps\ActiveRecord\UserLog;
use Apps\ActiveRecord\UserNotification;
use Apps\Model\Front\Profile\FormIgnoreDelete;
use Apps\Model\Front\Profile\FormPasswordChange;
use Apps\Model\Front\Profile\FormSettings;
use Apps\Model\Front\Sitemap\EntityBuildMap;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Url;

/**
 * Class Profile. User profiles application front controller
 * @package Apps\Controller\Front
 */
class Profile extends FrontAppController
{
    const BLOCK_PER_PAGE = 10;
    const EVENT_CHANGE_PASSWORD = 'profile.changepassword.success';

    /**
     * Fatty action like actionIndex(), actionShow() are located in standalone traits.
     * This feature allow provide better read&write accessibility
     */

    use Profile\ActionIndex {
        index as actionIndex;
    }

    use Profile\ActionShow {
        show as actionShow;
    }

    use Profile\ActionFeed {
        feed as actionFeed;
    }

    use Profile\ActionWallDelete {
        wallDelete as actionWalldelete;
    }

    use Profile\ActionAvatar {
        avatar as actionAvatar;
    }

    use Profile\ActionNotifications {
        notifications as actionNotifications;
    }

    use Profile\ActionIgnore {
        ignore as actionIgnore;
    }

    use Profile\ActionSearch {
        search as actionSearch;
    }


    /**
     * Show user messages (based on ajax, all in template)
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionMessages()
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        return $this->view->render('messages');
    }

    /**
     * User profile settings
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionSettings()
    {
        // check if auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object
        $user = App::$User->identity();
        // create model and pass user object
        $model = new FormSettings($user);

        // check if form is submited
        if ($model->send() && $model->validate()) {
            $model->save();
            App::$Session->getFlashBag()->add('success', __('Profile data are successful updated'));
        }

        // render view
        return $this->view->render('settings', [
            'model' => $model
        ]);
    }

    /**
     * Action change user password
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionPassword()
    {
        // check if user is authed
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get user object and create model with user object
        $user = App::$User->identity();
        $model = new FormPasswordChange($user);

        // check if form is submited and validation is passed
        if ($model->send() && $model->validate()) {
            $model->make();
            App::$Event->run(static::EVENT_CHANGE_PASSWORD, [
                'model' => $model
            ]);

            App::$Session->getFlashBag()->add('success', __('Password is successful changed'));
        }

        // set response output
        return $this->view->render('password', [
            'model' => $model
        ]);
    }

    /**
     * Show user logs
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     */
    public function actionLog()
    {
        // check if user is authorized
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // get log records
        $records = UserLog::where('user_id', App::$User->identity()->getId());
        if ($records->count() > 0) {
            $records = $records->orderBy('id', 'DESC');
        }

        // render output view
        return $this->view->render('log', [
            'records' => $records
        ]);
    }

    /**
     * Unblock always blocked user
     * @param string $targetId
     * @return string
     * @throws \Ffcms\Core\Exception\SyntaxException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws \Exception
     */
    public function actionUnblock($targetId)
    {
        // check if user is auth
        if (!App::$User->isAuth()) {
            throw new ForbiddenException();
        }

        // check if target is defined
        if (!Any::isInt($targetId) || $targetId < 1 || !App::$User->isExist($targetId)) {
            throw new NotFoundException();
        }

        $user = App::$User->identity();

        // check if target user in blacklist of current user
        if (!Blacklist::have($user->getId(), $targetId)) {
            throw new NotFoundException();
        }

        $model = new FormIgnoreDelete($user, $targetId);
        if ($model->send() && $model->validate()) {
            $model->make();
            $this->response->redirect(Url::to('profile/ignore'));
        }

        return $this->view->render('unblock', [
            'model' => $model
        ]);
    }

    /**
     * Cron schedule - build user profiles sitemap
     */
    public static function buildSitemapSchedule()
    {
        // get not empty user profiles
        $profiles = ProfileRecords::whereNotNull('nick');
        if ($profiles->count() < 1) {
            return;
        }

        // get languages if multilanguage enabled
        $langs = null;
        if (App::$Properties->get('multiLanguage')) {
            $langs = App::$Properties->get('languages');
        }

        // build sitemap from content items via business model
        $sitemap = new EntityBuildMap($langs);
        foreach ($profiles->get() as $user) {
            $sitemap->add('profile/show/' . $user->user_id, $user->updated_at, 'weekly', 0.2);
        }

        try {
            $sitemap->save('profile');
        } catch (SyntaxException $e){}
    }

    /**
     * Cleanup tables as scheduled action
     */
    public static function cleanupTablesSchedule()
    {
        // calculate date (now - 1week) for sql query
        $date = (new \DateTime('now'))->modify('-1 week')->format('Y-m-d');
        try {
            UserNotification::where('created_at', '<=', $date)->delete();
            UserLog::where('created_at', '<=', $date)->delete();
        } catch (\Exception $e) {}
    }
}
