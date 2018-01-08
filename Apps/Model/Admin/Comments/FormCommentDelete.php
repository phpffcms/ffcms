<?php

namespace Apps\Model\Admin\Comments;

use Apps\ActiveRecord\CommentPost;
use Ffcms\Core\Arch\Model;

/**
 * Class FormCommentDelete. Process comments and answers delete
 * @package Apps\Model\Admin\Comments
 */
class FormCommentDelete extends Model
{
    private $_record;
    private $_type;

    /**
     * FormCommentDelete constructor. Pass active record and type of comment system inside.
     * @param CommentPost[] $record
     * @param string $type
     */
    public function __construct($record, $type)
    {
        $this->_record = $record;
        $this->_type = $type;
        parent::__construct();
    }

    /**
     * Make delete items after submit
     */
    public function make()
    {
        // also delete all answers
        if ($this->_type === 'comment') {
            foreach ($this->getRecord() as $com) {
                /** @var CommentPost $com */
                $com->answers()->delete();
            }
        }

        $this->_record->delete();
    }

    /**
     * Get records to delete as object
     * @return object
     */
    public function getRecord()
    {
        return $this->_record->get();
    }
}
