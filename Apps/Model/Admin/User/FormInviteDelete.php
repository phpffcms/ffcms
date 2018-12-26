<?php

namespace Apps\Model\Admin\User;

use Apps\ActiveRecord\Invite;
use Ffcms\Core\Arch\Model;
use Illuminate\Support\Collection;

/**
 * Class FormInviteDelete. Process invite record delete
 * @package Apps\Model\Admin\User
 */
class FormInviteDelete extends Model
{
    /** @var Invite|Collection */
    private $_record;

    /**
     * FormInviteDelete constructor.
     * @param Invite|Collection $record
     */
    public function __construct($record)
    {
        $this->_record = $record;
        parent::__construct(true);
    }

    public function make()
    {
        $this->_record->delete();
    }
}
