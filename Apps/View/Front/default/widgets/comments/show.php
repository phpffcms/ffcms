<!-- comment form -->
<form action="" method="post">
    <input type="hidden" id="comment-response-id" name="comment-response" />
    <textarea class="form-control wysi-comments" name="message"></textarea>
</form>
<!-- comment send button -->
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-success pull-right"><?= __('Send') ?></button>
    </div>
</div>

<!-- comment list dom element to insert inner -->
<div id="comment-list"></div>

<!-- comment item structure -->
<div class="row object-lightborder hidden" id="comment-structure">
    <div class="col-md-2">
        <div class="text-center">
            <img id="comment-user-avatar" class="img-responsive img-rounded" alt="User avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" />
        </div>
        <div class="text-center">
            <span class="label label-success"><i class="fa fa-plus"></i></span>
            <span class="label label-danger"><i class="fa fa-minus"></i></span>
        </div>
    </div>
    <div class="col-md-10">
        <h5 style="margin-top: 0;">
            <i class="fa fa-pencil"></i> <span id="comment-user-nick">Unknown</span>
            <small class="pull-right" id="comment-date">01.01.2016</small>
        </h5>
        <div class="object-text" id="comment-text">
            <?= __('Loading') . ' ...' ?>
        </div>
        <hr style="margin: 5px;" />
        <div>
            <i class="fa fa-comment-o"></i>
            <a href="#" class="show-comment-answers">
                <?= __('Answers') ?> (<span id="comment-answer-count">0</span>)
            </a>
            <a href="#" class="pull-right">
                <?= __('Delete'); ?>
            </a>
        </div>
        <div id="comment-answers-0" class="hidden"></div>
    </div>
</div>

<!-- comment answer item structure -->
<div id="comment-answer-item" class="hidden">
    <div class="row object-lightborder">
        <div class="col-md-2">
            <img src="http://ffcms3.local/upload/user/avatar/small/default.jpg" alt="avatar" class="img-responsive img-rounded">
        </div>
        <div class="col-md-10">
            <div class="answer-header">
                <span id="answer-user-name"><?= __('Unknown') ?></span>
                <small class="pull-right">
                    <span class="answer-date">00.00.00 00-00-00</span>
                    <a href="#send-wall-object-1" class="delete-answer" id="delete-answer-2-1"><i class="fa fa-lg fa-times"></i></a>
                </small>
            </div>
            <div id="answer-text"></div>
        </div>
    </div>
</div>

<!-- comment add answer form -->
<div id="comment-answer-form" class="hidden">
    <div class="well">
        <div id="send-wall-object-1"></div>
        <input type="text" id="make-answer-1" placeholder="Напишите комментарий" class="form-control wall-answer-text" maxlength="200">
        <a style="margin-top: 5px;" href="#wall-post-1" class="send-wall-answer btn btn-primary btn-sm" id="send-wall-1">Отправить</a>
        <span class="pull-right" id="answer-counter-1">200</span>
    </div>
</div>


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
            var answersLoaded = [];

            // load answers via JSON and add to current DOM
            $.fn.loadCommentList = function() {
                $.getJSON(script_url+'/api/comments/list/' + comOffset +'?path=' + comPath + '&lang='+script_lang, function (json) {
                    if (json.status !== 1) {
                        return null;
                    }
                    // if json response is done lets foreach rows
                    $.each(json.data, function(index,row) {
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
                        // set date, remove id
                        commentDom.find('#comment-date').text(row.date).removeAttr('id');
                        // set data for load answers - id, anchor
                        commentDom.find('.show-comment-answers').attr('href', '#comment-item-'+row.id).attr('id', 'comment-id-'+row.id);

                        // remove duplicate id attribute
                        commentDom.find('#comment-user-nick').removeAttr('id');

                        // set answers anchor with id
                        commentDom.find('#comment-answers-0').attr('id', 'comment-answers-' + row.id);

                        // append or prepend data to general dom element
                        targetElem.append(commentDom);
                    });
                });
                // increase offset
                comOffset++;
            };

            $.fn.loadAnswerList = function (comId) {
                var targetElement = $('#comment-answers-' + comId);
                if (answersLoaded[comId] === true) {
                    targetElement.toggle('slow');
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
                        } else { // its a guest, display only nickname
                            ansDom.find('#answer-user-name').text(row.user.name).removeAttr('id');
                        }

                        targetElement.append(ansDom);

                        // make visible
                        targetElement.removeClass('hidden');
                        // save marker
                        answersLoaded[comId] = true;
                    });
                });
            };

            // load first N comments
            $.fn.loadCommentList();

            $(document).on('click', '.show-comment-answers', function(){
                var comId = this.id.replace('comment-id-', '');
                if (comId > 0)
                    $.fn.loadAnswerList(comId);
            });
        });
    });
</script>