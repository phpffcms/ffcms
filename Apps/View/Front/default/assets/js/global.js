/**
 * Show notifications in webpage title
 * @param num int
 */
var setNotificationNumber = function (num)
{
    if (isNaN(num)) {
        return;
    }
    var regx = /^\@\(\d*\+?\)-> /;
    if (num < 1) {
        document.title = document.title.replace(regx, '');
        return;
    }

    if (regx.exec(document.title)) {
        document.title = document.title.replace(regx, "@(" + num + ")-> ");
    } else {
        document.title = "@(" + num + ")-> " + document.title;
    }
};

// check if $text is valid email
var validator_email = function(text) {
    let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(text);
}
// check if pwd is valid
var validator_pwd = function(text) {
    let status = true;
    if (text.length < 8)
        status = false;
    
    if (text.toLowerCase() == text)
        status = false;

    rx = /[^0-9]/;
    if (!rx.test(text))
        status = false;
    
    return status;
}

// cookie features - set&get
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// jquery features
$(document).ready(function(){
    // notification function for user pm count block (class="pm-count-block")
    var loadPmInterval = false;
    var summaryBlock = $('#summary-count-block');
    var msgBlock = $('#pm-count-block');
    var notifyBlock = $('#notify-count-block');
    function ajaxNotify() {
        $.getJSON(script_url+'/api/profile/notifications?lang='+script_lang, function(resp){
            if (resp.status === 1) {
                if (resp.summary > 0) {
                    summaryBlock.addClass('alert-danger', 1000).text(resp.summary);
                    // set new messages count
                    if (resp.messages > 0) {
                        msgBlock.text(resp.messages).addClass('alert-danger', 1000);
                    } else {
                        msgBlock.removeClass('alert-danger', 1000).text(0);
                    }
                    // set new notifications count
                    if (resp.notify > 0) {
                        notifyBlock.text(resp.notify).addClass('alert-danger', 1000);
                    } else {
                        notifyBlock.removeClass('alert-danger', 1000).text(0);
                    }
                } else {
                    summaryBlock.removeClass('alert-danger', 1000).text(0);
                }
                setNotificationNumber(resp.summary);
            } else if (loadPmInterval !== false) { // remove autorefresh
                clearInterval(loadPmInterval);
            }
        }).fail(function(){
            if (loadPmInterval !== false) {
                clearInterval(loadPmInterval);
            }
        });
    }

    // instantly run counter
    ajaxNotify();

    // show cookie toast if cookie not setup
    if (getCookie('ffcms-cookie').length < 1) {
        const toastElem = document.getElementById('cookieNotification')
        const toast = new bootstrap.Toast(toastElem)
        toast.show();
    }
    // if ok - set cookie and close window
    $('#cookieOk').on('click', function(e){
        setCookie('ffcms-cookie', 'agree');
        const toastElem = document.getElementById('cookieNotification')
        const toast = new bootstrap.Toast(toastElem)
        toast.hide();
    });

    // make autorefresh every 10 seconds
    loadPmInterval = setInterval(ajaxNotify, 10000);
    var timer = 0;
    // make live search on user keypress in search input
    $('#searchInput').keypress(function(e){
        // bind key code
        var keycode = ((typeof e.keyCode != 'undefined' && e.keyCode) ? e.keyCode : e.which);
        // check if pressed ESC button to hide dropdown results
        if (keycode === 27) {
            $('#ajax-result-container').addClass('d-none');
            return;
        }
        // define timer on key press delay to execute
        if (timer) {
            clearTimeout(timer);
        }
        timer = setTimeout(makeSearch, 500);
    });
    // detect search cancel by pushing esc key
    $('#searchInput').keydown(function(e){
        e = e || window.event;
        var isCanceled = false;
        // browser 2017 feature for esc key
        if ("key" in e) {
            isCanceled = (e.key == "Escape" || e.key == "Esc");
        } else {
            isCanceled = e.keyCode == 27;
        }
        if (isCanceled) {
            // if escape pushed - remove search results & cleanup search input
            $('#ajax-result-container').addClass('d-none');
            $(this).val('');
        }
    });
    
    /**$('#searchInput').focusout(function(e){
        $('#search-popup').addClass('d-none');
    });*/

    // execute search query by defined timer
    function makeSearch() {
        var query = $('#searchInput').val();
        if (query.length < 2) {
            return null;
        }

        // cleanup & make AJAX query with building response
        $('#ajax-result-items').empty();
        $.getJSON(script_url+'/api/search/index?query='+query+'&lang='+script_lang, function (resp) {
            if (resp.status !== 1 || resp.count < 1)
                return;

            let searchContainer = $('#search-popup');
            let searchList = searchContainer.find('#search-list').empty();
            $.each(resp.data, function(id, item) {
                searchList.append('<a href="' + site_url + item.uri + '" class="list-group-item list-group-item-action">' + item.title + '</a>');
            });
            searchContainer.removeClass('d-none');
        });
    }
});