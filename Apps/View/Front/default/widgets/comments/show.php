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
            <img id="comment-user-avatar" class="img-responsive img-rounded" alt="User avatar" src="/upload/user/avatar/small/default.jpg" />
        </div>
        <div class="text-center">
            <span class="label label-success"><i class="fa fa-plus"></i></span>
            <span class="label label-danger"><i class="fa fa-minus"></i></span>
        </div>
    </div>
    <div class="col-md-10">
        <h5 style="margin-top: 0;">
            <i class="fa fa-pencil"></i> <a href="javascript:void(0)" id="comment-user-nick">Unknown</a>
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
        <div id="comment-answer-dom-1" class="hidden"></div>
    </div>
</div>


<script>
    window.jQ.push(function() {
        $(function () {
            var comOffset = 0;
            var comPath = '<?= \App::$Request->getPathInfo() ?>';
            var comStructure = $('#comment-structure').clone();
            comStructure.removeClass('hidden').removeAttr('id');
            var targetElem = $('#comment-list');

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

                        } else {

                        }
                        // set answers count for this comment post
                        commentDom.find('#comment-answer-count').text(row.answers).attr('id', 'comment-answer-count-'+row.id);
                        // set id for row
                        commentDom.attr('id', 'comment-item-'+row.id);
                        // set date, remove id
                        commentDom.find('#comment-date').text(row.date).removeAttr('id');
                        // set data for load answers - id, anchor
                        commentDom.find('.show-comment-answers').attr('href', '#comment-item-'+row.id).attr('id', 'comment-id-'+row.id);
                        // set user nickname
                        commentDom.find('#comment-user-nick').text(row.user.name);
                        // set link to profile
                        if (row.user.id > 0) {
                            commentDom.find('#comment-user-nick').attr('href', site_url + '/profile/show/' + row.user.id);
                        }
                        // remove duplicate id attribute
                        commentDom.find('#comment-user-nick').removeAttr('id');

                        // append or prepend data to general dom element
                        targetElem.append(commentDom);
                    });
                });
                // increase offset
                comOffset++;
            };

            $.fn.loadAnswerList = function (comId) {
                alert('Loading: ' + comId)
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