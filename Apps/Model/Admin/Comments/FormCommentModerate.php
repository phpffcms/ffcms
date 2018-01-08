<?php

namespace Apps\Model\Admin\Comments;

use Apps\ActiveRecord\CommentAnswer;
use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\Model;

class FormCommentModerate extends Model
{
    private $_records;
    private $_type;

    /**
     * FormCommentModerate constructor. Pass active record and type of comment system inside.
     * @param CommentPost|CommentAnswer $records
     * @param string $type
     */
    public function __construct($records, $type)
    {
        $this->_records = $records;
        $this->_type = $type;
        parent::__construct();
    }

    /**
     * Accept comments and answers
     */
    public function make()
    {
        $this->_records->update(['moderate' => 0]);
    }

    /**
     * Get moderated records
     * @return object
     */
    public function getRecord()
    {
        return $this->_records->get();
    }
}
