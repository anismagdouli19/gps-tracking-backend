

<div class="login-box">
<div class="login-border">
<div class="login-style">
	<div class="login-header">
		<div class="logo clear">
			<img src="<?php echo subdomain('assets_url')?>/terminator/images/logo_earth_bw.png" alt="" class="picture" />

			<span class="textlogo">
				<span class="title">VietTracker Backend</span>
				<span class="text">Version 1.0</span>
			</span>
		</div>
	</div>
		<div class="login-inside">
			<div class="login-data">
				<?php echo $this->backend->build($fields,array( array('type'=>'submit','title'=>'Login') ) );?>
			</div>
		</div>
</div>
</div>
</div>
