<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;

/**
 * Class UserRecovery. Active record model for user recoveries requests
 * @package Apps\ActiveRecord
 * @property int $id
 * @property int $user_id
 * @property string $password
 * @property string $token
 * @property int $archive
 * @property string $created_at
 * @property string $updated_at
 */
class UserRecovery extends ActiveModel
{

}