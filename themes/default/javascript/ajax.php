<?php header('Content-type: text/javascript'); ?>

var firstrun = true;
var element = $('#<?php echo $_GET['element'] ?>');
var sticky = <?php if(isset($_GET['sticky'])){ echo $_GET['sticky']; }else{ echo 'false';} ?>;
var repeat = <?php if(isset($_GET['repeat'])){ echo $_GET['repeat']; }else{ echo 'false';} ?>;
var period = <?php if(isset($_GET['period'])){ echo $_GET['period']; }else{ echo '500';} ?>;
var url = '<?php echo $_GET['address'] ?>';

$(document).ready(function() {
	if(sticky){
	   	gotoBottom();
	}
	if (repeat) {
		if(firstrun){
			getContent(url);
		}
		setInterval ("getContent(url)", period);
	} else {
		getContent(url);
	}
});


function getContent(url){
	$.ajax({
	  url: url,
	  success: function(data) {
	    $('#<?php echo $_GET['element'] ?>').html(data);
	    if(sticky){
	   		gotoBottom();
	   		if(firstrun){
	   			$('#<?php echo $_GET['element'] ?>').scrollTop( 999999 );
	   			firstrun = false;
	   		}
	   	}
	  }
	});
}

function gotoBottom(){
	var scrolltop = $('#<?php echo $_GET['element'] ?>').scrollTop()
	var scrollheight = $('#<?php echo $_GET['element'] ?>')[0].scrollHeight
	var height = $('#<?php echo $_GET['element'] ?>').height()

	if( scrollheight - (scrolltop + height) < 50 ){
		$('#<?php echo $_GET['element'] ?>').scrollTop( 999999);
	}

}