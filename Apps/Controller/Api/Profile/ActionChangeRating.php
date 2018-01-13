<?php


namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\ProfileRating;
use Ffcms\Core\App;
use Ffcms\Core\Exception\ForbiddenException;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Exception\NotFoundException;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionChangeRating
 * @package Apps\Controller\Api\Profile
 * @property Request $request
 * @property Response $response
 * @method void setJsonHeader
 */
trait ActionChangeRating
{
    /**
     * Change user rating action
     * @return string
     * @throws ForbiddenException
     * @throws NativeException
     * @throws NotFoundException
     * @throws \Ffcms\Core\Exception\SyntaxException
     */
    public function changeRating(): ?string
    {
        if (!App::$User->isAuth()) {
            throw new ForbiddenException('Auth required');
        }

        $this->setJsonHeader();

        // get operation type and target user id
        $targetId = (int)$this->request->get('target');
        $type = $this->request->get('type');

        // check type of query
        if ($type !== '+' && $type !== '-') {
            throw new NativeException('Wrong data');
        }

        // check if passed user id is exist
        if (!Any::isInt($targetId) || $targetId < 1 || !App::$User->isExist($targetId)) {
            throw new NotFoundException('Wrong user info');
        }

        $cfg = \Apps\ActiveRecord\App::getConfigs('app', 'Profile');
        // check if rating is enabled for website
        if (!(bool)$cfg['rating']) {
            throw new NativeException('Rating is disabled');
        }

        // get target and sender objects
        $target = App::$User->identity($targetId);
        $sender = App::$User->identity();

        // disable self-based changes ;)
        if ($target->getId() === $sender->getId()) {
            throw new ForbiddenException('Self change prevented');
        }

        // check delay
        $diff = Date::convertToDatetime(time() - $cfg['ratingDelay'], Date::FORMAT_SQL_TIMESTAMP);

        $query = ProfileRating::where('target_id', $target->getId())
            ->where('sender_id', $sender->getId())
            ->where('created_at', '>=', $diff)
            ->orderBy('id', 'DESC');
        if ($query->count() > 0) {
            throw new ForbiddenException('Delay required');
        }

        // delay is ok, lets insert a row
        $record = new ProfileRating();
        $record->target_id = $target->getId();
        $record->sender_id = $sender->getId();
        $record->type = $type;
        $record->save();

        if ($type === '+') {
            $target->profile->rating += 1;
        } else {
            $target->profile->rating -= 1;
        }
        $target->profile->save();

        return json_encode(['status' => 1, 'data' => 'ok']);
    }
}
