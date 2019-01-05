<?php

namespace Apps\Model\Admin\Profile;

use Ffcms\Core\Arch\Model;

/**
 * Class FormSettings. Admin profile settings business logic
 * @package Apps\Model\Admin\Profile
 */
class FormSettings extends Model
{
    public $guestView;
    public $wallPostOnPage;
    public $wallPostOnFeed;
    public $delayBetweenPost;
    public $rating;
    public $usersOnPage;
    public $ratingDelay;

    private $_configs;

    /**
     * Construct model with default values
     * @param array|null $configs
     */
    public function __construct(array $configs = null)
    {
        $this->_configs = $configs;
        parent::__construct();
    }

    public function before()
    {
        if ($this->_configs === null) {
            return;
        }

        foreach ($this->_configs as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'guestView' => __('Guest view'),
            'wallPostOnPage' => __('Posts in profile'),
            'wallPostOnFeed' => __('Posts on feed'),
            'delayBetweenPost' => __('Post delay'),
            'rating' => __('Rating'),
            'usersOnPage' => __('User per page'),
            'ratingDelay' => __('Rating delay')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['guestView', 'wallPostOnPage', 'delayBetweenPost', 'rating', 'usersOnPage', 'ratingDelay', 'wallPostOnFeed'], 'required'],
            [['wallPostOnPage', 'delayBetweenPost', 'usersOnPage', 'ratingDelay', 'wallPostOnFeed'], 'int'],
            [['guestView', 'rating'], 'boolean']
        ];
    }
}
