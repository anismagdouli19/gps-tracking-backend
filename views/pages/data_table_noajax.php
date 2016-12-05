
<div class="main-wrap">
		<div class="page clear">
			<div class="content-box">
			<div class="box-body">
				<div class="box-header clear">
				<h2 class="fl" ><?php echo $this->backend->pageTitle();?></h2>
				<?php if(isset($actions)) echo '<div class="tabs" >'.$this->backend->showButtons($actions);?>
				</div>
			</div>
				<?php
$uri = uri_string();
$query='';
if($this->input->get()){
	foreach($this->input->get() AS $key=>$valQuery){
		$query.="&$key=$valQuery";
	}
}
$newItem = site_url($uri.'/add-new').(($query!='')?'?'.substr($query,1):'');

$getURL = current_url()."?format=json".( ($query!='')?'&'.substr($query,1):'' );
// exit('$getURL='.$getURL);

if(isset($controller)){
	$addGet='';
	if(isset($_GET) && count($_GET)>0){
		foreach($_GET AS $key=>$val)
			$addGet .= "&$key=$val";
	}
	//$url = ($addGet!='')?'?'.substr($addGet,1):'';

	echo UI::button($this->lang->line("$controller add new"),array('id'=>"item-add", 'href'=>$newItem));
	if(isset($otherButton)) echo $otherButton;
}

$column=$aoColumns=$aoColumnDefs='';
$tableContent='';
if($table){
	$i=0;
	foreach($table AS $key=>$info){
		$column.='<th></th>';
		$softTable = ($info[1]==TRUE)?",'bSortable': true":",'bSortable': false";
		$sClass = (isset($info[3])&& $info[3]!='')?",'sClass': '$info[3]' ":'';
		$aoColumns .="{'sTitle': '$info[0]' $softTable , 'sWidth': '$info[2]px' $sClass},";
		if($key=='content'){
			$aoColumnDefs .="{'aTargets': [$i], 'fnRender': function (oObj){return anchor(oObj.aData[$i],'".base_url($uri.'/update')."/'+oObj.aData[0]+'".(($query!='')?'?'.substr($query,1):'')."','','')+'<input type=\"hidden\" value=\"'+oObj.aData[1]+'\" >';}},";
		} else if($key=='actions'){
			$aoColumnDefs.="{ 'aTargets': [$i], 'fnRender': function ( oObj ){return tableView.tableActions(oObj.aData[0]);}},";
		} else if($key=='uactions'){
			$aoColumnDefs.="{ 'aTargets': [$i], 'fnRender': function ( oObj ){return tableView.tableUActions(oObj.aData[0]);}},";
		} else if($key=='public'){
			$aoColumnDefs.="{ 'aTargets': [$i], 'fnRender': function ( oObj ){return '<input type=\"checkbox\" checked=\"checked\" class=\"iphone\" />';}},";
		}
		$i++;
	}

	if($items){
		foreach($items AS $k=>$rowContent){
			$tableContent .= '<tr>'
							.'<td>'.$rowContent[0].'</td>';
			for($ii=1;$ii <= count($table)-2;$ii++){

				$tableContent .='<td>'.$rowContent[$ii].'</td>';
			}
			$tableContent.='<td class="center">'
					.$this->backend->tableButtonAction('edit')
					.$this->backend->tableButtonAction('remove')
				.'<input type="hidden" value="'.$rowContent[0].'">'
			.'</td></tr>';
		}
	}
// 	bug($items);exit;
}
?>
<div class="box-wrap clear">
	<div id="data-table" >
		<div class="dataTables_wrapper">
		<table  class="style1 tablesorter" id="datatable">
			<thead><tr><?php echo $column;?></tr></thead>
			<tbody><?php echo $tableContent;?></tbody><tfoot></tfoot>
		</table>
		</div>
	</div>
</div>



<script type="text/javascript" charset="utf-8">
var tableconfig = {
        "bProcessing": true,"bServerSide": false, "sPaginationType": "full_numbers", "aaSorting": [],
        //"bFilter": false,
        "aoColumns": [<?php echo $aoColumns;?>], "aoColumnDefs":	[<?php echo $aoColumnDefs;?>],

 };
//jQuery('#datatable').dataTable().fnDestroy();
$(document).ready(function() {
	var oTable = $('#datatable').dataTable(tableconfig);
	tableView.actionEvents();
});
var tableView = {
		urlAction:function(action,id){

			var uri="<?php echo $uri;?>";
			if(id)
				url = vt.site+ uri + ((action)?"/"+action+"/"+id:'') + "<?php echo (($query!='')?'?'.substr($query,1):'')?>";
			else
				url = vt.site+ uri + ((action)?"/"+action+"/":'') + "<?php echo (($query!='')?'?'.substr($query,1):'')?>";
			return url;
		},
		urlActionMotorUpdate: function(action,id){
			var uri = 'vehicle/motor/update/';
		    url = vt.site+ uri + "/"+id + "<?php echo (($query!='')?'?'.substr($query,1):'')?>";
		},
		tableActions:function(id){
			view = '<?php echo $this->backend->tableButtonAction('edit');?>';
			view+= '<?php echo $this->backend->tableButtonAction('remove');?>';
			view+= '<input type="hidden" value="'+id+'">';
			return view;

		},
		tableUActions:function(id){
			view = '<?php echo $this->backend->tableButtonAction('edit');?>';
			view+= '<?php echo $this->backend->tableButtonAction('remove');?>';
			view+= '<?php echo $this->backend->tableButtonAction('changepass');?>';
			view+= '<input type="hidden" value="'+id+'">';
			return view;
		},
		actionEvents:function(){
			$('#datatable button').click(function(){
				$id = $(this).parent().find('input[type=hidden]').val();
				if($(this).hasClass('item-edit')){
					window.location.href =  tableView.urlActionMotorUpdate('update',$id);
				} else if($(this).hasClass('item-changepass')){
					window.location.href =  tableView.urlAction('changepass',$id);
				}else if($(this).hasClass('item-remove')){
					tableView.publish($(this),-1);
				} else if($(this).hasClass('item-publish')){
					tableView.publish($(this),0);
				} else if($(this).hasClass('item-unpublish')){
					tableView.publish($(this),1);
				} else if($(this).hasClass('item-new-car')){
					window.location.href =  site_url+ 'vehicle/motor/add-new?user='+$id;
				}
			});
			$('button[action=add-new]').click(function(){
				window.location.href =  url = tableView.urlAction('add-new');
			});
		},
		publish:function(e,value,action){
			var uri="<?php echo $uri;?>";
			var tr = e.parents('tr');
			var data = {
				"<?php echo $this->security->get_csrf_token_name();?>":"<?php echo $this->security->get_csrf_hash();?>",
				"<?php echo $this->form->protection('id');?>":$('td:nth-child(1)',tr).html(),
				"<?php echo $this->form->protection('publish');?>":value,
			};
			action = (action)?action:'publish';
			$.ajax({
				url: site_url+uri+'/'+action+"<?php echo (($query!='')?'?'.substr($query,1):'')?>",
				type: "POST",data: data,  dataType: "json",
				success: function(data){
					if(data.hasOwnProperty('action') && data.action==true){
						if(value==1){
							e.removeClass('item-unpublish').addClass('item-publish');
						} else if(value ==0) {
							e.removeClass('item-publish').addClass('item-unpublish');
						} else if(value == -1){
								tr.remove(); return;
						}

					}
				},
			});
		},
	};
</script>
			</div>
		</div>
</div>

