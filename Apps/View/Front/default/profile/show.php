<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

/** @var $user object */
/** @var $wall Apps\Model\Front\WallPost|null */
/** @var $notify array|null */
/** @var $wallRecords object */
/** @var $pagination Ffcms\Core\Helper\HTML\SimplePagination */
/** @var $isSelf bool */

// $user is a target profile depended object(not current user!!!)

$name = \App::$Security->strip_tags($user->nick);

if ($name == null || String::length($name) < 1) {
    $name = __('No name');
}

$this->title = __('Profile') . ': ' . $name;

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    $this->title
];
?>
<div class="row">
    <div class="col-md-12">
        <h1><?= $name ?> <sup><small>id: <?php echo $user->id; ?></small></sup></h1>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-4">
        <div class="text-centered"><img src="<?= $user->getAvatarUrl('big') ?>" class="img-responsive" /></div>
        <?php
        $userMenu = null;
        if (true === $isSelf) {
            $userMenu = [
                ['type' => 'link', 'link' => ['profile/avatar'], 'text' => '<i class="fa fa-camera"></i> ' . __('Avatar'), 'html' => true],
                ['type' => 'link', 'link' => ['profile/messagelist'], 'text' => '<i class="fa fa-envelope"> ' . __('Messages'), 'html' => true],
                ['type' => 'link', 'link' => ['profile/settings'], 'text' => '<i class="fa fa-cogs"></i> ' . __('Settings'), 'html' => true]
            ];
        } else {
            $userMenu = [
                ['type' => 'link', 'link' => ['profile/messagewrite', $user->id], 'text' => '<i class="fa fa-pencil-square-o"></i> ' . __('Write message'), 'html' => true]
            ];
        }
        ?>
        <?= Listing::display([
            'type' => 'ul',
            'property' => ['class' => 'nav nav-pills nav-stacked'],
            'items' => $userMenu
        ]) ?>
    </div>
    <div class="col-md-8">
        <h2><?php echo __('Profile data'); ?></h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td><?php echo __('Join date'); ?></td>
                    <td><?php echo Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY); ?></td>
                </tr>
                <?php if ($user->getCustomParam('birthday') !== null): ?>
                <tr>
                    <td><?php echo __('Birthday'); ?></td>
                    <td><?php echo Date::convertToDatetime($user->getCustomParam('birthday'), Date::FORMAT_TO_DAY) ?></td>
                </tr>
                <?php endif; ?>
                <?php $sex = $user->getCustomParam('sex'); ?>
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
                <?php if ($user->getCustomParam('phone') !== null): ?>
                <tr>
                    <td><?php echo __('Phone'); ?></td>
                    <td><?php echo \App::$Security->strip_tags($user->getCustomParam('phone')); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($user->getCustomParam('weburl') !== null): ?>
                <tr>
                    <td><?php echo __('Website'); ?></td>
                    <td><a rel="nofollow" href="<?php echo \App::$Security->strip_tags($user->getCustomParam('weburl')); ?>"><?php echo __('Visit'); ?></a></td>
                </tr>
                <?php endif; ?>
                <?php if ($user->getCustomParam('hobby') !== null): ?>
                <tr>
                    <td><?php echo __('Interests'); ?></td>
                    <td>
                        <?php
                        $hobbyArray = explode(',', $user->getCustomParam('hobby'));
                        foreach ($hobbyArray as $item) {
                            $item = \App::$Security->strip_tags($item);
                            if ($item !== null && \Ffcms\Core\Helper\String::length($item) > 1) {
                                echo \Ffcms\Core\Helper\Url::link(['profile/hobbylist', $item], $item) . ' ';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <h2><?= __('Wall') ?></h2>
        <?php if ($wall !== null): ?>
            <?php
            // show notification if exist
            if (Object::isArray($notify) && count($notify) > 0) {
                echo $this->show('macro/notify', ['notify' => $notify]);
            }
            ?>
            <?php $form = new \Ffcms\Core\Helper\HTML\Form(
                $wall,
                ['class' => 'form-horizontal', 'method' => 'post', 'action' => ''],
                ['base' => '<div class="form-group no-margin-bottom"><div class="col-md-12">%item% <p class="help-block">%help%</p></div></div>']
            ); ?>
            <?= $form->field('message', 'textarea', ['class' => 'form-control']); ?>
            <div class="text-right"><?= $form->submitButton(__('Send'), ['class' => 'btn btn-default']); ?></div>
            <?= $form->finish(); ?>
            <?php \App::$Alias->addPlainCode('js', "$('#" . $wall->getFormName() . "').on('change keyup keydown paste cut', 'textarea', function () { $(this).height(0).height(this.scrollHeight);}).find('textarea').change();") ?>
        <?php endif; ?>
        <?php
        if ($wallRecords !== null):
            foreach ($wallRecords as $post):
                /** @var $referObject object */
                $referObject = \App::$User->identity($post->sender_id);
                if ($referObject === null) { // caster not founded? skip ...
                    continue;
                }
                ?>
                <div class="row" style="padding-top: 10px">
                    <div class="col-md-12">
                        <div class="media" style="border: solid 1px #dbdbdb;padding: 10px;">
                            <a class="pull-left" href="http://ffcms.ru/ru/user/id1">
                                <img class="media-object img-responsive"
                                     src="<?= $referObject->getAvatarUrl('small') ?>" style="width:64px;height:64px;">
                            </a>

                            <div class="media-body">
                                <h5 class="media-heading">
                                    <?= Url::link(['profile/show', $post->sender_id],
                                        $referObject->get('nick', __('No name'))); ?>,
                                    <?= Date::convertToDatetime($post->updated_at, Date::FORMAT_TO_SECONDS); ?>
                                </h5>
                                <?php echo \App::$Security->strip_tags($post->message); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            endforeach;
        endif;
        ?>
        <div class="text-center">
            <?= $pagination->display(['class' => 'pagination pagination-centered']) ?>
        </div>
    </div>
</div>