<div class="content-box">
	<div class="box-body">
		<div class="box-header clear"><h2><?php echo $this->backend->pageTitle();?></h2></div>
		<div class="box-wrap clear">
		<table class="style1">
			<thead><tr>
				<th><?php echo $this->backend->button('Update','button',' class="button red  fl" id="update-user" ');?></th>
				<th class="full"></th><th></th>
			</tr>
			</thead><tbody>
			<?php foreach ($fields AS $key=>$value):?>
			<tr>
				<th><?php echo $this->lang->line( ucfirst($key) )?></th>
				<td class="edit-field edit-textfield long"><?php echo $value;?></td>
			</tr>
			<?php endforeach;?>
			</tbody></table>
		</div>
		<div class="box-wrap clear">
		<?php echo $this->backend->button('Add Vehicle','button',' class="button green  fl" id="add-device" ');?>
		<?php echo $this->backend->button('Add Tracking','button',' class="button green  fl" id="add-tracking" ');?>
		</div>
		<?php $this->load->view('modules/datatable',array('table'=>$table));?>
	</div>
</div>
<script type="text/javascript" charset="utf-8">
	$('#add-device').click(function(){
		window.location.href = vt.site+'vehicle/motor/add-new?user=<?php echo $this->input->get('user')?>';
	});
	$('#add-tracking').click(function(){
		window.location.href = vt.site+'vehicle/motor/add-tracking?user=<?php echo $this->input->get('user')?>';
	});
	$('#update-user').click(function(){
		window.location.href = vt.site+'user/manager/update/<?php echo $this->input->get('user')?>';
	});
</script>
