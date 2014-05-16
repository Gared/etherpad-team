<script type="text/javascript">
	function setIframeHeight() {
	    var height = $(window).height();
	    var footerHeight = $('#footer').height();
	    var iframeOffset = $('#etherpad_frame').offset().top;
	    $('#etherpad_frame').css('height', (height - footerHeight - iframeOffset) * 0.95 | 0);
	}
	$(document).ready(setIframeHeight);
	$(window).resize(setIframeHeight);
</script>
<iframe id="etherpad_frame" src="<?php echo $url; ?>" name="ep_view">
<?php echo __("Your browser doesn't support iframe's"); ?>
</iframe>