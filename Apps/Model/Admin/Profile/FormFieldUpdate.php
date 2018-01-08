<?php

namespace Apps\Model\Admin\Profile;

use Apps\ActiveRecord\ProfileField;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;

/**
 * Class FormFieldUpdate. Update additional field business logic model
 * @package Apps\Model\Admin\Profile
 */
class FormFieldUpdate extends Model
{
    public $type;
    public $name;
    public $reg_exp;
    public $reg_cond;

    private $_record;

    /**
     * FormFieldUpdate constructor. Pass profile field record inside
     * @param ProfileField $record
     */
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
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Forum display labels
     * @return array
     */
    public function labels(): array
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
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['type', 'reg_exp', 'reg_cond'], 'required'],
            ['reg_cond', 'in', [0, 1]]
        ];

        foreach (App::$Properties->get('languages') as $lang) {
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
        $this->_record->name = $this->name;
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
