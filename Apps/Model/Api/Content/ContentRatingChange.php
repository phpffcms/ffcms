<?php

namespace Apps\Model\Api\Content;

use Apps\ActiveRecord\Content;
use Apps\ActiveRecord\ContentRating;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Interfaces\iUser;

class ContentRatingChange extends Model
{
    private $_content;
    private $_type;
    private $_user;
    
    /**
     * ContentRatingChange constructor. Pass inside record, type and user object
     * @param Content $record
     * @param string $type
     * @param iUser $user
     */
    public function __construct($record, $type, $user)
    {
        $this->_content = $record;
        $this->_type = $type;
        $this->_user = $user;
        parent::__construct();
    }

    /**
     * Check if content item is always rated by current user
     * @return boolean
     */
    public function isAlreadyRated()
    {
        return $this->_content->ratings->where('user_id', '=', $this->_user->getId())->count() > 0;
    }
    
    /**
     * Get content item rating
     * @return int
     */
    public function getRating()
    {
        return $this->_content->rating;
    }
    
    /**
     * Make content rating change - save results to db.
     */
    public function make()
    {
        // insert this rate to db logs
        $contentRating = new ContentRating();
        $contentRating->content_id = $this->_content->id;
        $contentRating->user_id = $this->_user->getId();
        $contentRating->type = $this->_type;
        $contentRating->save();
        
        // set ignored content id to rate in session
        $ignored = App::$Session->get('content.rate.ignore');
        $ignored[] = $this->_content->id;
        App::$Session->set('content.rate.ignore', $ignored);
        
        // save rating changes to database
        switch ($this->_type) {
            case 'plus':
                $this->_content->rating += 1;
                break;
            case 'minus':
                $this->_content->rating -= 1;
                break;
        }
        // save to db
        $this->_content->save();
        
        // update content author rating
        $authorId = (int)$this->_content->author_id;
        if ($authorId > 0 && App::$User->isExist($authorId)) {
            $authorObject = App::$User->identity($authorId);
            if ($authorObject !== null) {
                if ($this->_type === 'plus') {
                    $authorObject->profile->rating += 1;
                } else {
                    $authorObject->profile->rating -= 1;
                }
                $authorObject->profile->save();
            }
        }
        return true;
    }
}
