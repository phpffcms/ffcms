<?php

namespace Apps\Model\Admin\Main;


use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;

class FormYandexToken extends Model
{
    public $token;
    public $expires;

    public $_name = 'yaTokenForm';

    private $_cfg;

    /**
     * FormYandexToken constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        $this->_cfg = $configs;
        parent::__construct(false);
    }

    /**
     * Display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'token' => 'Token',
            'expires' => 'Lifetime (secs)'
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            [['token', 'expires'], 'required'],
            ['token', 'length_min', 20],
            ['expires', 'int']
        ];
    }

    /**
     * Save configs
     */
    public function make()
    {
        $cfg = $this->_cfg;

        $cfg['oauth']['token'] = $this->token;
        $cfg['oauth']['expires'] = time() + (int)$this->expires;

        App::$Properties->writeConfig('Yandex', $cfg);
    }
}