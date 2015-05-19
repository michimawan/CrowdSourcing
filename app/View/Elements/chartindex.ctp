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
				verticalAlign: "center",
				horizontalAlign: "left",
				fontSize: 20,
				fontFamily: "Helvetica"        
			},
			theme: "theme2",
			data: [
			{        
				type: "pie",       
				indexLabelFontFamily: "Garamond",       
				indexLabelFontSize: 20,
				indexLabel: "{label} {y}%",
				startAngle:-20,      
				showInLegend: false,
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