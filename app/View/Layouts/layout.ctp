<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title ?></title>
	
	<?php
		echo $this->Html->css(array('cake.generic.css','bootstrap.min.css', 'styles.css', 'sticky-footer.css'));
		echo $this->Html->script(array('jquery-2.1.3.min.js', 'canvasjs.js'));
	?>


</head>
<body>
	<div id="wrap">
		<header>
		<?php echo $this->element('navbar',
									array('menu'=>strtolower($this->params['controller'])));?> 
		</header>

		<div class="container">
			<?php
			echo $this->fetch('content');
			?>
		</div>
		
		<footer class='footer'>
		<?php
		echo $this->element('footer');
		?>
		</footer>
	</div>
</body>
</html>