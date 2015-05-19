<div class="navbar navbar-default navbar-fixed-top">
  	<div class="container">
    	<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
			</button>
			<!-- menampilkan judul dr web -->
			<div>
				<span class="navbar-brand">Crowd Sourcing Facebook</span>
			</div>
		</div>

		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav right">
				<li><a class="login" href="'.$authUrl.'"><?php echo $this->Html->image('sign-in-button.png', array('alt' => 'Login with G+')); ?></a></li>
			</ul>
		</div>
  	</div>
</div>