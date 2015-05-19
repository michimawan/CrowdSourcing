<?php
	
	$banyaklabel = $json->n;
	$total = $comments*$banyaklabel;
	$labels = $labels/$total * 100;
	$belum = ($total-$labels) / $total *100;
?>
<div class='hidden' id='data' attribute="<?php echo $labels.'-'.$belum ?>"></div>
<div>
	<div class='col-xs-7'>
		<h2>Selamat Datang,</h2>
		<h2>Portal Crowd Sourcing Facebook</h2>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec egestas, erat ut efficitur condimentum, turpis erat ultrices nibh, sed cursus nulla tellus in neque. Nullam ac vestibulum elit, ut suscipit ipsum. Donec non ipsum fermentum, ultricies neque vitae, vestibulum ante. Donec non quam dignissim, hendrerit mi non, euismod mauris. Etiam eu fringilla elit. Vivamus vel metus hendrerit, mattis ex ut, tempus neque. Sed vestibulum eros id luctus posuere. Mauris tortor sem, faucibus et venenatis consequat, interdum in ligula. Pellentesque vel eros eget enim pellentesque convallis. Morbi tempor orci non congue placerat. Pellentesque a tempus nibh. Phasellus at arcu vel ligula euismod aliquam. Suspendisse sapien sem, finibus ut sem nec, consectetur sollicitudin libero. Morbi lectus ante, congue eu ullamcorper vitae, suscipit vel nisi. 
		</p>
	</div>
	<div class='prosentase col-xs-5'>
		<div id="chart" style="height: 200px; width: 100%;"></div>
		
		
	</div>
</div>

	<?php
	echo $this->element('chartindex');
	?>
	