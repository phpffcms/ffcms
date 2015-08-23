<?php

namespace Apps\Model\Front\Profile;

use Apps\ActiveRecord\ProfileField;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\Serialize;
use Ffcms\Core\Interfaces\iUser;

class FormSettings extends Model
{
    public $nick;
    public $sex;
    public $birthday;
    public $city;
    public $hobby;
    public $phone;
    public $url;

    public $custom_data = [];

    private $_user;

    public function __construct(iUser $user)
    {
        $this->_user = $user;
        parent::__construct();
    }

    /**
    * Set default data
    */
    public function before()
    {
        $profile = $this->_user->getProfile()->toArray(); // object to array (property's is protected of access)
        foreach ($profile as $property => $value) {
            // if property exist - lets pass data to model
            if (property_exists($this, $property)) {
                if ($property === 'birthday' && null !== $value) {
                    $this->birthday = Date::convertToDatetime($value, Date::FORMAT_TO_DAY);
                    continue;
                }
                if ($property === 'custom_data') {
                    $this->custom_data = Serialize::decode($value);
                    continue;
                }
                $this->$property = $value;
            }
        }
    }

    /**
    * Labels
    */
    public function labels()
    {
        $labels =  [
            'nick' => __('Nickname'),
            'sex' => __('Sex'),
            'birthday' => __('Birthday'),
            'city' => __('City'),
            'hobby' => __('Hobby'),
            'phone' => __('Phone'),
            'url' => __('Website')
        ];

        // labels for custom fields
        foreach (ProfileField::getAll() as $custom) {
            $labels['custom_data.' . $custom->id] = Serialize::getDecodeLocale($custom->name);
        }

        return $labels;
    }

    /**
    * Rules for validation
    */
    public function rules()
    {
        $rules = [
            ['sex', 'required'],
            [['city', 'hobby', 'phone', 'url', 'nick', 'birthday'], 'used'],
            ['nick', 'length_max', '50'],
            ['city', 'length_max', '50'],
            ['sex', 'in', ['0', '1', '2']],
            ['hobby', 'length_max', '50'],
            ['phone', 'phone'],
            ['url', 'url']
        ];

        // custom profile fields
        foreach (ProfileField::getAll() as $custom) {
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
        $profile = $this->_user->getProfile();

        $profile->nick = App::$Security->strip_tags($this->nick);
        $profile->sex = $this->sex;
        $newBirthday = Date::convertToDatetime($this->birthday, Date::FORMAT_SQL_DATE);
        if (false !== $newBirthday) {
            $profile->birthday = $newBirthday;
        }
        $profile->city = App::$Security->strip_tags($this->city);
        $profile->hobby = App::$Security->strip_tags($this->hobby);
        $profile->phone = $this->phone;
        $profile->url = App::$Security->strip_tags($this->url);
        $profile->custom_data = Serialize::encode(App::$Security->strip_tags($this->custom_data));

        $profile->save();
    }

}