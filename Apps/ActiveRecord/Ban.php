<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Ban table active record
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $ipv4
 * @property int|null $user_id
 * @property bool $ban_write
 * @property bool $ban_read
 * @property int|null $expired
 * @property string $created_at
 * @property string $updated_at
 */
class Ban extends ActiveModel
{
    protected $table = 'ban';

    protected $casts = [
        'id' => 'integer',
        'ban_write' => 'boolean',
        'ban_read' => 'boolean'
    ];

    /**
     * Check if ip or user_id is in active ban
     * @param string|null $ipv4 
     * @param int|null $userId 
     * @param bool $read
     * @param bool $write
     * @return self|null
     */
    public static function isBanned($ipv4 = null, $userId = null, $write = true, $read = false) 
    {
        if (!$ipv4 && !$userId) {
            return false;
        }

        $query = self::where(function($q) use ($ipv4, $userId) {
            if ($ipv4) {
                $q->where('ipv4', $ipv4);
            }

            if ($userId) {
                $q->orWhere('user_id', $userId);
            }
        })->where(function($q){
            $q->whereNull('expired')
                ->orWhere('expired', 0)
                ->orWhere('expired', '>=', time());
        })->where(function($q) use ($write, $read) {
            if ($write) {
                $q->where('ban_write', true);
            }

            if ($read) {
                $q->orWhere('ban_read', true);
            }
        });

        return $query->first();
    }
}