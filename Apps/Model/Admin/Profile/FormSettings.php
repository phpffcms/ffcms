<?php

namespace Apps\Model\Admin\Profile;

use Ffcms\Core\Arch\Model;

class FormSettings extends Model
{
    public $guestView;
    public $wallPostOnPage;
    public $delayBetweenPost;
    public $rating;
    public $usersOnPage;
    public $ratingDelay;

    /**
     * Construct model with default values
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }

        parent::__construct();
    }

    /**
    * Labels
    */
    public function labels()
    {
        return [
            'guestView' => __('Guest view'),
            'wallPostOnPage' => __('Post on page'),
            'delayBetweenPost' => __('Post delay'),
            'rating' => __('Rating'),
            'usersOnPage' => __('User per page'),
            'ratingDelay' => __('Rating delay')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        return [
            [['guestView', 'wallPostOnPage', 'delayBetweenPost', 'rating', 'usersOnPage', 'ratingDelay'], 'required'],
            [['guestView', 'wallPostOnPage', 'delayBetweenPost', 'rating', 'usersOnPage', 'ratingDelay'], 'int'],
            [['guestView', 'rating'], 'in', ['0', '1']]
        ];
    }
}