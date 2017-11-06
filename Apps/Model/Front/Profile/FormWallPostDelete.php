<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Arch\Model;

/**
 * Class FormWallPostDelete. Delete wall post business logic model
 * @package Apps\Model\Front\Profile
 */
class FormWallPostDelete extends Model
{
    public $id;

    /** @var WallPost */
    private $_post;

    /**
     * FormWallPostDelete constructor. Pass wall post active record object inside model
     * @param WallPost $post
     */
    public function __construct(WallPost $post)
    {
        $this->_post = $post;
        parent::__construct();
    }

    /**
     * Set post id based on post active record
     */
    public function before()
    {
        $this->id = $this->_post->id;
    }

    /**
     * Delete wall post object from db
     */
    public function make()
    {
        $this->_post->answers()->delete();
        $this->_post->delete();
    }


}