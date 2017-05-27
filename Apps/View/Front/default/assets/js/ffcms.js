var audioCtx = new (window.AudioContext || window.webkitAudioContext || window.audioContext);
// use web audio context api to play sound of notification
var beep = function(duration, frequency, volume, type, callback) {
    var oscillator = audioCtx.createOscillator();
    var gainNode = audioCtx.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    if (volume){gainNode.gain.value = volume;}
    if (frequency){oscillator.frequency.value = frequency;}
    if (type){oscillator.type = type;}
    if (callback){oscillator.onended = callback;}

    oscillator.start();
    setTimeout(function(){oscillator.stop()}, (duration ? duration : 500));
};

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

// jquery features
$(document).ready(function(){
    // notification function for user pm count block (class="pm-count-block")
    var loadPmInterval = false;
    var soundPlayed = false;
    var summaryBlock = $('#summary-count-block');
    var msgBlock = $('#pm-count-block');
    var notifyBlock = $('#notify-count-block');
    function ajaxNotify() {
        $.getJSON(script_url+'/api/profile/notifications?lang='+script_lang, function(resp){
            if (resp.status === 1) {
                if (resp.summary > 0) {
                    // play sound notification
                    if (!soundPlayed) {
                        // todo: code here
                        beep(150, 150, 0.6, "triangle");
                        soundPlayed = true;
                    }
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
    // make autorefresh every 10 seconds
    loadPmInterval = setInterval(ajaxNotify, 10000);
    var timer = 0;
    // make live search on user keypress in search input
    $('#search-line').keypress(function(e){
        // bind key code
        var keycode = ((typeof e.keyCode != 'undefined' && e.keyCode) ? e.keyCode : e.which);
        // check if pressed ESC button to hide dropdown results
        if (keycode === 27) {
            $('#ajax-result-container').addClass('hidden');
            return;
        }
        // define timer on key press delay to execute
        if (timer) {
            clearTimeout(timer);
        }
        timer = setTimeout(makeSearch, 1000);
    });
    // detect search cancel by pushing esc key
    $('#search-line').keydown(function(e){
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
            $('#ajax-result-container').addClass('hidden');
            $(this).val('');
        }
    });

    // execute search query by defined timer
    function makeSearch() {
        var query = $('#search-line').val();
        if (query.length < 2) {
            return null;
        }

        // cleanup & make AJAX query with building response
        $('#ajax-result-items').empty();
        $.getJSON(script_url+'/api/search/index?query='+query+'&lang='+script_lang, function (resp) {
            if (resp.status !== 1 || resp.count < 1)
                return;
            var searchHtml = $('#ajax-carcase-item').clone().removeClass('hidden');
            $.each(resp.data, function(relevance, item) {
                var searchItem = searchHtml.clone();
                searchItem.find('#ajax-search-link').attr('href', site_url + item.uri);
                searchItem.find('#ajax-search-title').text(item.title);
                searchItem.find('#ajax-search-snippet').text(item.snippet);
                $('#ajax-result-items').append(searchItem.html());
                searchItem = null;
            });
            $('#ajax-result-container').removeClass('hidden');
        });
    }
});