<?php

/** @var \Ffcms\Templex\Template\Template $this */
/** @var \Apps\ActiveRecord\WallPost[]|\Illuminate\Support\Collection $records */
/** @var array $pagination */

use Ffcms\Core\Helper\Date;
use Ffcms\Templex\Url\Url;

$this->layout('_layouts/default', [
    'title' => __('Profile stream'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->getId()]) => __('Profile'),
        __('Profile stream')
    ]
]);

?>
<?php $this->start('body') ?>

<h1><?= __('Profile stream') ?></h1>
<hr />
<?php if ($records->count() < 1): ?>
    <p class="alert alert-warning"><?= __('No user wall post found yet') ?></p>
<?php endif; ?>
<?php foreach ($records as $post): ?>
    <div class="row object-lightborder ml-1" id="wall-post-<?= $post->id ?>">
        <div class="col-xs-4 col-md-2">
            <div class="text-center">
                <?= Url::a(['profile/show', [$post->sender_id]], $post->senderUser->profile->getName(), ['style' => 'color: ' . $post->senderUser->role->color]) ?>
                <img class="img-fluid img-rounded" alt="Avatar of <?= $post->senderUser->profile->getName() ?>" src="<?= $post->senderUser->profile->getAvatarUrl('small') ?>" />
                <div class="text-muted"><?= Date::humanize($post->updated_at); ?></div>
            </div>
        </div>
        <div class="col-xs-8 col-md-10">
            <div class="object-text">
                <?= $post->message ?>
            </div>
            <hr style="margin: 5px;" />
            <div><i class="fas fa-comment"></i>
                <a href="#wall-post-<?= $post->id ?>" id="wall-post-response-<?= $post->id ?>" class="show-wall-response">
                    <?= __('Answers') ?> (<span id="wall-post-response-count-<?= $post->id ?>">0</span>)
                </a>
            </div>
            <div id="wall-answer-dom-<?= $post->id; ?>" class="d-none"></div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->bootstrap()->pagination(['profile/feed'], ['class' => 'pagination justify-content-center'])
    ->size($pagination['total'], $pagination['page'], $pagination['step'])
    ->display() ?>

<!-- list answers and add answer dom elements -->
<div id="show-answer-list" class="d-none">
    <div class="row wall-answer">
        <div class="col-md-2 col-xs-4"><img id="wall-answer-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" alt="avatar" class="img-fluid img-rounded avatar" /></div>
        <div class="col-md-10 col-xs-8">
            <div class="answer-header">
                <a href="<?= \App::$Alias->baseUrl ?>/profile/index" id="wall-answer-userlink">unknown</a>
                <small class="float-end"><span id="wall-answer-date">01.01.1970</span>
                    <a href="javascript:void(0)" class="delete-answer d-none" id="delete-answer"><i class="fas fa-trash-alt"></i></a>
                </small>
            </div>
            <div id="wall-answer-text"></div>
        </div>
    </div>
</div>
<div id="add-answer-field" class="d-none">
    <hr style="margin: 5px;"/>
    <input type="text" id="make-answer" placeHolder="<?= __('Write comment') ?>" class="form-control wall-answer-text" maxlength="200"/>
    <a style="margin-top: 5px;" href="#wall-post" class="send-wall-answer btn btn-primary btn-sm" id="send-wall">
        <?= __('Send') ?>
    </a>
    <span class="float-end" id="answer-counter">200</span>
</div>

<script>
    var hideAnswers = [];
    $(document).ready(function () {
        var elements = $('.object-lightborder');
        var viewer_id = 0;
        var is_self_profile = false;
        <?php if (\App::$User->isAuth()): ?>
        viewer_id = <?= \App::$User->identity()->getId() ?>;
        <?php endif; ?>
        var postIds = [];
        $.each(elements, function (key, val) {
            postIds.push(val.id.replace('wall-post-', ''));
        });
        // load answers count via JSON
        if (postIds.length > 0) {
            $.getJSON(script_url + '/api/profile/wallanswercount/' + postIds.join(',') + '?lang=' + script_lang, function (json) {
                // data is successful loaded, lets parse it and set to exist dom elements as text value
                if (json.status === 1) {
                    $.each(json.data, function (key, val) {
                        $('#wall-post-response-count-' + key).text(val);
                    });
                }
            });
        }
        // load answers via JSON and add to current DOM
        loadAnswers = function (postId) {
            $.getJSON(script_url + '/api/profile/showwallanswers/' + postId + '?lang=' + script_lang, function (json) {
                if (json.status !== 1) {
                    return null;
                }
                var answerField = $('#add-answer-field').clone();
                var answerDom = $('#show-answer-list').clone();
                answerField.removeAttr('id').removeClass('d-none');
                answerDom.removeAttr('id').removeClass('d-none');
                // add hidden div with wall post object id
                answerField.prepend($('<div></div>').attr('id', 'send-wall-object-' + postId));
                // set make answer wall post object id
                answerField.find('#make-answer').attr('id', 'make-answer-' + postId);
                // build send submit button - set id and href to wall post object anchor
                answerField.find('#send-wall').attr('id', 'send-wall-' + postId).attr('href', '#wall-post-' + postId);
                // build counter (max chars in input = 200)
                answerField.find('#answer-counter').attr('id', 'answer-counter-' + postId);
                var addAnswerField = '';
                if (viewer_id > 0) {
                    addAnswerField = answerField.html();
                }
                var answers = '';
                $.each(json.data, function (idx, row) {
                    // clone general dom element
                    var dom = answerDom.clone();
                    // set avatar src
                    dom.find('#wall-answer-avatar')
                        .attr('src', row.user_avatar)
                        .removeAttr('id');
                    // set user link
                    dom.find('#wall-answer-userlink')
                        .attr('href', '<?= Url::to('profile/show') ?>/' + row.user_id).text(row.user_name)
                        .attr('style', 'color: '+row.user_color)
                        .removeAttr('id');
                    // set date
                    dom.find('#wall-answer-date').text(row.answer_date).removeAttr('id');
                    // set message text
                    dom.find('#wall-answer-text').text(row.answer_message);
                    // check if this user can remove answers - answer writer or target user profile
                    if (is_self_profile || row.user_id === viewer_id) {
                        dom.find('#delete-answer')
                            .attr('href', '#send-wall-object-' + postId)
                            .attr('id', 'delete-answer-' + row.answer_id + '-' + postId)
                            .removeClass('d-none');
                    }
                    answers += dom.html();
                });
                $('#wall-answer-dom-' + postId).html(addAnswerField + answers);
            })
        };
        addAnswer = function (postId, message) {
            $.post(script_url + '/api/profile/sendwallanswer/' + postId + '?lang=' + script_lang, {message: message}, function (response) {
                if (response.status === 1) {
                    loadAnswers(postId);
                } else {
                    $('#send-wall-object-' + postId).html('<p class="alert alert-warning"><?= __('Comment send was failed! Try to send it later.') ?></p>');
                }
            }, 'json');
        };
        // if clicked on "Answers" - show it and send form
        $('.show-wall-response').on('click', function () {
            var postId = this.id.replace('wall-post-response-', '');
            // control hide-display on clicking to "Answers" link
            if (hideAnswers[postId] === true) {
                hideAnswers[postId] = false;
                $('#wall-answer-dom-' + postId).addClass('d-none');
                return null;
            } else {
                hideAnswers[postId] = true;
                $('#wall-answer-dom-' + postId).removeClass('d-none');
            }
            // load data and set html
            loadAnswers(postId);
        });
        // calc entered symbols
        $(document).on('keyup', '.wall-answer-text', function () {
            var postId = this.id.replace('make-answer-', '');
            var msglimit = 200;
            var msglength = $(this).val().length;
            var limitObject = $('#answer-counter-' + postId);
            if (msglength >= msglimit) {
                limitObject.html('<span class="badge badge-danger">0</span>');
            } else {
                limitObject.text(msglimit - msglength);
            }
        });
        // delegate live event simple for add-ed dom element
        $(document).on('click', '.send-wall-answer', function () {
            var answerToId = this.id.replace('send-wall-', '');
            var message = $('#make-answer-' + answerToId).val();
            if (message == null || message.length < 3) {
                alert('<?= __('Message is too short') ?>');
                return null;
            }
            addAnswer(answerToId, message);
        });
    });
</script>

<?php $this->stop() ?>