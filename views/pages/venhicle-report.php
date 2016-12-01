
<div class="content-box">
	<div class="box-body">
		<div class="box-header clear"><h2><?php echo $this->backend->pageTitle();?></h2></div>
		<div class="box-wrap clear">
		<?php if(isset($fields) && is_array($fields)):?>
		<table class="style1">
			<thead><tr>
				<th></th>
				<th class="full"></th><th></th>
			</tr>
			</thead><tbody>
			<?php foreach ($fields AS $key=>$value):?>
			<tr>
				<th><?php echo $this->lang->line( 'Venhicle Report '.ucfirst($key) )?></th>
				<td class="edit-field edit-textfield long"><?php echo $value;?></td>
			</tr>
			<?php endforeach;?>
			</tbody></table>
		</div>
		<?php else:?>
		Khong co du lieu
		<?php endif;?>
		<div class="box-wrap clear">
		</div>
	</div>
</div>
