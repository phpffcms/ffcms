<?php
/** @var array $configs */
?>
<!-- Note! You can change this structures any way you like. JS operations based ONLY on ID and "hidden" class -->
<!-- comments general line -->
<div class="row">
    <div class="col-md-12">
        <div class="page-comment">
            <ul class="comments" id="comment-list"></ul>
        </div>
        <div id="comment-show-more" class="hidden">
            <button class="btn btn-block btn-info">Load more (<span id="comment-left-count">85</span> left)</button>
        </div>
    </div>
</div>

<!-- Comment post item structure. -->
<li class="clearfix hidden" id="comment-structure">
    <img id="comment-user-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="avatar" alt="Avatar">
    <div class="post-comments">
        <p class="meta">
            <span id="comment-date">01.01.2016</span> <span id="comment-user-nick"><?= __('Unknown') ?></span>:
            <i class="pull-right">
                <a href="#" class="show-comment-answers">
                    <small><?= __('Answers') ?> (<span id="comment-answer-count">0</span>)</small>
                </a>
            </i>
        </p>
        <p id="comment-text"><?= __('Loading') . ' ...' ?></p>
    </div>
    <div id="comment-answers-0" class="hidden"></div>
    <div class="row hidden" id="add-replay-to">
        <div class="col-md-12" style="padding-bottom: 15px;">
            <div class="pull-right">
                <a href="#showReplay" class="btn btn-info btn-sm comment-hook-replay" id="replay-to-0">
                    <i class="fa fa-plus-circle"></i> <?= __('Replay') ?>
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
            <p class="meta">
                <span class="answer-date">00.00.00 00-00-00</span>
                <span id="answer-user-name"><?= __('Unknown') ?></span>:
            </p>
            <p id="answer-text"><?= __('Loading') . ' ...' ?></p>
        </div>
    </li>
</ul>

<?php if ((int)$configs['guestAdd'] === 1 || \App::$User->isAuth()): ?>
<!-- comment form -->
<form name="comment-add-form" action="" method="post" style="padding-top: 15px;" class="form-horizontal">
    <input type="hidden" name="replay-to" value="0" />
    <?php if (!\App::$User->isAuth()): ?>
    <div class="form-group">
        <label for="guest-name" class="col-sm-3 control-label"><?= __('Your name') ?>:</label>
        <div class="col-sm-9">
            <input id="guest-name" type="text" name="guest-name" class="form-control" placeholder="John" required minlength="3">
        </div>
    </div>
    <?php endif; ?>
    <textarea class="form-control wysi-comments" name="message"></textarea>
</form>
<!-- comment send button -->
<div class="row" id="showReplay">
    <div class="col-md-12">
        <span class="label label-primary hidden" id="replay-to-text"><?= __('Replay to') ?>:
            <span id="replay-to-user">user</span>
            <i class="fa fa-close"></i>
        </span>
        <button class="btn btn-success pull-right" id="add-new-comment"><?= __('Send') ?></button>
    </div>
</div>
<?php endif ?>

<script>
    window.jQ.push(function() {
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

            // load comments posts via JSON and add to current DOM
            $.fn.loadCommentList = function() {
                $.getJSON(script_url+'/api/comments/list/' + comOffset +'?path=' + comPath + '&lang='+script_lang, function (json) {
                    if (json.status !== 1) {
                        return null;
                    }
                    // if json response is done lets foreach rows
                    $.each(json.data, function(index,row) {
                        commentData[row.id] = row;
                        // create clone of comment structure
                        var commentDom = comStructure.clone();
                        // set comment text
                        commentDom.find('#comment-text').html(row.text);
                        // working aroud user data, prepare display
                        if (row.user.id > 0) {
                            commentDom.find('#comment-user-avatar').attr('src', row.user.avatar).removeAttr('id');
                            var userLink = $('<a></a>').attr('href', site_url + '/profile/show/'+row.user.id).text(row.user.name);
                            commentDom.find('#comment-user-nick').html(userLink);
                        } else {
                            commentDom.find('#comment-user-nick').text(row.user.name);
                        }
                        // set answers count for this comment post
                        commentDom.find('#comment-answer-count').text(row.answers).attr('id', 'comment-answer-count-'+row.id);
                        // set id for row
                        commentDom.attr('id', 'comment-item-'+row.id);
                        // set id for answer add
                        commentDom.find('#replay-to-0').attr('id', 'replay-to-'+row.id);
                        // set date, remove id
                        commentDom.find('#comment-date').text(row.date).removeAttr('id');
                        // set data for load answers - id, anchor
                        commentDom.find('.show-comment-answers').attr('href', '#comment-item-'+row.id).attr('id', 'comment-id-'+row.id);

                        // remove duplicate id attribute
                        commentDom.find('#comment-user-nick').removeAttr('id');

                        // set answers anchor with id
                        commentDom.find('#comment-answers-0').attr('id', 'comment-answers-' + row.id);

                        // append or prepend data to general dom element
                        targetElem.append(commentDom.show('slow'));
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

            $.fn.loadAnswerList = function (comId) {
                var targetElement = $('#comment-answers-' + comId);
                if (answersLoaded[comId] === true) {
                    targetElement.toggle('slow');
                    targetElement.parent().find('#add-replay-to').toggle('slow');
                    return null;
                }
                // load data via jquery-json
                $.getJSON(script_url + '/api/comments/showanswers/' + comId + '?lang='+script_lang, function(json){
                    // status != 1 sounds like fail query
                    if (json.status !== 1) {
                        return null;
                    }

                    // foreach response content and prepare output html container
                    $.each(json.data, function(idx,row){
                        var ansDom = answStructure.clone();
                        ansDom.find('#answer-text').text(row.text).removeAttr('id');

                        if (row.user.id > 0) { // registered user, link required
                            var userLink = $('<a></a>').attr('href', site_url + '/profile/show/'+row.user.id).text(row.user.name);
                            ansDom.find('#answer-user-name').html(userLink).removeAttr('id');
                            ansDom.find('#answer-user-avatar').attr('src', row.user.avatar).removeAttr('id');
                        } else { // its a guest, display only nickname
                            ansDom.find('#answer-user-name').text(row.user.name).removeAttr('id');
                        }

                        // append to target comment post
                        targetElement.append(ansDom);
                        // make item visible
                        targetElement.hide().removeClass('hidden').show('slow');
                        // save marker
                        answersLoaded[comId] = true;
                        targetElement.parent().find('#add-replay-to').hide().removeClass('hidden').show('slow');
                    });
                });
            };

            // load first N comments
            $.fn.loadCommentList();

            $('#comment-show-more').on('click', function(){
                $.fn.loadCommentList();
            });

            // show answers for comment
            $(document).on('click', '.show-comment-answers', function(){
                var comId = this.id.replace('comment-id-', '');
                if (comId > 0)
                    $.fn.loadAnswerList(comId);
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

                $.post(script_url+'/api/comments/add?lang='+script_lang, formData.serialize()).done(function(res){
                    console.log(res);
                });
            });

        });
    });
</script>