<?php

namespace Apps\Model\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Templex\Url\Url;

/**
 * Class FormYandexConnect
 * @package Apps\Model\Admin\Main
 */
class FormYandexConnect extends Model
{
    public $callback;
    public $appid;

    private $_configs;

    /**
     * FormYandexConnect constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_configs = $configs;
        parent::__construct(false);
    }

    /**
     * Process before features
     */
    public function before()
    {
        $this->appid = $this->_configs['oauth']['app_id'];
        $this->callback = Url::to('main/yandextoken');
    }

    /**
     * Display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'callback' => 'Callback URI',
            'appid' => 'ID'
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['appid', 'required'],
            ['appid', 'length_min', 32]
        ];
    }

    /**
     * Make save configs
     */
    public function make()
    {
        $cfg = $this->_configs;
        $cfg['oauth']['app_id'] = $this->appid;

        App::$Properties->writeConfig('Yandex', $cfg);
    }
}