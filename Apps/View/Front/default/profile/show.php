<?php
use \Ffcms\Core\Helper\Date;
/** @var $user object */

if ($user->nick == null) {
    $user->nick = __('No name');
}
$this->title = __('Profile') . ': ' . \App::$Security->strip_tags($user->nick);
?>
<div class="row">
    <div class="col-md-12">
        <h1><?php echo \App::$Security->strip_tags($user->nick); ?> (id: <?php echo $user->id; ?>)</h1>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-4">
        <img src="<?php echo \App::$User->getAvatarUrl('big', $user->id); ?>" class="img-responsive" />
        <ul class="nav nav-pills nav-stacked">
            <li class="active"><a href="#">Settings</a></li>
            <li><a href="#">Billing</a></li>
        </ul>
    </div>
    <div class="col-md-8">
        <h2><?php echo __('Profile data'); ?></h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td><?php echo __('Join date'); ?></td>
                    <td><?php echo Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY); ?></td>
                </tr>
                <?php if (\App::$User->getCustomParam('birthday', $user->id) !== null): ?>
                <tr>
                    <td><?php echo __('Birthday'); ?></td>
                    <td><?php echo Date::convertToDatetime(\App::$User->getCustomParam('birthday', $user->id), Date::FORMAT_TO_DAY) ?></td>
                </tr>
                <?php endif; ?>
                <?php $sex = \App::$User->getCustomParam('sex', $user->id); ?>
                <tr>
                    <td><?php echo __('Sex'); ?></td>
                    <td>
                        <?php
                            if ($sex == 1) { // could be string(1) "1" or int(1) 1
                                echo __('Male');
                            } elseif ($sex == 2) {
                                echo __('Female');
                            } else {
                                echo __('Unknown');
                            }
                        ?>
                    </td>
                </tr>
                <?php if (\App::$User->getCustomParam('phone', $user->id) !== null): ?>
                <tr>
                    <td><?php echo __('Phone'); ?></td>
                    <td><?php echo \App::$Security->strip_tags(\App::$User->getCustomParam('phone'), $user->id); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (\App::$User->getCustomParam('weburl', $user->id) !== null): ?>
                <tr>
                    <td><?php echo __('Website'); ?></td>
                    <td><a rel="nofollow" href="<?php echo \App::$Security->strip_tags(\App::$User->getCustomParam('weburl', $user->id)); ?>"><?php echo __('Visit'); ?></a></td>
                </tr>
                <?php endif; ?>
                <?php if (\App::$User->getCustomParam('hobby', $user->id) !== null): ?>
                <tr>
                    <td><?php echo __('Interests'); ?></td>
                    <td><?php //todo: fixme - parse array of interests ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <h2>Wall</h2>
        <p>test</p>
    </div>
</div>