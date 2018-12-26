<?php

use Ffcms\Core\Helper\Type\Any;
use Ffcms\Templex\Url\Url;

/** @var \Ffcms\Templex\Template\Template $this */

$this->layout('_layouts/default', [
    'title' => __('My dialogs'),
    'breadcrumbs' => [
        Url::to('main/index') => __('Home'),
        Url::to('profile/show', [\App::$User->identity()->id]) => __('Profile'),
        __('My messages')
    ],
    'fullgrid' => true
]);

?>

<?php $this->start('body') ?>
<div class="row" id="msg-layout">
    <div class="col-sm-4 d-none d-sm-block">
        <div class="well-light">
            <div id="message-user-list" class="pt-1"></div>
            <div class="row">
                <div class="col-md-12">
                    <a href="#" class="btn btn-primary btn-block btn-sm" id="show-more-dialogs"><?= __('Show more') ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-12 m-0 p-0">
        <!-- user info -->
        <div class="row">
            <div class="col-md-12">
                <div class="well-light">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="dialog-user-streak">
                                <div class="float-right">
                                    <img src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="float-right img-fluid img-circle" style="max-height: 50px;" />
                                    <div class="float-right" style="padding-top: 12px;">
                                        <span class="media-person-uname"><?= __('No data') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="message-scroll-body d-none">
            <div class="col-md-12">
                <p class="text-center"><a href="javascript:void(0);" id="message-load-before"><?= __('Load previous') ?></a></p>
            </div>
            <div id="messages-before"></div>
            <div id="messages-now"></div>
            <div id="messages-after"></div>
            <div id="messages-blocked-user" class="d-none alert alert-danger"><?= __('This user are in your black list or you are in blacklist!') ?></div>
            <div id="message-end-position"></div>
        </div>
        <div class="message-add-container d-none" style="padding-top: 10px;">
            <textarea class="form-control" id="msg-text" maxlength="1000"></textarea>
            <a href="javascript:void(0);" class="btn btn-primary" id="send-new-message"><?= __('Send message') ?></a>
        </div>
    </div>
</div>

<!-- dynamic dom templates -->
<!-- 1. Userlist -->
<div class="d-none" id="msg-user">
    <div id="msg-user-background">
        <div class="row">
            <div class="col-md-12">
                <img id="msg-user-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="pull-left img-fluid img-circle" style="max-height: 50px;padding-right: 5px;" />
                <div style="padding-top: 12px;">
                    <span class="media-person-uname" id="msg-user-name"><?= __('Unknown') ?></span>
                    <span class="d-none" id="msg-user-isnew"><i class="glyphicon glyphicon-envelope"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 2. Current dialog title -->
<div class="d-none" id="dialog-title">
    <img id="msg-user-avatar" src="<?= \App::$Alias->scriptUrl ?>/upload/user/avatar/small/default.jpg" class="float-right img-fluid img-circle" style="max-height: 50px;" />
    <div class="float-right" style="padding-top: 12px;padding-right: 5px;">
        <a href="#" target="_blank" id="msg-user-link"><span class="media-person-uname" id="msg-user-name">unknown</span></a>
    </div>
</div>

<!-- 3.1. Messages between users - owner message -->
<div class="d-none" id="msg-owner">
    <div class="row pt-1">
        <div class="col-md-6 col-xs-12">
            <div class="message-text">
                <div>
                    <small id="msg-user-nick"><?= __('You') ?></small>
                    <small class="float-right text-secondary" id="msg-date">01.01.1970</small>
                </div>
                <div id="msg-text" style="word-break: break-all">text</div>
            </div>
        </div>
    </div>
</div>
<!-- 3.2. Messages between users - oponent message -->
<div class="d-none" id="msg-remote">
    <div class="row pt-1">
        <div class="offset-md-6 col-md-6 col-xs-12">
            <div class="message-text message-text-remote">
                <div>
                    <small id="msg-user-nick">target</small>
                    <small class="float-right text-secondary" id="msg-date">01.01.1970</small>
                </div>
                <div id="msg-text" style="word-break: break-all">text</div>
            </div>
        </div>
    </div>
</div>

<script>
    /** Css class for default, selected and blocked user display in list */
    var cssUserList = {
        'default': 'media-person',
        'active': 'media-person-selected',
        'blocked': 'media-person-blocked'
    };
    var active_dialog_id = 0;
    var user_object = [];
    var dialog_offset = 0;
    var new_dialog = 0;
    var profile_link = '<?= Url::to('profile/show') ?>';
    var last_msg = [];
    var first_msg = [];
    $(document).ready(function(){
        $(function(){
            var userListDom = $('#msg-user').clone();
            var dialogPanelDom = $('#dialog-title').clone();
            var myMsgDom = $('#msg-owner').clone();
            var remMsgDom = $('#msg-remote').clone();
            // load users with active dialog
            loadDialogUsers = function() {
                $.getJSON(script_url+'/api/profile/listmessagedialog/'+dialog_offset+'/'+new_dialog+'/?lang='+script_lang, function(response){
                        if (response.status !== 1) {
                            $('#show-more-dialogs').addClass('d-none');
                            return false;
                        }
                        if (response.data.length < 1) {
                            $('#msg-layout').after('<p><?= __('You have no active dialogs. Find users to message with: %u%', ['u' => Url::a(['profile/index', ['all']], __('user list'))]) ?></p>');
                            $('#msg-layout').remove();
                            return false;
                        }
                        var userMap = '';
                        $('.media-person').removeClass(cssUserList.active);
                        $.each(response.data, function(key, row){
                            var itemClass = cssUserList.default;
                            if (row.user_id == active_dialog_id) {
                                itemClass += ' '+cssUserList.active;
                            }
                            if (row.user_block == true) {
                                itemClass += ' '+cssUserList.blocked;
                            }
                            // clone user list dom template
                            var itemDom = userListDom.clone();
                            // set id attr
                            itemDom.find('#msg-user-background').attr('id', 'msg-user-'+row.user_id).addClass(itemClass);
                            // set user obj avatar
                            itemDom.find('#msg-user-avatar').attr('src', row.user_avatar).removeAttr('id');
                            // set user name
                            if (row.user_block == true) {
                                itemDom.find('#msg-user-name').html('<s>'+row.user_nick+'</s>').removeAttr('id');
                            } else {
                                itemDom.find('#msg-user-name').text(row.user_nick).removeAttr('id');
                            }
                            // check if new messages inside
                            if (row.message_new === true) {
                                itemDom.find('#msg-user-isnew').removeClass('d-none').removeAttr('id');
                            }
                            // store object data
                            user_object[row.user_id] = row;
                            // concat string result
                            userMap += itemDom.html();
                        });
                        $('#message-user-list').html(userMap);

                    }).done(function(){
                        if (new_dialog < 1)
                            return false;

                        $('.message-scroll-body').removeClass('d-none');
                        // set message streak title
                        var current_user = user_object[new_dialog];
                        var dialogDom = dialogPanelDom.clone();
                        // cleanup global id
                        dialogDom.removeClass('d-none').removeAttr('id');
                        // set user avatar in title panel
                        dialogDom.find('#msg-user-avatar').attr('src', current_user.user_avatar).removeAttr('id');
                        // set user nickname
                        dialogDom.find('#msg-user-name').text(current_user.user_nick).removeAttr('id');
                        // set user profile link
                        dialogDom.find('#msg-user-link').attr('href', profile_link + '/' + current_user.user_id).removeAttr('id');
                        $('#dialog-user-streak').html(dialogDom.html());
                        // load 'now' dialog messages
                        loadMessageDialog('now');
                        $('.message-add-container').removeClass('d-none');
                    });
            };
            loadMessageDialog = function (type) {
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
                    // mark blocked user
                    if (resp.blocked == true) {
                        $('#messages-blocked-user').removeClass('d-none');
                        $('#send-new-message').addClass('disabled');
                    } else {
                        $('#send-new-message').removeClass('disabled');
                        $('#messages-blocked-user').addClass('d-none');
                    }
                    var msgBody = '';
                    var isFirst = true;
                    $.each(resp.data, function(idx,row){
                        if (type != 'after' && isFirst) {
                            first_msg[active_dialog_id] = row.id;
                            isFirst = false;
                        }
                        // get message dom element
                        var msgDom = (!row.my ? remMsgDom.clone() : myMsgDom.clone());
                        // set msg date
                        msgDom.find('#msg-date').text(row.date).removeAttr('id');
                        // set msg text
                        msgDom.find('#msg-text').text(row.message).removeAttr('id');
                        // add target user name from obj cache
                        if (!row.my && user_object[active_dialog_id] != null) {
                            msgDom.find('#msg-user-nick').text(user_object[active_dialog_id].user_nick);
                        }
                        // compile output concat var
                        msgBody += msgDom.html();
                        if (type != 'before') {
                            last_msg[active_dialog_id] = row.id;
                        }
                    });
                    if (type == 'now') {
                        $('#messages-now').html(msgBody);
                        $(".message-scroll-body").animate({ scrollTop: 100000 }, "fast");
                    } else if(type == 'before') {
                        $('#messages-before').prepend(msgBody);
                    } else if (type == 'after') {
                        $('#messages-now').append(msgBody);
                        $(".message-scroll-body").animate({ scrollTop: 100000 }, "fast");
                    }
                });
            };
            <?php // check if defined ?newdialog=userid
            $dialogId = \App::$Request->query->get('newdialog', false);
            if ($dialogId && Any::isInt($dialogId) && $dialogId > 0) : ?>
            new_dialog = <?= $dialogId ?>;
            active_dialog_id = new_dialog;
            <?php endif; ?>
            // load dialogs when page ready
            loadDialogUsers();
            // set scheduled loader
            window.setInterval(loadDialogUsers, 15 * 1000);
            // callback for user onclick -> show dialogs
            $(document).on('click', '.media-person', function() {
                var selected_dialog_id = this.id.replace('msg-user-', '');
                if (selected_dialog_id === active_dialog_id) {
                    return false;
                }
                // set active id
                active_dialog_id = selected_dialog_id;
                $('.media-person').removeClass(cssUserList.active);
                $(this).addClass(cssUserList.active);
                // make msg body visible
                $('.message-scroll-body').removeClass('d-none');
                // set message streak title
                var current_user = user_object[selected_dialog_id];
                var dialogDom = dialogPanelDom.clone();
                // cleanup global id
                dialogDom.removeClass('d-none').removeAttr('id');
                // set user avatar in title panel
                dialogDom.find('#msg-user-avatar').attr('src', current_user.user_avatar).removeAttr('id');
                // set user nickname
                dialogDom.find('#msg-user-name').text(current_user.user_nick).removeAttr('id');
                // set user profile link
                dialogDom.find('#msg-user-link').attr('href', profile_link + '/' + current_user.user_id).removeAttr('id');
                $('#dialog-user-streak').html(dialogDom.html());
                // load 'now' dialog messages
                loadMessageDialog('now');
                $('.message-add-container').removeClass('d-none');
            });
            $(document).on('click', '#message-load-before', function(){
                loadMessageDialog('before');
            });
            // set schedule to show new messages
            window.setInterval(function(){loadMessageDialog('after')}, 15 * 1000);
            // if clicked "show more" - increase offset and load permamently
            $('#show-more-dialogs').on('click', function(){
                var obj = $(this);
                obj.addClass('disabled');
                setTimeout(function(){
                    obj.removeClass('disabled');
                }, 5000);
                dialog_offset += 1;
                loadDialogUsers();
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
                        loadMessageDialog('after');
                        $('#msg-text').val(null);
                    }
                }, 'json').done(function(){
                    if (active_dialog_id == new_dialog) {
                        new_dialog = 0;
                        loadMessageDialog('now');
                    }
                });
            });
            // send message by pressing enter
            $('#msg-text').keypress(function(e){
                if (e.which == 13) {
                    $('#send-new-message').focus().click();
                    $(this).focus();
                }
            });
        });
    });
</script>

<?php $this->stop() ?>