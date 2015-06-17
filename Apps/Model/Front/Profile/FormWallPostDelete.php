<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\WallPost;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\ForbiddenException;

class FormWallPostDelete extends Model
{
    public $id;

    private $_post;

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
    * Pseudo-rules, here is not required
    */
    public function rules()
    {
        return [];
    }

    public function make()
    {
        if ($this->id === null) {
            throw new ForbiddenException();
        }

        $this->_post->getAnswer()->delete();
        $this->_post->delete();
    }


}