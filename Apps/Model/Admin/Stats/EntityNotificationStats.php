<?php

namespace Apps\Model\Admin\Stats;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\Arch\Model;

/**
 * Class EntityNotificationStats. Short statistic for admin layout main
 * @package Apps\Model\Admin\Stats
 */
class EntityNotificationStats extends Model
{
    public $contents;
    public $feedback;
    public $comments;

    public $total;

    /**
     * Calculate notification statistics
     */
    public function before()
    {
        $this->calcContentsModerate();
        $this->calcNewFeedback();
        $this->calcCommentsModerate();

        $this->total = $this->contents + $this->feedback + $this->comments;
    }

    /**
     * Calculate content on moderation
     */
    private function calcContentsModerate()
    {
        $this->contents = Content::where('display', '=', 0)->count();
    }

    /**
     * Calculate unreaded feedback
     */
    private function calcNewFeedback()
    {
        $this->feedback = FeedbackPost::where('readed', '=', 0)->where('closed', '=', 0)->count();
    }

    /**
     * Calculate comments on moderation
     */
    private function calcCommentsModerate()
    {
        $this->comments = CommentPost::where('moderate', '=', 1)->count();
        $this->comments += CommentAnswer::where('moderate', '=', 1)->count();
    }
}
