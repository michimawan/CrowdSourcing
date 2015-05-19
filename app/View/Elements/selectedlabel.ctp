function selectedlabel(){
	var label = $('#selected').attr('attr');
	
	if(label == 'positif') {
		addCss('btn-primary');
	} else if(label == 'netral'){
		addCss('btn-warning');
	} else if(label == 'negatif'){
		addCss('btn-danger');
	}
}

function addCss(btn){
	$('.'+btn).css('border', '5px solid red');
}