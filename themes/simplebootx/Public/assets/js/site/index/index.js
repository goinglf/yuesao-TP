$(function(){
	$('.log_title .control').hover(
		function () {
			$(this).addClass('on').siblings().removeClass('on');
			$('.abcb').eq($(this).index()).addClass('on').siblings().removeClass('on');
		},
		function () {
			
		}
	);
});