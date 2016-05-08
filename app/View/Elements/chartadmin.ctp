<script type="text/javascript">
	window.onload = function () {
		var a = $("#dataPoint").attr('attribute');
		draw(a);
	}

	function draw(datas){

		var data = datas.split("-");
		if(data[3] == 0){

			var chart = new CanvasJS.Chart("chart",
			{
				title:{
					text: "Prosentase Label"
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
					indexLabelFontFamily: "Helvetica",
					indexLabelFontSize: 20,
					indexLabel: "{label} {y}%",
					startAngle:-20,
					showInLegend: false,
					toolTipContent:"{legendText} {y}%",
					dataPoints: [
						{  y: data[2], legendText:"Netral", label: "Netral" },
						{  y: data[0], legendText:"Positif", label: "Positif" },
						{  y: data[1], legendText:"Negatif" , label: "Negatif"}
					]
				}
				]
			});

		} else
		var chart = new CanvasJS.Chart("chart",
		{
			title:{
				text: "Prosentase Label"
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
				indexLabelFontFamily: "Helvetica",
				indexLabelFontSize: 20,
				indexLabel: "{label} {y}%",
				startAngle:-20,
				showInLegend: false,
				toolTipContent:"{legendText} {y}%",
				dataPoints: [
					{  y: data[3], legendText:"Belum", label: "Belum" },
					{  y: data[2], legendText:"Netral", label: "Netral" },
					{  y: data[0], legendText:"Positif", label: "Positif" },
					{  y: data[1], legendText:"Negatif" , label: "Negatif"}
				]
			}
			]
		});
		chart.render();
	}

</script>
