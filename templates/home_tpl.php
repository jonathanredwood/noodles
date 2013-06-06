<?php echo $copy ?>


<script>
$(function() {
	$(".slider").slider({
		orientation: "vertical",
		range: "min",
		min: 0,
		max: 100,
		value: 0,
		slide: function( event, ui ) {
			$.ajax({
				url: '?'+$(this).attr('data-id')+'='+ui.value
			});
		}
	});

});
</script>

<div data-id="A" class="slider" id="slider1"></div>
<div data-id="B"  class="slider" id="slider2"></div>
<div data-id="C"  class="slider" id="slider3"></div>

<div><a href="?tides=1">Use Tide</a></div>