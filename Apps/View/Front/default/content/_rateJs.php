<script>
window.jQ.push(function(){
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

	// if user vote new rating for item
	$('.change-rate').click(function() {
        // get content item id
        var id = this.id.replace('content-', '');
        if (id < 0) {
        	return null;
        }
        
    	// hide vote element for current content id
        $('.hide-rate-'+id).hide();
        
    	// get type of rating vote
    	if (this.className.indexOf('minus') > 0) {
        	$.fn.changeRating('minus', id);
        } else if(this.className.indexOf('plus') > 0) {
        	$.fn.changeRating('plus', id);
        }
    });

	// change rating ajax function
	$.fn.changeRating = function(type, contentId) {
		// change rating function definition
	   	$.getJSON(script_url + '/api/content/changerate/' + type + '/' + contentId + '?lang='+script_lang, function(json){
			// check if response is success
		    if (json.status !== 1) {
		    	alert(json.message);
		    	return null;
		    }
		    
		    var newRate = json.rating;
		    if (newRate > 0) {
		    	newRate = '+'+newRate;
		    }
		    // set new rate count to DOM
		    $('#rate-value-'+contentId).text(newRate);
		});
	};
});
</script>