<?php

namespace Apps\Controller\Api\Content;

use Apps\ActiveRecord\Content as ContentRecord;
use Apps\Model\Api\Content\ContentRatingChange;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionChangeRate
 * @package Apps\Controller\Api\Content
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionChangeRate
{
    /**
     * Change content item rating action
     * @param string $type
     * @param string $id
     * @throws NativeException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @return string
     */
    public function changeRate(string $type, string $id)
    {
        $this->setJsonHeader();

        // check input params
        if (!Arr::in($type, ['plus', 'minus']) || !Any::isInt($id)) {
            throw new NativeException('Bad conditions');
        }

        // get current user and check is authed
        $user = App::$User->identity();
        if ($user === null || !App::$User->isAuth()) {
            throw new ForbiddenException(__('Authorization is required!'));
        }

        // set ignored content id to rate in session
        $ignored = App::$Session->get('content.rate.ignore');
        $ignored[] = $id;
        App::$Session->set('content.rate.ignore', $ignored);

        // find content record
        $record = ContentRecord::find($id);
        if ($record === null || $record->count() < 1) {
            throw new NotFoundException(__('Content item is not founded'));
        }

        // check if author rate him-self content
        if ($record->author_id === $user->getId()) {
            throw new ForbiddenException(__('You can not rate your own content'));
        }

        // initialize model
        $model = new ContentRatingChange($record, $type, $user);
        // check if content items is already rated by this user
        if ($model->isAlreadyRated()) {
            throw new ForbiddenException(__('You have already rate this!'));
        }

        // make rate - add +1 to content rating and author rating
        $model->make();

        return json_encode([
            'status' => 1,
            'rating' => $model->getRating()
        ]);
    }
}
