<?php 
//http://preview.ait-themes.com/terminator/tables.html
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head><?php $this->load->view('template/head');?></head>
<body>
<?php 
 if($this->session->userdata('uid')){
 	$this->load->view('template/head-top');
 }
?>
<div class="main pagesize"><div class="main-wrap"><div class="page clear">
	<?php 
		if($this->msg){
			echo $this->backend->notification($this->msg);
		}
		if ($content) echo "<div class='clearfix wrappage container' >$content</div>";
	?>
</div></div></div>
</body>
</html>
