<?php
use Apps\ActiveRecord\ProfileField;
use Ffcms\Core\Helper\Date;
use Ffcms\Core\Helper\HTML\Form;
use Ffcms\Core\Helper\HTML\Listing;
use Ffcms\Core\Helper\Simplify;
use Ffcms\Core\Helper\Type\Obj;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Helper\Url;

/** @var $user Apps\ActiveRecord\User */
/** @var $viewer Apps\ActiveRecord\User */
/** @var $wall Apps\ActiveRecord\WallPost|null */
/** @var $notify array|null */
/** @var $wallRecords object */
/** @var $pagination Ffcms\Core\Helper\HTML\SimplePagination */
/** @var $isSelf bool */
/** @var $ratingOn bool */
/** @var $this Ffcms\Core\Arch\View */

// $user is a target profile depended object(not current user!!!)

$name = $user->getProfile()->nick;

if (Str::likeEmpty($name)) {
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
        <h1><?= $name ?> <sup><small>id: <?= $user->id; ?></small></sup></h1>
    </div>
</div>
<hr/>
<?php if (\App::$User->isAuth() && $user->inBlacklist($viewer->getId())): ?>
<p class="alert alert-danger"><?= __('You are in blacklist of this user. Your access is limited.') ?></p>
<?php endif; ?>
<div class="row">
    <div class="col-md-4">
        <img src="<?= $user->getProfile()->getAvatarUrl('big') ?>" class="img-responsive center-block img-rounded" />
        <?php
        if ($ratingOn):
            $rateClass = 'btn-default';
            $rateValue = (int)$user->getProfile()->rating;
            if ($user->getProfile()->rating > 0) {
                $rateClass = 'btn-info';
            } elseif ($user->getProfile()->rating < 0) {
                $rateClass = 'btn-warning';
            }
        ?>
        <?php if ($isSelf): ?>
        <div class="row">
            <div class="col-md-12">
                <a href="javascript:void(0);" class="btn btn-block <?= $rateClass ?>">
                    <?= __('Rating') ?>: <span class="badge"><?= $rateValue ?></span>
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-8" style="padding-right: 0;">
                <a href="javascript:void(0);" class="btn btn-block <?= $rateClass ?>">
                    <?= __('Rating') ?>:
                    <span class="badge"><?= $rateValue > 0 ? '+' : null ?>
                        <span id="ratingValue"><?= $rateValue ?></span>
                    </span>
                </a>
            </div>
            <div class="col-md-2" style="padding-left: 1px;padding-right: 0;">
                <button id="addRating" class="btn btn-block btn-success">+</button>
            </div>
            <div class="col-md-2" style="padding-left: 1px; padding-right: 0;">
                <button class="btn btn-block btn-danger" id="reduceRating">-</button>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php
        $userMenu = null;
        if (true === $isSelf) {
            $userMenu = [
                ['type' => 'link', 'link' => ['profile/avatar'], 'text' => '<i class="glyphicon glyphicon-camera"></i> ' . __('Avatar'), 'html' => true],
                ['type' => 'link', 'link' => ['profile/messages'], 'text' => '<i class="glyphicon glyphicon-envelope"></i> ' . __('Messages') . ' <span class="badge pm-count-block">0</span>', 'html' => true],
                ['type' => 'link', 'link' => ['profile/settings'], 'text' => '<i class="glyphicon glyphicon-cog"></i> ' . __('Settings'), 'html' => true]
            ];
        } elseif (\App::$User->isAuth()) {
            $userMenu = [
                [
                    'type' => 'link', 'link' => Url::to('profile/messages', null, null, ['newdialog' => $user->id]),
                    'text' => '<i class="glyphicon glyphicon-envelope"></i> ' . __('Write message'), 'html' => true
                ],
                [
                    'type' => 'link', 'link' => Url::to('profile/ignore', null, null, ['id' => $user->id]),
                    'text' => '<i class="glyphicon glyphicon-ban-circle"></i> ' . __('Block'), 'html' => true, 'property' => ['class' => 'alert-danger']
                ]
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
                    <td><?= __('Group') ?></td>
                    <td><span class="label label-default" style="background-color: <?= $user->getRole()->color ?>;"><?= $user->getRole()->name ?></span></td>
                </tr>
                <tr>
                    <td><?= __('Join date'); ?></td>
                    <td><?= Date::convertToDatetime($user->created_at, Date::FORMAT_TO_DAY); ?></td>
                </tr>
                <?php if ($user->getProfile()->birthday !== null && !Str::startsWith('0000-', $user->getProfile()->birthday)): ?>
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
                <?php if (!Str::likeEmpty($user->getProfile()->phone)): ?>
                <tr>
                    <td><?= __('Phone'); ?></td>
                    <td><?= $user->getProfile()->phone ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!Str::likeEmpty($user->getProfile()->url)): ?>
                <tr>
                    <td><?= __('Website'); ?></td>
                    <td>
                        <a rel="nofollow" target="_blank" href="<?= $user->getProfile()->url ?>"><?= __('Visit'); ?></a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!Str::likeEmpty($user->getProfile()->city)):
                    $city = trim($user->getProfile()->city);
                ?>
                <tr>
                    <td><?= __('City') ?></td>
                    <td><?= Url::link(['profile/index', 'city', $city], $city) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!Str::likeEmpty($user->getProfile()->hobby)): ?>
                <tr>
                    <td><?= __('Interests'); ?></td>
                    <td>
                        <?php
                        $hobbyArray = explode(',', $user->getProfile()->hobby);
                        foreach ($hobbyArray as $item) {
                            $item = \App::$Security->strip_tags($item);
                            if (!Str::likeEmpty($item)) {
                                echo Url::link(['profile/index', 'hobby', trim($item, ' ')], $item, ['class' => 'label label-success']) . ' ';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php
                $custom_fields = $user->getProfile()->custom_data;
                if ($custom_fields !== null && Obj::isArray($custom_fields) && count($custom_fields) > 0): ?>
                    <?php foreach ($custom_fields as $cid => $value): ?>
                        <?php if (!Str::likeEmpty($value)): ?>
                            <tr>
                                <td><?= ProfileField::getNameById($cid) ?></td>
                                <td>
                                    <?php
                                    if (ProfileField::getTypeById($cid) === 'link') {
                                        echo Url::link($value, Str::sub($value, 30));
                                    } else {
                                        echo \App::$Security->strip_tags($value);
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
        <h2><?= __('Wall') ?></h2>
        <?php if ($wall !== null): ?>
            <?php
            // show notification if exist
            if (Obj::isArray($notify) && count($notify) > 0) {
                echo $this->render('native/macro/notify', ['notify' => $notify]);
            }
            ?>
            <?php $form = new Form(
                $wall,
                ['class' => 'form-horizontal', 'method' => 'post', 'action' => ''],
                ['base' => 'profile/form/wall_base']
            ); ?>
            <?= $form->start() ?>
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

                $referNickname = Simplify::parseUserNick($post->sender_id);
                ?>
                <div class="row object-lightborder" id="wall-post-<?= $post->id ?>">
                    <div class="col-xs-4 col-md-2">
                        <div class="text-center"><img class="img-responsive img-rounded" alt="Avatar of <?= $referNickname ?>"
                             src="<?= $referObject->getProfile()->getAvatarUrl('small') ?>" />
                        </div>
                    </div>
                    <div class="col-xs-8 col-md-10">
                        <h5 style="margin-top: 0;">
                            <i class="glyphicon glyphicon-pencil"></i> <?= Url::link(['profile/show', $post->sender_id], $referNickname) ?>
                            <small class="pull-right"><?= Date::humanize($post->updated_at); ?></small>
                        </h5>
                        <div class="object-text">
                            <?= $post->message ?>
                        </div>
                        <hr style="margin: 5px;" />
                        <div><i class="glyphicon glyphicon-comment"></i>
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

<!-- add answer dom template -->
<div id="add-answer-field" class="hidden">
    <hr style="margin: 5px;"/>
    <input type="text" id="make-answer" placeHolder="<?= __('Write comment') ?>" class="form-control wall-answer-text" maxlength="200"/>
    <a style="margin-top: 5px;" href="#wall-post" class="send-wall-answer btn btn-primary btn-sm" id="send-wall">
        <?= __('Send') ?>
    </a>
    <span class="pull-right" id="answer-counter">200</span>
</div>
<div id="show-answer-list" class="hidden">
    <div class="row wall-answer">
        <div class="col-md-2 col-xs-4"><img id="wall-answer-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" alt="avatar" class="img-responsive img-rounded avatar" /></div>
        <div class="col-md-10 col-xs-8">
            <div class="answer-header">
                <a href="<?= \App::$Alias->baseUrl ?>/profile/index" id="wall-answer-userlink">unknown</a>
                <small class="pull-right"><span id="wall-answer-date">01.01.1970</span>
                    <a href="#send-wall-object" class="delete-answer hidden" id="delete-answer"><i class="glyphicon glyphicon-remove"></i></a>
                </small>
            </div>
            <div id="wall-answer-text"></div>
        </div>
    </div>
</div>


<script>
    var hideAnswers = [];
    $(document).ready(function () {
        var elements = $('.object-lightborder');
        var viewer_id = 0;
        var target_id = 0;
        var is_self_profile = <?= $isSelf === true ? 'true' : 'false' ?>;
        <?php if (\App::$User->isAuth()): ?>
        viewer_id = <?= $viewer->getId() ?>;
        <?php endif; ?>
        target_id = <?= $user->getId() ?>;
        var postIds = [];
        $.each(elements, function (key, val) {
            postIds.push(val.id.replace('wall-post-', ''));
        });

        // load answers count via JSON
        if (postIds.length > 0) {
            $.getJSON(script_url + '/api/profile/wallanswercount/' + postIds.join(',') + '?lang=' + script_lang, function (json) {
                // data is successful loaded, pharse
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
                answerField.removeAttr('id').removeClass('hidden');
                answerDom.removeAttr('id').removeClass('hidden');
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
                    dom.find('#wall-answer-avatar').attr('src', row.user_avatar).removeAttr('id');
                    // set user link
                    dom.find('#wall-answer-userlink').attr('href', '<?= Url::to('profile/show') ?>/' + row.user_id).text(row.user_nick).removeAttr('id');
                    // set date
                    dom.find('#wall-answer-date').text(row.answer_date).removeAttr('id');
                    // set message text
                    dom.find('#wall-answer-text').text(row.answer_message);
                    // check if this user can remove answers - answer writer or target user profile
                    if (is_self_profile || row.user_id === viewer_id) {
                        dom.find('#delete-answer').attr('href', '#send-wall-object-' + postId).attr('id', 'delete-answer-' + row.answer_id + '-' + postId).removeClass('hidden');
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
                }
            }, 'json').done(function () {
                return true;
            });
            return false;
        };


        // if clicked on "Answers" - show it and send form
        $('.show-wall-response').on('click', function () {
            var postId = this.id.replace('wall-post-response-', '');
            // control hide-display on clicking to "Answers" link
            if (hideAnswers[postId] === true) {
                hideAnswers[postId] = false;
                $('#wall-answer-dom-' + postId).addClass('hidden');
                return null;
            } else {
                hideAnswers[postId] = true;
                $('#wall-answer-dom-' + postId).removeClass('hidden');
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
                limitObject.html('<span class="label label-danger">0</span>');
            } else {
                limitObject.text(msglimit - msglength);
            }
        });

        $(document).on('click', '.delete-answer', function () {
            var answerIdPostId = this.id.replace('delete-answer-', '').split('-');
            $.getJSON(script_url + '/api/profile/deleteanswerowner/' + answerIdPostId[0] + '?lang=' + script_lang, function (response) {
                loadAnswers(answerIdPostId[1]);
            });
        });

        // delegate live event simple for add-ed dom element
        $(document).on('click', '.send-wall-answer', function () {
            var answerToId = this.id.replace('send-wall-', '');
            var message = $('#make-answer-' + answerToId).val();
            if (message == null || message.length < 3) {
                alert('<?= __('Message is too short') ?>');
                return null;
            }

            var result = addAnswer(answerToId, message);
            // sending going wrong !
            if (false === result) {
                $('#send-wall-object-' + answerToId).html('<p class="alert alert-warning"><?= __('Comment send was failed! Try to send it later.') ?></p>');
            }
        });

        // work with + and - rating clicks
        changeRating = function (type) {
            // prevent some shits
            if (is_self_profile || viewer_id == 0) {
                return false;
            }

            $.post(script_url + '/api/profile/changerating?lang=' + script_lang, {type: type, target: target_id}, function (resp) {
                if (resp.status === 1) {
                    var rV = parseInt($('#ratingValue').text());
                    if (type == '+') {
                        $('#ratingValue').text(rV + 1);
                    } else {
                        $('#ratingValue').text(rV - 1);
                    }
                    alert('<?= __('Rating was successful changed') ?>');
                } else {
                    alert('<?= __('Rating cannot be changed') ?>');
                }
                $('#addRating').addClass('disabled');
                $('#reduceRating').addClass('disabled');
            }, 'json');
        };

        $('#addRating').on('click', function () {
            changeRating('+');
        });
        $('#reduceRating').on('click', function () {
            changeRating('-');
        });
    });
</script>