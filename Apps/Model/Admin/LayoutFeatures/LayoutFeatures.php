<?php

namespace Apps\Model\Admin\LayoutFeatures;

use Apps\ActiveRecord\CommentPost;
use Apps\ActiveRecord\FeedbackPost;
use Ffcms\Core\Arch\Model;
use Illuminate\Support\Collection;

/**
 * Class LayoutFeatures. Basic admin layout features for last feedback queries, comments, etc
 * @package Apps\Model\Admin\LayoutFeatures
 */
class LayoutFeatures extends Model
{
    const LIMIT = 5;

    private $_feedback;
    private $_comments;
    private $_content;

    /**
     * Process all database query before init
     */
    public function before()
    {
        $this->_feedback = FeedbackPost::with('user')
            ->where('closed', false)
            ->where('readed', false)
            ->orderBy('id', 'DESC')
            ->take(static::LIMIT)
            ->get();

        $this->_comments = CommentPost::with('user')
            ->orderBy('id', 'DESC')
            ->take(static::LIMIT)
            ->get();

        parent::before();
    }

    /**
     * Get feedback post array
     * @return FeedbackPost[]|Collection
     */
    public function getFeedback()
    {
        return $this->_feedback;
    }

    /**
     * Get comment post array
     * @return CommentPost|Collection
     */
    public function getComments()
    {
        return $this->_comments;
    }
}
