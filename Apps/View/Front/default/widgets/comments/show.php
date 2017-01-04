<?php
/** @var array $configs */

?>

<?php if ((bool)$configs['guestAdd'] && (bool)$configs['guestModerate'] && !\App::$User->isAuth()): ?>
<p class="alert alert-warning"><?= __('All guest comments will be moderated before display') ?></p>
<?php endif; ?>

<div id="comments-list"></div> <!-- special anchor to use #comment-list -->
<!-- Note! You can change this structures any way you like. JS operations based ONLY on ID and "hidden" class -->
<!-- comments general line -->
<div class="row">
    <div class="col-md-12">
        <div class="page-comment">
            <ul class="comments" id="comment-list">
                <li class="hidden" id="load-comments"></li>
            </ul>
        </div>
        <div id="comment-show-more" class="hidden">
            <button class="btn btn-block btn-info">Load more (<span id="comment-left-count">0</span> left)</button>
        </div>
    </div>
</div>

<!-- Comment post item structure. -->
<li class="clearfix hidden" id="comment-structure">
    <img id="comment-user-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="avatar" alt="Avatar">
    <div class="post-comments">
        <div class="meta">
            <span id="comment-date">01.01.2016</span> <span id="comment-user-nick"><?= __('Unknown') ?></span>:
            <i class="pull-right">
                <a href="#" class="show-comment-answers">
                    <small><?= __('Answers') ?> (<span id="comment-answer-count">0</span>)</small>
                </a>
            </i>
        </div>
        <div id="comment-text" class="comment-text"><?= __('Loading') . ' ...' ?></div>
    </div>
    <div id="comment-answers-0" class="hidden"></div>
    <div class="row hidden" id="add-replay-to">
        <div class="col-md-12" style="padding-bottom: 15px;">
            <div class="pull-right">
                <a href="#showReplay" class="btn btn-info btn-sm comment-hook-replay" id="replay-to-0">
                    <i class="glyphicon glyphicon-plus"></i> <?= __('Replay') ?>
                </a>
            </div>
        </div>
    </div>
</li>

<!-- comment answer item structure -->
<ul class="comments hidden" id="comment-answer-item">
    <li class="clearfix">
        <img id="answer-user-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="avatar" alt="avatar">
        <div class="post-comments">
            <div class="meta">
                <span id="answer-date">00.00.00 00-00-00</span>
                <span id="answer-user-name"><?= __('Unknown') ?></span>:
            </div>
            <div id="answer-text" class="comment-text"><?= __('Loading') . ' ...' ?></div>
        </div>
    </li>
</ul>

<?php if ((int)$configs['guestAdd'] === 1 || \App::$User->isAuth()): ?>
<!-- comment form -->
<form name="comment-add-form" action="" method="post" style="padding-top: 15px;" class="form-horizontal">
    <input type="hidden" name="replay-to" value="0" />
    <input type="hidden" name="pathway" value="<?= \App::$Request->getPathInfo() ?>" />
    <?php if (!\App::$User->isAuth()): ?>
    <div class="form-group">
        <label for="guest-name" class="col-sm-3 control-label"><?= __('Your name') ?>:</label>
        <div class="col-sm-9">
            <input id="guest-name" type="text" name="guest-name" class="form-control" placeholder="John" required>
        </div>
    </div>
    <?php if (\App::$Captcha->isFull()): ?>
		<div class="col-md-offset-3 col-md-9"><?= \App::$Captcha->get() ?></div>
    <?php else: ?>
    <div class="form-group">
        <label for="guest-captcha" class="col-sm-3 control-label"><?= __('Captcha') ?>:</label>
        <div class="col-sm-9">
        	<img src="<?= \App::$Captcha->get() ?>" onclick="this.src='<?= \App::$Captcha->get() ?>&lang=<?= \App::$Request->getLanguage() ?>&rnd='+Math.random()" id="comment-captcha" /> <br />
            <input id="guest-captcha" type="text" name="captcha" class="form-control" required>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <textarea class="form-control wysi-comments" name="message"></textarea>
</form>
<!-- comment send button -->
<div class="row" id="showReplay">
    <div class="col-md-12">
        <span class="label label-primary hidden" id="replay-to-text"><?= __('Replay to') ?>:
            <span id="replay-to-user">user</span>
            <i class="glyphicon glyphicon-remove"></i>
        </span>
        <button class="btn btn-success pull-right" id="add-new-comment"><?= __('Send') ?></button>
    </div>
</div>
<?php endif ?>

<script>
    $(document).ready(function() {
        $(function () {
            var comOffset = 0;
            var comPath = '<?= \App::$Request->getPathInfo() ?>';
            var comStructure = $('#comment-structure').clone();
            var answStructure = $('#comment-answer-item').clone();
            var comSendForm = $('#comment-answer-form').clone();
            comStructure.removeClass('hidden').removeAttr('id');
            answStructure.removeClass('hidden').removeAttr('id');
            comSendForm.removeClass('hidden').removeAttr('id');
            var targetElem = $('#comment-list');
            var commentData = [];
            var answersLoaded = [];

            buildCommentDOM = function (data) {
                // comment is always loaded (can be ajax-add from user in after position or something else)
                if (data.id in commentData) {
                    $('#comment-item-'+data.id).remove();
                }
                commentData[data.id] = data;
                // create clone of comment structure
                var commentDom = comStructure.clone();
                if (data.moderate === 1) {
                	commentDom.find('#comment-text').addClass('premoderate-comment');
                	commentDom.find('#comment-date').before('<span class="label label-danger"><?= __('On moderation') ?></span> ');
                }
                // set comment text
                commentDom.find('#comment-text').html(data.text).removeAttr('id');
                // working around user data, prepare display
                if (data.user.id > 0) {
                    commentDom.find('#comment-user-avatar').attr('src', data.user.avatar).removeAttr('id');
                    var userLink = $('<a></a>').attr('href', site_url + '/profile/show/'+data.user.id).text(data.user.name);
                    commentDom.find('#comment-user-nick').html(userLink);
                } else {
                    commentDom.find('#comment-user-nick').text(data.user.name);
                }
                // set answers count for this comment post
                commentDom.find('#comment-answer-count').text(data.answers).attr('id', 'comment-answer-count-'+data.id);
                // set id for row
                commentDom.attr('id', 'comment-item-'+data.id);
                // set id for answer add
                commentDom.find('#replay-to-0').attr('id', 'replay-to-'+data.id);
                // set date, remove id
                commentDom.find('#comment-date').text(data.date).removeAttr('id');
                // set data for load answers - id, anchor
                commentDom.find('.show-comment-answers').attr('href', '#comment-item-'+data.id).attr('id', 'comment-id-'+data.id);

                // remove duplicate id attribute
                commentDom.find('#comment-user-nick').removeAttr('id');

                // set answers anchor with id
                commentDom.find('#comment-answers-0').attr('id', 'comment-answers-' + data.id);

                return commentDom;
            };

            buildAnswerDOM = function (data) {
                var ansDom = answStructure.clone();
                if (data.moderate === 1) {
                	ansDom.find('#answer-text').addClass('premoderate-comment');
                	ansDom.find('#answer-date').before('<span class="label label-danger"><?= __('On moderation') ?></span> ');
                }
                ansDom.find('#answer-text').html(data.text).removeAttr('id');

                if (data.user.id > 0) { // registered user, link required
                    var userLink = $('<a></a>').attr('href', site_url + '/profile/show/'+data.user.id).text(data.user.name);
                    ansDom.find('#answer-user-name').html(userLink).removeAttr('id');
                    ansDom.find('#answer-user-avatar').attr('src', data.user.avatar).removeAttr('id');
                } else { // its a guest, display only nickname
                    ansDom.find('#answer-user-name').text(data.user.name).removeAttr('id');
                }

                // set date
                ansDom.find('#answer-date').text(data.date).removeAttr('id');

                return ansDom;
            };

            // load comments posts via JSON and add to current DOM
            loadCommentList = function() {
                $.getJSON(script_url+'/api/comments/list/' + comOffset +'?path=' + comPath + '&lang='+script_lang, function (json) {
                    if (json.status !== 1) {
                        var errorNotify = $('<p></p>').text(json.message).attr('id', 'comments-empty-notify');
                        targetElem.append(errorNotify);
                        return null;
                    }

                    // remove error notifications
                    targetElem.find('#comments-empty-notify').remove();

                    // if json response is done lets foreach rows
                    $.each(json.data, function(index,row) {
                        // add comment to document DOM model
                        var domHtmlComment = buildCommentDOM(row);
                        // find current hidden selector and add data before it
                        targetElem.find('#load-comments').before(domHtmlComment.show('slow'));
                    });

                    if (json.leftCount > 0) {
                        $('#comment-show-more').removeClass('hidden').find('#comment-left-count').text(json.leftCount);
                    } else {
                        $('#comment-show-more').addClass('hidden');
                    }
                });
                // increase offset
                comOffset++;
            };

            loadAnswerList = function (comId) {
                var targetElement = $('#comment-answers-' + comId);
                if (answersLoaded[comId] === true) {
                    targetElement.toggle('slow');
                    targetElement.parent().find('#add-replay-to').toggle('slow');
                    return null;
                }
                // load data via jquery-json
                $.getJSON(script_url + '/api/comments/showanswers/' + comId + '?lang='+script_lang, function(json){
                    if (json.status !== 1) {
                        targetElement.parent().find('#add-replay-to').hide().removeClass('hidden').show('slow');
                        return null;
                    }

                    // foreach response content and prepare output html container
                    $.each(json.data, function(idx,row){
                        if (row == null) {
                            return null;
                        }
                        var domHtmlAnswer = buildAnswerDOM(row);

                        // append to target comment post
                        targetElement.append(domHtmlAnswer);
                        // make item visible
                        targetElement.hide().removeClass('hidden').show('slow');
                        // save marker
                        answersLoaded[comId] = true;
                        targetElement.parent().find('#add-replay-to').hide().removeClass('hidden').show('slow');
                    });
                });
            };

            // load first N comments
            loadCommentList();

            $('#comment-show-more').on('click', function(){
                loadCommentList();
            });

            // show answers for comment
            $(document).on('click', '.show-comment-answers', function(){
                var comId = this.id.replace('comment-id-', '');
                if (comId > 0)
                    loadAnswerList(comId);
            });

            // add 'replayTo' field for sending form
            $(document).on('click', '.comment-hook-replay', function(){
                var comId = this.id.replace('replay-to-', '');
                if (comId > 0) {
                    // set hidden value
                    $('form[name="comment-add-form"]').find('input[name="replay-to"]').val(comId);
                    // find comment with current id
                    var comUser = commentData[comId].user.name;
                    var comHtml = $('#replay-to-text');
                    // show label
                    comHtml.find('#replay-to-user').text(comUser);
                    comHtml.hide().removeClass('hidden').show('slow');
                }
            });

            // remove replayTo field after cancel click
            $(document).on('click', '#replay-to-text', function(){
                // unset replay to field
                $('form[name=comment-add-form]').find('input[name=replay-to]').val(0);
                // remove label
                $(this).toggle('slow');
            });

            // listen click on "add comment" button
            $('#add-new-comment').on('click', function(){
                var formData = $('form[name="comment-add-form"]');

                // refresh user captcha if guest submit
                if ($('#comment-captcha').length > 0) {
					$('#comment-captcha').trigger('click');
                }

                $.post(script_url+'/api/comments/add?lang='+script_lang, formData.serialize()).done(function(res){
                    // comment post is successful added
                    if (res.status === 1) {
                        // its a new comment post item
                        if (res.data.type === 'post') {
                            var domHtmlComment = buildCommentDOM(res.data);
                            formData.trigger('reset');
                            ckCleanup();
                            // remove error notifications
                            targetElem.find('#comments-empty-notify').remove();
                            // add comment dom content element
                            targetElem.find('#load-comments').after(domHtmlComment);
                        } else if (res.data.type === 'answer') { // looks like a new answer to comment post item
                            var domHtmlAnswer = buildAnswerDOM(res.data);
                            var targetCommentDom = $('#comment-answers-' + res.data.comment_id);
                            formData.trigger('reset');
                            ckCleanup();
                            targetCommentDom.append(domHtmlAnswer).hide().removeClass('hidden').show('slow');
                        }
                    } else {
                        alert(res.message);
                    }
                });
            });

            ckCleanup = function () {
                for (var ckInstance in CKEDITOR.instances ) {
                    CKEDITOR.instances[ckInstance].updateElement();
                    CKEDITOR.instances[ckInstance].setData('');
                }
            };

        });
    });
</script>