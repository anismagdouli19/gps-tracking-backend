<div class="content-box"><div class="box-body">
	<div class="box-header clear">
		<h2><?php echo 	$this->backend->pageTitle()?></h2>
	</div>
	<div class="box-wrap clear">
		<div class="columns clear bt-space15" ><?php echo $this->backend->build($form,(isset($buttons)?$buttons:null) );?></div>
	</div>
</div></div>