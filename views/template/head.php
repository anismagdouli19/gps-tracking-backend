<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head><script type="text/javascript">var NREUMQ=[];NREUMQ.push(["mark","firstbyte",new Date().getTime()]);</script>
<title><?php echo $title;?></title>
<meta name="Description" content="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
if(isset($scripts_head) && $scripts_head !='' ){
	echo '<script type="text/javascript">'.$scripts_head.'</script>';
} 
//echo bug($scripts_head); exit;
?>
<?php echo ($_scripts) ?>
<?php echo  $_styles ?>
<script type="text/javascript"><?php echo $scripts.$scripts_ready;?>;</script>

