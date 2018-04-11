<?php

namespace Apps\Controller\Front\Profile;

use Ffcms\Core\Arch\View;
use Ffcms\Core\Exception\NotFoundException;
use Apps\ActiveRecord\Profile as ProfileRecords;
use Ffcms\Core\Helper\HTML\SimplePagination;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionIndex. Index action in Profile controller.
 * @package Apps\Controller\Front\Profile
 * @property Response $response
 * @property View $view
 */
trait ActionIndex
{
    /**
     * List user profiles on website by defined filter
     * @param string $name
     * @param null|string|int $value
     * @return string
     * @throws NotFoundException
     */
    public function index(string $name, ?string $value = null): ?string
    {
        $records = null;
        // set current page and offset
        $page = (int)$this->request->query->get('page', 0);
        $cfgs = $this->application->configs;
        $userPerPage = (int)$cfgs['usersOnPage'];
        if ($userPerPage < 1) {
            $userPerPage = 1;
        }
        $offset = $page * $userPerPage;

        switch ($name) {
            case 'rating': // rating list, order by rating DESC
                // check if rating is enabled
                if ((int)$cfgs['rating'] !== 1) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->orderBy('rating', 'DESC');
                break;
            case 'hobby': // search by hobby
                if (Str::likeEmpty($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('hobby', 'like', '%' . $value . '%');
                break;
            case 'city':
                if (Str::likeEmpty($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('city', $value);
                break;
            case 'born':
                if ($value === null || !Any::isInt($value)) {
                    throw new NotFoundException();
                }
                $records = (new ProfileRecords())->where('birthday', 'like', $value . '-%');
                break;
            case 'all':
                $records = (new ProfileRecords())->orderBy('id', 'DESC');
                break;
            default:
                $this->response->redirect('profile/index/all');
                break;
        }

        // get total records count
        $totalCount = $records->count();

        // get profile list with relation for user and role tables in 1 query
        $profiles = $records->with(['user', 'user.role'])
            ->skip($offset)
            ->take($userPerPage)
            ->get();

        // render output view
        return $this->view->render('profile/index', [
            'records' => $profiles,
            'pagination' => [
                'page' => $page,
                'total' => $totalCount,
                'step' => $userPerPage
            ],
            'id' => $name,
            'add' => $value,
            'ratingOn' => (int)$cfgs['rating']
        ]);
    }
}
