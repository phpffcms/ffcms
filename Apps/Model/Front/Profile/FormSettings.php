<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\ProfileField;
use Apps\ActiveRecord\User;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Interfaces\iUser;

/**
 * Class FormSettings. Business logic of user personal settings form
 * @package Apps\Model\Front\Profile
 */
class FormSettings extends Model
{
    public $name;
    public $sex;
    public $birthday;
    public $city;
    public $hobby;
    public $phone;
    public $url;

    public $custom_data = [];

    private $_user;

    /**
     * FormSettings constructor. Pass user object inside
     * @param iUser|User $user
     */
    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct(true);
    }

    /**
     * Set default data
     */
    public function before()
    {
        $profile = $this->_user->profile->toArray(); // object to array (property's is protected of access)
        foreach ($profile as $property => $value) {
            // if property exist - lets pass data to model
            if (property_exists($this, $property)) {
                if ($property === 'birthday') {
                    if (Str::likeEmpty($value)) {
                        $this->birthday = null;
                    } else {
                        $this->birthday = Date::convertToDatetime($value, Date::FORMAT_TO_DAY);
                    }
                    continue;
                }
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
        $labels = [
            'name' => __('Full name'),
            'sex' => __('Sex'),
            'birthday' => __('Birthday'),
            'city' => __('City'),
            'hobby' => __('Hobby'),
            'phone' => __('Phone'),
            'url' => __('Website')
        ];

        // labels for custom fields
        foreach (ProfileField::all() as $custom) {
            $labels['custom_data.' . $custom->id] = $custom->getLocaled('name');
        }

        return $labels;
    }

    /**
     * Rules for validation
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            ['sex', 'required'],
            [['city', 'hobby', 'phone', 'url', 'name', 'birthday'], 'used'],
            ['name', 'length_max', '70'],
            ['city', 'length_max', '50'],
            ['sex', 'in', [0, 1, 2]],
            ['hobby', 'length_max', '50'],
            ['phone', 'phone'],
            ['url', 'url']
        ];

        // custom profile fields
        foreach (ProfileField::all() as $custom) {
            $rules[] = [
                'custom_data.' . $custom->id,
                'used'
            ];
            $rules[] = [
                'custom_data.' . $custom->id,
                (int)$custom->reg_cond === 1 ? 'direct_match' : 'reverse_match',
                $custom->reg_exp
            ];
        }

        return $rules;
    }

    /**
     * Save data after validation
     */
    public function save()
    {
        $profile = $this->_user->profile;
        $profile->name = $this->name;
        $profile->sex = $this->sex;
        $profile->birthday = (Str::likeEmpty($this->birthday) ? null : Date::convertToDatetime($this->birthday, Date::FORMAT_SQL_DATE));
        $profile->city = $this->city;
        $profile->hobby = $this->hobby;
        $profile->phone = $this->phone;
        $profile->url = $this->url;
        $profile->custom_data = $this->custom_data;

        $profile->save();
    }
}
