<?php
if(strtolower($this->params['controller']) === 'statuses') {
?>

$('li').click(function(){
	var data = $(this).attr('attr');
	if($('#'+data).css('display') == 'block')
		$('.panel.panel-default:visible').slideToggle();
	else{
		$('.panel.panel-default:visible').slideToggle();
		$('#'+data).slideToggle();
	}
	
});

<?php	
}
?>