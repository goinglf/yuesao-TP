$(function(){
	
	$('.log_in_title_on').click(function(){
//		this.classList.add('active');
		$(this).addClass('active').siblings().removeClass('active');
		
		$('.f__tab').eq($(this).index()).addClass('active').siblings().removeClass('active');
	});
	
	$('form input[type="checkbox"]').click(function(){
		console.log( $('#btnRegister').attr('disabled') );
		if ($('#btnRegister').attr('disabled')) {
			$('#btnRegister').removeAttr('disabled')
		}else {
			$('#btnRegister').attr('disabled', '')
		}
	});
});
