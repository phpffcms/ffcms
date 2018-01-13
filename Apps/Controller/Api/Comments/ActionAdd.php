<?php

namespace Apps\Controller\Api\Comments;

use Apps\Model\Api\Comments\CommentAnswerAdd;
use Apps\Model\Api\Comments\CommentPostAdd;
use Apps\Model\Api\Comments\EntityCommentData;
use Ffcms\Core\App;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;
use Apps\ActiveRecord\App as AppRecord;

/**
 * Trait ActionAdd
 * @package Apps\Controller\Api\Comments
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader()
 */
trait ActionAdd
{
    /**
     * Add comment or answer via ajax.
     * @return string
     * @throws NativeException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function add(): ?string
    {
        $this->setJsonHeader();
        $configs = AppRecord::getConfigs('widget', 'Comments');

        $replayTo = (int)$this->request->request->get('replay-to');
        $model = null;
        // check if its a answer (comment answer type)
        if ($replayTo > 0) {
            $model = new CommentAnswerAdd($configs);
            $model->replayTo = $replayTo;
        } else { // sounds like new comment row
            $model = new CommentPostAdd($configs);
            $model->pathway = App::$Security->strip_tags($this->request->request->get('pathway'));
        }

        // pass general comment params to model
        $model->message = App::$Security->secureHtml((string)$this->request->request->get('message'));
        $model->guestName = App::$Security->strip_tags($this->request->request->get('guest-name'));

        // check model conditions before add new row
        if ($model === null || !$model->check()) {
            throw new NativeException('Unknown error');
        }

        // add comment post or answer to database and get response active record row
        $record = $model->buildRecord();
        // pass row to entity builder model
        $response = new EntityCommentData($record);

        return json_encode([
            'status' => 1,
            'data' => $response->make() // build row to standard format
        ]);
    }
}
