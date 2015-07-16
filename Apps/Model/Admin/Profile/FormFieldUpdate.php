<?php

namespace Apps\Model\Admin\Profile;

use Apps\ActiveRecord\ProfileField;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Serialize;

class FormFieldUpdate extends Model
{
    public $type;
    public $name;
    public $reg_exp;
    public $reg_cond;

    private $_record;

    public function __construct(ProfileField $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    /**
    * Set defaults values
    */
    public function before()
    {
        foreach ($this->_record->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                if ($property === 'name') {
                    $this->name = Serialize::decode($value);
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
        return [
            'name' => __('Title'),
            'type' => __('Type'),
            'reg_exp' => __('Validation regexp'),
            'reg_cond' => __('Validation direction')
        ];
    }

    /**
    * Validation rules
    */
    public function rules()
    {
        $rules = [
            [['type', 'reg_exp', 'reg_cond'], 'required'],
            ['reg_cond', 'in', ['0', '1']]
        ];

        foreach (App::$Property->get('languages') as $lang) {
            $rules[] = ['name.' . $lang, 'required'];
            $rules[] = ['name.' . $lang, 'length_max', 50];
        }

        return $rules;
    }

    /**
     * Save data to db
     */
    public function save()
    {
        $this->_record->name = Serialize::encode($this->name);
        $this->_record->reg_exp = $this->reg_exp;
        $this->_record->reg_cond = $this->reg_cond;
        $this->_record->type = $this->type;
        $this->_record->save();
    }

    /**
     * Delete record from db
     * @throws \Exception
     */
    public function delete()
    {
       $this->_record->delete();
    }
}