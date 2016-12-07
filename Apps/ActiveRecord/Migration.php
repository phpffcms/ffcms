<?php

namespace Apps\ActiveRecord;


use Ffcms\Core\Arch\ActiveModel;

/**
 * Class Migration. Active record object for table prefix_migrations
 * @package Apps\ActiveRecord
 * @property int $id
 * @property string $migration
 * @property string $created_at
 * @property string $updated_at
 */
class Migration extends ActiveModel
{
    protected $casts = [
        'id' => 'integer',
        'migration' => 'string'
    ];
}