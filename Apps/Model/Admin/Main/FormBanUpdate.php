<?php

namespace Apps\Model\Admin\Main;

use Apps\ActiveRecord\Ban;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FormBanUpdate. Update ban form logic
 * @package Apps\Model\Admin\Profile
 */
class FormBanUpdate extends Model
{
    private $_record;

    public $ip;
    public $userId;
    public $read;
    public $write;
    public $expire;

    public $perma = false;

    /**
     * Construct method
     * @param Ban $record
     */
    public function __construct(Ban $record)
    {
        $this->_record = $record;
        parent::__construct();
    }

    public function before()
    {
        if ($this->_record->id) {
            $this->ip = $this->_record->ipv4;
            $this->userId = $this->_record->user_id;
            $this->read = $this->_record->ban_read;
            $this->write = $this->_record->ban_write;
            $this->expire = $this->_record->expired;

            if ($this->_record->expired === null) {
                $this->perma = true;
            }
        }
    }

    /**
     * Display labels
     * @return array 
     */
    public function labels(): array
    {
        return [
            'ip' => __('IP address'),
            'userId' => __("User id"),
            'read' => __('Read block'),
            'write' => __('Write block'),
            'expire' => __('Ban expires'),
            'perma' => __('Permanent')
        ];
    }

    /**
     * Validation rues
     * @return array 
     */
    public function rules(): array
    {
        return [
            [['ip', 'userId', 'read', 'write', 'expire'], 'used'],
            [['read', 'write'], 'boolean'],
            ['ip', 'length_max', 32],
            ['userId', 'int'],
            ['ip', 'ipv4']
        ];
    }

    /**
     * Save row
     * @return void 
     */
    public function save()
    {
        $this->_record->ipv4 = $this->ip;
        $this->_record->user_id = $this->userId;
        $this->_record->ban_read = (bool)$this->read;
        $this->_record->ban_write = (bool)$this->write;

        if (!$this->perma && $this->expire && Str::length($this->expire) > 3) {
            $this->_record->expired = $this->expire;
        }

        $this->_record->save();
    }


}