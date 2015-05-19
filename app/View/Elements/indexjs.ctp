$('.setting > img').click(function(){
	$('.settings').slideToggle();
});

$('.submit').click(function(){
	
	var harga = $('#hargaperlabel').val();
	var labels = $('#labelperkomen').val();
	if(harga > 0 && labels > 0){
		var combine = harga + ' ' + labels;

		$.ajax({
			dataType:"html",
			success: function(result){
				$('.payment').each(function(i, obj) {
				    var now = $(this).html();
				    var priceold = $(this).attr('prices');
				    now = now / priceold;
				    now = now * harga;
				    $(this).html(now);
				    $(this).attr('prices', harga);

				});
				$('.settings').slideToggle();

				var data = $("#dataPoint").attr('attribute');
				var datas = setdatapoint(data, labels);
				
				draw(datas);

				alert('setting sudah diupdate');
		    },
		    type:"get",
	        url:'<?php echo $this->Html->url(array('action'=>'changesetting')); ?>/' +  combine
		});
	} else {
		alert('maaf, minimum nilai harga dan jumlah label adalah 1');
	}

	
});

function setdatapoint(data, label){

	data = data.split("-");
	var pos = data[0];
	var neg = data[1];
	var net = data[2];
	var belum = data[3];
	var labelold = data[4];
	var komen = data[5];

	var totallama = labelold * komen;

	pos = Math.round(pos * totallama / 100);
	neg = Math.round(neg * totallama / 100);
	net = Math.round(net * totallama / 100);
	
	belum = (label*komen)-(pos+neg+net);
	
	$('.information > h3:last').html('Data belum terlabel: ' + belum);

	var totalbaru = label * komen;
	pos = pos / totalbaru * 100;
	neg = neg / totalbaru * 100;
	net = net / totalbaru * 100;
	belum = belum / totalbaru * 100;

	var str = pos + '-' + neg + '-' + net + '-' + belum;
	return str;
}