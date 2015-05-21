<script type="text/javascript">
	window.onload = function () {
		var a = $("#data").attr('attribute');
		
		draw(a);	
	}

	function draw(datas){
	
		var data = datas.split("-");

		var chart = new CanvasJS.Chart("chart",
		{
			title:{
				text: ""
			},
		    animationEnabled: true,
			legend:{
				verticalAlign: "bottom",
				horizontalAlign: "center",
				fontSize: 15,
				fontFamily: "Helvetica"        
			},
			theme: "theme2",
			data: [
			{        
				type: "pie",       
				indexLabelFontFamily: "Garamond",       
				indexLabelFontSize: 20,
				indexLabelFontWeight: "bold",
				indexLabelFontColor: "MistyRose",       
				indexLabelLineColor: "darkgrey", 
				indexLabelPlacement: "inside",
				indexLabel: "{y}%",
				startAngle:-20,      
				showInLegend: true,
				toolTipContent:"{legendText} {y}%",
				dataPoints: [
					{  y: data[0], legendText:"Belum", label: "Belum" },
					{  y: data[1], legendText:"Terlabeli", label: "Terlabeli" }
				]
			}
			]
		});
		chart.render();
	}
	
</script>