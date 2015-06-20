<?php
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Object;
use Ffcms\Core\Helper\String;
use Ffcms\Core\Helper\Url;

/** @var $user Apps\Model\Basic\User */
/** @var $viewer Apps\Model\Basic\User */
/** @var $wall Apps\ActiveRecord\WallPost|null */
/** @var $notify array|null */
/** @var $wallRecords object */
/** @var $pagination Ffcms\Core\Helper\HTML\SimplePagination */
/** @var $isSelf bool */

// $user is a target profile depended object(not current user!!!)

$name = \App::$Security->strip_tags($user->getProfile()->nick);

if ($name == null || String::length($name) < 1) {
    $name = __('No name');
}

$this->title = __('Profile') . ': ' . $name;

$this->breadcrumbs = [
    Url::to('/') => __('Home'),
    $this->title
];

?>
<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="col-md-12">
        <h1><?= $name ?> <sup><small>id: <?= $user->id; ?></small></sup></h1>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-4">
        <img src="<?= $user->getProfile()->getAvatarUrl('big') ?>" class="img-responsive center-block img-rounded" />
        <?php
        $userMenu = null;
        if (true === $isSelf) {
            $userMenu = [
                ['type' => 'link', 'link' => ['profile/avatar'], 'text' => '<i class="fa fa-camera"></i> ' . __('Avatar'), 'html' => true],
                ['type' => 'link', 'link' => ['profile/messages'], 'text' => '<i class="fa fa-envelope"> ' . __('Messages') . ' <span class="badge pm-count-block">0</span>', 'html' => true],
                ['type' => 'link', 'link' => ['profile/settings'], 'text' => '<i class="fa fa-cogs"></i> ' . __('Settings'), 'html' => true]
            ];
        } elseif (\App::$User->isAuth()) {
            $userMenu = [
                ['type' => 'link', 'link' => Url::to('profile/messages', null, null, ['newdialog' => $user->id]), 'text' => '<i class="fa fa-pencil-square-o"></i> ' . __('Write message'), 'html' => true]
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
        <h2><?= __('Profile data'); ?></h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td><?= __('Join date'); ?></td>
                    <td><?= Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY); ?></td>
                </tr>
                <?php if ($user->getProfile()->birthday !== null && !String::startsWith('0000-', $user->getProfile()->birthday)): ?>
                <tr>
                    <td><?= __('Birthday'); ?></td>
                    <td>
                        <?= Url::link(
                            ['profile/index', 'born', Date::convertToDatetime($user->getProfile()->birthday, 'Y')],
                            Date::convertToDatetime($user->getProfile()->birthday, Date::FORMAT_TO_DAY)
                            ) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php $sex = $user->getProfile()->sex ?>
                <tr>
                    <td><?= __('Sex'); ?></td>
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
                <?php if ($user->getProfile()->phone !== null && String::length($user->getProfile()->phone) > 0): ?>
                <tr>
                    <td><?= __('Phone'); ?></td>
                    <td><?= \App::$Security->strip_tags($user->getProfile()->phone); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($user->getProfile()->url !== null): ?>
                <tr>
                    <td><?= __('Website'); ?></td>
                    <td>
                        <a rel="nofollow" target="_blank" href="<?= \App::$Security->strip_tags($user->getProfile()->url); ?>"><?= __('Visit'); ?></a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($user->getProfile()->city !== null):
                    $city = \App::$Security->strip_tags($user->getProfile()->city);
                ?>
                <tr>
                    <td><?= __('City') ?></td>
                    <td><?= Url::link(['profile/index', 'city', trim($city, ' ')], $city) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($user->getProfile()->hobby !== null): ?>
                <tr>
                    <td><?= __('Interests'); ?></td>
                    <td>
                        <?php
                        $hobbyArray = explode(',', $user->getProfile()->hobby);
                        foreach ($hobbyArray as $item) {
                            $item = \App::$Security->strip_tags($item);
                            if ($item !== null && String::length($item) > 1) {
                                echo Url::link(['profile/index', 'hobby', trim($item, ' ')], $item, ['class' => 'label label-success']) . ' ';
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
                $referNickname = ($referObject->getProfile()->nick == null ? __('No name') : \App::$Security->strip_tags($referObject->getProfile()->nick));
                ?>
                <div class="row wall-post" id="wall-post-<?= $post->id ?>">
                    <div class="col-md-2">
                        <div class="text-center"><img class="img-responsive img-rounded" alt="Avatar of <?= $referNickname ?>"
                             src="<?= $referObject->getProfile()->getAvatarUrl('small') ?>" />
                        </div>
                    </div>
                    <div class="col-md-10">
                        <h5 style="margin-top: 0;">
                            <i class="fa fa-pencil"></i> <?= Url::link(['profile/show', $post->sender_id], $referNickname) ?>
                            <small class="pull-right"><?= Date::convertToDatetime($post->updated_at, Date::FORMAT_TO_SECONDS); ?></small>
                        </h5>
                        <div class="wall-post-text">
                            <?= \App::$Security->strip_tags($post->message); ?>
                        </div>
                        <hr style="margin: 5px;" />
                        <div><i class="fa fa-comment-o"></i>
                            <a href="#wall-post-<?= $post->id ?>" id="wall-post-response-<?= $post->id ?>" class="show-wall-response">
                                <?= __('Answers') ?> (<span id="wall-post-response-count-<?= $post->id ?>">0</span>)
                            </a>
                            <?php if ($post->target_id === $viewer->id || $post->sender_id === $viewer->id): ?>
                                <?= Url::link(['profile/walldelete', $post->id], __('Delete'), ['class' => 'pull-right']) ?>
                            <?php endif; ?>
                        </div>
                        <div id="wall-answer-dom-<?= $post->id; ?>" class="hidden"></div>
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
<script>
    var hideAnswers = [];
    window.jQ.push(function(){
        $(function(){
            var elements = $('.wall-post');
            var viewer_id = 0;
            var is_self_profile = <?= $isSelf === true ? 'true' : 'false' ?>;
            <?php if (\App::$User->isAuth()): ?>
            viewer_id = <?= $viewer->getId() ?>;
            <?php endif; ?>
            var postIds = [];
            $.each(elements, function(key, val) {
                postIds.push(val.id.replace('wall-post-', ''));
            });
            if (postIds.length < 1) {
                return null;
            }

            // load info about answers
            $.getJSON(script_url+'/api/profile/wallanswercount/' + postIds.join(',') + '?lang='+script_lang, function (json) {
                // data is successful loaded, pharse
                if (json.status === 1) {
                    $.each(json.data, function(key, val){
                        $('#wall-post-response-count-'+key).text(val);
                    });
                }
            });

            $.fn.loadAnswers = function(postId) {
                $.getJSON(script_url+'/api/profile/showwallanswers/' + postId +'?lang='+script_lang, function (json) {
                    if (json.status !== 1) {
                        return null;
                    }
                    var htmlAnswer = '<hr style="margin: 5px;" />';
                    htmlAnswer += '<div class="well">';
                    htmlAnswer += '<div id="send-wall-object-'+postId+'"></div>';
                    htmlAnswer += '<input type="text" id="make-answer-'+postId+'" placeHolder="<?= __('Write comment') ?>" class="form-control wall-answer-text" maxlength="200" />';
                    htmlAnswer += '<a style="margin-top: 5px;" href="#wall-post-'+postId+'" class="send-wall-answer btn btn-primary btn-sm" id="send-wall-'+postId+'"><?= __('Send') ?></a>';
                    htmlAnswer += '<span class="pull-right" id="answer-counter-'+postId+'">200</span>';
                    htmlAnswer += "</div>";
                    $.each(json.data, function(idx, row){
                        htmlAnswer += '<div class="row wall-answer">';
                        htmlAnswer += '<div class="col-md-2"><img src="'+row.user_avatar+'" alt="avatar" class="img-responsive img-rounded" /></div>';
                        htmlAnswer += '<div class="col-md-10">';
                        htmlAnswer += '<div class="answer-header">';
                        htmlAnswer += '<a href="<?= \App::$Alias->baseUrl ?>/profile/show/'+row.user_id+'">'+row.user_nick+'</a>';
                        htmlAnswer += '<small class="pull-right">'+row.answer_date;
                        if (is_self_profile || row.user_id === viewer_id) {
                            htmlAnswer += '<a href="#send-wall-object-' + postId + '" class="delete-answer" id="delete-answer-' + row.answer_id + '-' + postId +'"><i class="fa fa-lg fa-times"></i></a>';
                        }
                        htmlAnswer += '</small>';
                        htmlAnswer += '</div>';
                        htmlAnswer += '<div>' + row.answer_message + '</div>';
                        htmlAnswer += '</div></div>';
                    });
                    $('#wall-answer-dom-'+postId).html(htmlAnswer);
                })
            };

            $.fn.addAnswer = function(postId, message) {
                $.post(script_url+'/api/profile/sendwallanswer/'+postId+'?lang='+script_lang, {message: message}, function(response){
                    if (response.status === 1) {
                        $.fn.loadAnswers(postId);
                    }
                }, 'json').done(function() {
                    return true;
                });
                return false;
            };


            // if clicked on "Answers" - show it and send form
            $('.show-wall-response').on('click', function(){
                var postId = this.id.replace('wall-post-response-', '');
                // control hide-display on clicking to "Answers" link
                if (hideAnswers[postId] === true) {
                    hideAnswers[postId] = false;
                    $('#wall-answer-dom-'+postId).addClass('hidden');
                    return null;
                } else {
                    hideAnswers[postId] = true;
                    $('#wall-answer-dom-'+postId).removeClass('hidden');
                }
                // load data and set html
                $.fn.loadAnswers(postId);
            });

            // calc entered symbols
            $(document).on('keyup', '.wall-answer-text', function() {
                var postId = this.id.replace('make-answer-', '');
                var msglimit = 200;
                var msglength = $(this).val().length;

                var limitObject = $('#answer-counter-' + postId);

                if (msglength >= msglimit) {
                    limitObject.html('<span class="label label-danger">0</span>');
                } else {
                    limitObject.text(msglimit-msglength);
                }
            });

            $(document).on('click', '.delete-answer', function(){
                var answerIdPostId = this.id.replace('delete-answer-', '').split('-');
                $.getJSON(script_url+'/api/profile/deleteanswerowner/'+answerIdPostId[0]+'?lang='+script_lang, function(response){
                    $.fn.loadAnswers(answerIdPostId[1]);
                });
            });

            // delegate live event simple for add-ed dom element
            $(document).on('click', '.send-wall-answer', function(){
                var answerToId = this.id.replace('send-wall-', '');
                var message = $('#make-answer-'+answerToId).val();
                if (message == null || message.length < 3) {
                    alert('Message is too short');
                    return null;
                }

                var result = $.fn.addAnswer(answerToId, message);
                // sending going wrong !
                if (false === result) {
                    $('#send-wall-object-'+answerToId).html('<p class="alert alert-warning"><?= __('Comment send was failed! Wait few moments') ?></p>');
                }
            });
        });
    });
</script>