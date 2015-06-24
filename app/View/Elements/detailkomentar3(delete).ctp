<?php
if(strtolower($this->params['controller']) === 'statuses') {
?>

$(".detail").click(function(event){
	event.preventDefault();
	var idkomen = $(this).attr('id');

	$.ajax({
		dataType:"html", 
        success:function (data) {
        	var obj = JSON.parse(data);
        	
        	$('#labels'+idkomen).slideToggle();
	        buatElement(obj, idkomen);

	        if($('#'+idkomen).text() == 'lihat label')
				$('#'+idkomen).text('sembunyikan label');
			else
				$('#'+idkomen).text('lihat label');
        }, 
        type:"get",
        url:'<?php echo $this->Html->url(array('action'=>'detail')); ?>/' +  idkomen
    });
	
});

function buatElement(obj, idkomen){
	var i = 0;
	var pos = neg = net = 0;

	var arPos = arNeg = arNet = "";

	for (x in obj){
		for(y in obj[x])
			if(obj[x][y].constructor === Array){
				for(z in obj[x][y]){
					if(obj[x][y][z]['nama_label'] == 'positif'){
						pos++;
						if(arPos.length == 0){
							arPos += obj[x][y][z]['username_pelabel'];
						}
						else{
							arPos += "<br>"+obj[x][y][z]['username_pelabel'];
						}
						
					}
					else if(obj[x][y][z]['nama_label'] == 'negatif'){
						neg++;
						if(arNeg.length == 0){
							arNeg += obj[x][y][z]['username_pelabel'];
						}
						else{
							arNeg += "<br>"+obj[x][y][z]['username_pelabel'];
						}
					}
					else if(obj[x][y][z]['nama_label'] == 'netral'){
						net++;
						if(arPos.length == 0){
							arNet += obj[x][y][z]['username_pelabel'];
						}
						else{
							arNet += "<br>"+obj[x][y][z]['username_pelabel'];
						}
					}
				}
			}
	}

	$('#summary'+idkomen).children('li:first').children('span').html(pos);
	$('#summary'+idkomen).children('li:first').children('p').html(arPos);

	$('#summary'+idkomen).children('li:nth-child(even)').children('span').html(net);
	$('#summary'+idkomen).children('li:nth-child(even)').children('p').html(arNet);
	
	$('#summary'+idkomen).children('li:last').children('span').html(neg);
	$('#summary'+idkomen).children('li:last').children('p').html(arNeg);
}

$('li.list-group-item').click(function(){
	$('li > p:visible').slideToggle();
	$(this).children('p').slideToggle();
	return false;

});

<?php	
}
?>