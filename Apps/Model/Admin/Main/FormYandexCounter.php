<?php

namespace Apps\Model\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;

/**
 * Class FormYandexCounter
 * @package Apps\Model\Admin\Main
 */
class FormYandexCounter extends Model
{
    public $counter;

    private $_counters;

    /**
     * FormYandexCounter constructor.
     * @param array $counters
     */
    public function __construct(array $counters)
    {
        $this->_counters = $counters;
        parent::__construct(false);
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            ['counter', 'required'],
            ['counter', 'int']
        ];
    }

    /**
     * Save counter id to configuration file
     */
    public function make()
    {
        $cfg = App::$Properties->getAll('Yandex');
        $cfg['metrika']['id'] = (int)$this->counter;

        App::$Properties->writeConfig('Yandex', $cfg);
    }

    /**
     * Get counter id->name
     * @return array
     */
    public function getCounters(): array
    {
        return $this->_counters;
    }
}