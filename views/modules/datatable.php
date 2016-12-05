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
}
?>
<div class="box-wrap clear">
	<div id="data-table" >
		<div class="dataTables_wrapper">
		<table  class="style1 tablesorter" id="datatable">
			<thead><tr><?php echo $column?></tr></thead>
			<tbody></tbody><tfoot></tfoot>
		</table>
		</div>
	</div>
</div>



<script type="text/javascript" charset="utf-8">
var tableconfig = {
        "bProcessing": true,"bServerSide": true, "sPaginationType": "full_numbers",
		<?php if( isset($DisplayLength) && $DisplayLength) echo '"iDisplayLength": '.$DisplayLength.','; ?>

        "aoColumns": [<?php echo $aoColumns;?>], "aoColumnDefs":	[<?php echo $aoColumnDefs;?>], "fnServerData": function ( sSource, aoData, fnCallback ) {
        	jQuery.each(aoData, function(i, item) {
        	    $.each(item, function(ii, itemSub) {
            	    if(ii=='name'){
                	    if(itemSub!='<?php echo $this->security->get_csrf_token_name()?>')
            	    	aoData[i][ii] = '<?php echo $this->form->protection()?>'+itemSub;
                	}

            	});
        	});
			jQuery.ajax( {"dataType": 'json',"type": "POST","url": "<?php echo $getURL;?>","data": aoData,
	            success: function(data) { fnCallback(data); tableView.actionEvents(); }
            });
    	}
 };
tableconfig['fnServerParams']=function ( aoData ) {aoData.push( { "name": '<?php echo $this->security->get_csrf_token_name();?>', "value": '<?php echo $this->security->get_csrf_hash();?>' } );};
jQuery('#datatable').dataTable().fnDestroy();
$(document).ready(function() {
	var oTable = $('#datatable').dataTable(tableconfig);
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
		    return url;
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
				    if($(this).hasClass('motor-edit')){
					   window.location.href =  tableView.urlActionMotorUpdate('update',$id);
					} else {
					    window.location.href =  tableView.urlAction('update',$id);
					}


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
				} else if($(this).hasClass('item-report')){
					window.location.href =  $(this).attr('href');
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
				url: vt.site+uri+'/'+action+"<?php echo (($query!='')?'?'.substr($query,1):'')?>",
				type: "POST",data: data,  dataType: "json",
				success: function(data){
					if(data.hasOwnProperty('action') && data.action==true){

						//alert( e.attr('class') );
						if(value==1){
							e.removeClass('item-unpublish').addClass('item-publish');
							//e.removeClass('item-publish').addClass('item-unpublish');
							//e.children('.ui-icon').removeClass('ui-icon-locked').addClass('ui-icon-unlocked');
						} else if(value ==0) {
							e.removeClass('item-publish').addClass('item-unpublish');
							//e.children('.ui-icon').addClass('ui-icon-locked').removeClass('ui-icon-unlocked');
						} else if(value == -1){
								tr.remove(); return;
						}

					}
				},
			});
		},
	};
</script>