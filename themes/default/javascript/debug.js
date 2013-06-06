$(document).ready(function() {
	$('#devpanel').css('top',(- $('#devpanel').outerHeight()+20) );

	$('.debug-toggle').bind("click", function() {
		
		if($(this).hasClass("open")){
			$('#devpanel').animate({'top': (- $('#devpanel').outerHeight()+20) });
			$('.debug-toggle').removeClass('open')
		}else{
			$('#devpanel').animate({'top':'0px'});
			$('.debug-toggle').addClass('open');
		}
	});
});