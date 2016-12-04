

<div class="login-box">
<div class="login-border">
<div class="login-style">
	<div class="login-header">
		<div class="logo clear">
			<img src="<?php echo subdomain('assets_url')?>/images/logo_earth_bw.png" alt="" class="picture" />

			<span class="textlogo">
				<span class="title">GSP Tracking Backend</span>
				<span class="text">demo version</span>
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
