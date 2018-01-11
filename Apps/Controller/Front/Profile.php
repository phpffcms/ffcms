<?php

namespace Apps\Controller\Front;

use Apps\ActiveRecord\Profile as ProfileRecords;
use Apps\ActiveRecord\UserLog;
use Apps\ActiveRecord\UserNotification;
use Apps\Model\Front\Profile\FormSettings;
use Apps\Model\Front\Sitemap\EntityBuildMap;
use Extend\Core\Arch\FrontAppController;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\SyntaxException;

/**
 * Class Profile. User profiles application front controller
 * @package Apps\Controller\Front
 */
class Profile extends FrontAppController
{
    const BLOCK_PER_PAGE = 10;
    const EVENT_CHANGE_PASSWORD = 'profile.changepassword.success';
    const NOTIFY_PER_PAGE = 25;

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

    use Profile\ActionUnblock {
        unblock as actionUnblock;
    }

    use Profile\ActionPassword {
        password as actionPassword;
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
        } catch (SyntaxException $e) {
        }
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
        } catch (\Exception $e) {
        }
    }
}
