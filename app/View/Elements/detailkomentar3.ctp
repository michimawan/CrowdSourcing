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
	var res = "";
	var i = 0;
	var pos = neg = net = 0;

	/*
	for (x in obj){
		for(y in obj[x])
			if(obj[x][y].constructor === Array){
				for(z in obj[x][y]){
					if(obj[x][y][z]['nama_label'] == 'positif')
						pos++;
					else if(obj[x][y][z]['nama_label'] == 'negatif')
						neg++;
					else if(obj[x][y][z]['nama_label'] == 'netral')
						net++;
					

					res += buatDetail(obj[x][y][z]['username_pelabel'], obj[x][y][z]['nama_label']);
				}
			}
	}
	*/

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
						
					

					//res += buatDetail(obj[x][y][z]['username_pelabel'], obj[x][y][z]['nama_label']);
				}
			}
	}

	var puts = "<li class='list-group-item'><span class='badge'>" + pos + "</span><h4 class='list-group-item-heading'>Positif</h4><p class='dispnone'>"+arPos+"</p></li>";
	puts += "<li class='list-group-item'><span class='badge'>" + net + "</span><h4 class='list-group-item-heading'>Netral</h4><p class='dispnone'>"+arNet+"</p></li>";
	puts += "<li class='list-group-item'><span class='badge'>" + neg + "</span><h4 class='list-group-item-heading'>Negatif</h4><p class='dispnone'>"+arNeg+"</p></li>";
	$('#summary'+idkomen).append(puts);
	
	//$('#summary'+idkomen).children('li').children('p').append(res);
	//$('#summary'+idkomen).children('li').append(res);
	

	//$('#detailLabels'+idkomen).html(res);
}

$('li').click(function(){
	//$(this).children('p').slideToggle();
	console.log('clicked');
	$('li > p:visible').slideToggle();
	$(this).children('p').slideToggle();
	return false;

});

function buatDetail(nama, label){
	var res = '';

	res += "<li class='list-group-item'>";
	res += "<h5 class='list-group-item-heading'>"+ nama +"</h5>";
    res += "<p class='list-group-item-text'>"+ label +"</p>";
  	res += "</li>";

	return res;
}

$(".btn-default").click(function(event){
	var idkomen = $(this).attr('attr');
	$('#detailLabels'+idkomen).slideToggle();	
	
   	if($(this).find("span").hasClass("glyphicon glyphicon-chevron-down")){
   		$(this).find("span").removeClass("glyphicon glyphicon-chevron-down");
   		$(this).find("span").addClass("glyphicon glyphicon-chevron-up");
   	} else {
		$(this).find("span").removeClass("glyphicon glyphicon-chevron-up");
   		$(this).find("span").addClass("glyphicon glyphicon-chevron-down");
	}
});
<?php	
}
?>