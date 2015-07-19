<?php
use Ffcms\Core\Helper\Type\Object;
use Ffcms\Core\Helper\Url;

$this->title = __('My dialogs');
$this->breadcrumbs = [
    Url::to('main/index') => __('Home'),
    Url::to('profile/show', \App::$User->identity()->id) => __('Profile'),
    __('My messages')
];

?>
<h1><?= __('My messages') ?></h1>
<hr />
<div class="row">
    <div class="col-md-3 well-light" style="min-height: 300px">
        <div id="message-user-list" style="padding-bottom: 10px;"></div>
        <div class="row">
            <div class="col-md-12">
                <a href="#" class="btn btn-primary btn-block btn-sm" id="show-more-dialogs"><i class="fa fa-caret-down"></i> <?= __('Show more') ?></a>
            </div>
        </div>

    </div>
    <div class="col-md-9">
        <!-- user info -->
        <div class="row">
            <div class="col-md-12">
                <div class="well-light">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="dialog-user-streak">
                                <div class="pull-right">
                                    <img src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="pull-right img-responsive img-circle" style="max-height: 50px;" />
                                    <div class="pull-right" style="padding-top: 12px;">
                                        <span class="media-person-uname"><?= __('No data') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="message-scroll-body hidden">
            <div class="col-md-12">
                <p class="text-center"><a href="javascript:void(0);" id="message-load-before"><i class="fa fa-caret-up"></i> <?= __('Load previous') ?></a></p>
            </div>
            <div id="messages-before"></div>
            <div id="messages-now"></div>
            <div id="messages-after"></div>
            <div id="messages-blocked-user" class="hidden alert alert-danger"><?= __('This user are in your black list or you are in blacklist!') ?></div>
        </div>
        <div class="message-add-container hidden" style="padding-top: 10px;">
            <textarea class="form-control" id="msg-text" maxlength="1000" required></textarea>
            <a href="javascript:void(0);" class="btn btn-primary" id="send-new-message"><?= __('Send message') ?></a>
        </div>
    </div>
</div>

<script>
    var active_dialog_id = 0;
    var user_object = [];
    var dialog_offset = 0;
    var new_dialog = 0;

    var last_msg = [];
    var first_msg = [];

    window.jQ.push(function(){
        $(function(){
            // load users with active dialog
            $.fn.loadDialogUsers = function() {
                $.getJSON(script_url+'/api/profile/listmessagedialog/'+dialog_offset+'/'+new_dialog+'/?lang='+script_lang, function(response){
                    if (response.status === 1) {
                        if (response.data.length < 1) {
                            $('#show-more-dialogs').addClass('hidden');
                            $('#message-user-list').text('<?= __('No dialog founded') ?>');
                            return false;
                        }
                        var userMap = '';
                        $('.media-person').removeClass('media-person-selected');
                        $.each(response.data, function(key, row){
                            var itemClass = 'media-person';
                            if (row.user_id == active_dialog_id) {
                                itemClass += ' media-person-selected';
                            }
                            if (row.user_block == true) {
                                itemClass += ' media-person-blocked';
                            }
                            userMap += '<div class="'+itemClass+'" id="msg-user-'+row.user_id+'" >'; // if current add class media-person-selected
                            userMap += '<div class="row">';
                            userMap += '<div class="col-md-12">';
                            userMap += '<img src="'+row.user_avatar+'" class="pull-left img-responsive img-circle" style="max-height: 50px;padding-right: 5px;" />';
                            userMap += '<div style="padding-top: 12px;">';
                            userMap += '<span class="media-person-uname">';
                            if (row.user_block == true) {
                                userMap += '<s>'+row.user_nick+'</s>';
                            } else {
                                userMap += row.user_nick;
                            }
                            userMap += '</span>';
                            if (row.message_new === true) {
                                userMap += ' <i class="fa fa-envelope"></i>';
                            }
                            userMap += '</div>';
                            userMap += '</div></div></div>';
                            // store object data
                            user_object[row.user_id] = row;
                        });
                        $('#message-user-list').html(userMap);
                    } else {
                        $('#show-more-dialogs').addClass('hidden');
                    }
                }).complete(function(){
                    if (new_dialog > 0) {
                        $('.message-scroll-body').removeClass('hidden');
                        // set message streak title
                        var current_user = user_object[new_dialog];
                        $('#dialog-user-streak').html('<img src="'+current_user.user_avatar+'" class="pull-right img-responsive img-circle" style="max-height: 50px;" />'+
                        '<div class="pull-right" style="padding-top: 12px;padding-right: 5px;"><span class="media-person-uname">'+current_user.user_nick+'</span></div>');
                        // load 'now' dialog messages
                        $.fn.loadMessageDialog('now');
                        $('.message-add-container').removeClass('hidden');
                    }
                });
            };
            $.fn.loadMessageDialog = function (type) {
                // prevent empty cycles
                if (active_dialog_id < 1) {
                    return false;
                }

                var msg_query = script_url+'/api/profile/messagelist/'+active_dialog_id+'?lang='+script_lang;
                if (type == 'before') {
                    if (first_msg[active_dialog_id] == null) {
                        return false;
                    }
                    msg_query += '&id='+first_msg[active_dialog_id]+'&type=before';
                } else if (type == 'after') {
                    if (last_msg[active_dialog_id] == null) {
                        return false;
                    }
                    msg_query += '&id='+last_msg[active_dialog_id]+'&type=after';
                } else {
                    msg_query += '&type=now';
                }

                $.getJSON(msg_query, function(resp){
                    if (resp.status !== 1) {
                        return false;
                    }

                    if (resp.blocked == true) {
                        $('#messages-blocked-user').removeClass('hidden');
                        $('#send-new-message').addClass('disabled');
                    } else {
                        $('#send-new-message').removeClass('disabled');
                        $('#messages-blocked-user').addClass('hidden');
                    }

                    var msgBody = '';
                    var isFirst = true;
                    $.each(resp.data, function(idx,row){
                        if (type != 'after' && isFirst) {
                            first_msg[active_dialog_id] = row.id;
                            isFirst = false;
                        }
                        var msgClass = 'col-md-6';
                        var msgTextClass = 'message-text';
                        if (row.my != 1) {
                            msgClass += ' col-md-offset-6';
                            msgTextClass += ' message-text-remote';
                        }

                        msgBody += '<div class="row" style="padding-top: 15px;">';
                        msgBody += '<div class="'+msgClass+'">';
                        msgBody += '<div class="'+msgTextClass+'">';
                        msgBody += '<div><small style="color: #696969;">'+row.date+'</small></div>';
                        msgBody += '<div>'+row.message+'</div>';
                        msgBody += '</div></div></div>';
                        if (type != 'before') {
                            last_msg[active_dialog_id] = row.id;
                        }
                    });
                    if (type == 'now') {
                        $('#messages-now').html(msgBody);
                        $(".message-scroll-body").animate({ scrollTop: $(document).height() }, "slow");
                    } else if(type == 'before') {
                        $('#messages-before').prepend(msgBody);
                    } else if (type == 'after') {
                        $('#messages-now').append(msgBody);
                        $(".message-scroll-body").animate({ scrollTop: $(document).height() }, "slow");
                    }
                });
            };
            <?php // check if defined ?newdialog=userid
            $dialogId = \App::$Request->query->get('newdialog', false);
            if (false !== $dialogId && Object::isLikeInt($dialogId) && $dialogId > 0) : ?>
            new_dialog = <?= $dialogId ?>;
            active_dialog_id = new_dialog;
            <?php endif; ?>
            // load dialogs when page ready
            $.fn.loadDialogUsers();
            // set scheduled loader
            window.setInterval($.fn.loadDialogUsers, 15 * 1000);
            // callback for user onclick -> show dialogs
            $(document).on('click', '.media-person', function() {
                var selected_dialog_id = this.id.replace('msg-user-', '');
                if (selected_dialog_id === active_dialog_id) {
                    return false;
                }
                // set active id
                active_dialog_id = selected_dialog_id;
                $('.media-person').removeClass('media-person-selected');
                $(this).addClass('media-person-selected');
                // make msg body visible
                $('.message-scroll-body').removeClass('hidden');
                // set message streak title
                var current_user = user_object[selected_dialog_id];
                var profile_link = '<?= Url::to('profile/show') ?>';
                $('#dialog-user-streak').html('<img src="'+current_user.user_avatar+'" class="pull-right img-responsive img-circle" style="max-height: 50px;" />'+
                '<div class="pull-right" style="padding-top: 12px;padding-right: 5px;">' +
                '<a href="'+profile_link+'/'+current_user.user_id+'" target="_blank"><span class="media-person-uname">'+current_user.user_nick+'</span></a></div>');
                // load 'now' dialog messages
                $.fn.loadMessageDialog('now');
                $('.message-add-container').removeClass('hidden');
            });
            $(document).on('click', '#message-load-before', function(){
                $.fn.loadMessageDialog('before');
            });
            // set schedule to show new messages
            window.setInterval(function(){$.fn.loadMessageDialog('after')}, 15 * 1000);

            // if clicked "show more" - increase offset and load permamently
            $('#show-more-dialogs').on('click', function(){
                var obj = $(this);
                obj.addClass('disabled');
                setTimeout(function(){
                    obj.removeClass('disabled');
                }, 5000);
                dialog_offset += 1;
                $.fn.loadDialogUsers();
            });

            // if click to btn send message to target
            $('#send-new-message').on('click', function(){
                if (active_dialog_id == 0) {
                    return false;
                }
                var msgText = $('#msg-text').val();
                if (msgText.length < 1) {
                    return false;
                }

                $.post(script_url+'/api/profile/messagesend/'+active_dialog_id+'?lang='+script_lang, {message: msgText}, function(resp){
                    if (resp.status === 1) {
                        $.fn.loadMessageDialog('after');
                        $('#msg-text').val(null);
                    }
                }, 'json').complete(function(){
                    if (active_dialog_id == new_dialog) {
                        new_dialog = 0;
                        $.fn.loadMessageDialog('now');
                    }
                });
            });
        });
    });
</script>