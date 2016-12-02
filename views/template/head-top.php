<?php
$user = $this->backend->getSystemInfo();
?>
<div class="pagetop">
	<div class="head pagesize">
		<div class="head_top">
			<div class="topbuts">
				<ul class="clear">
				<li><a href="//viettracker.vn">View Site</a></li>
				<li><?php echo anchor('account/info',lang('Settings'))?></li>
				<li><a class="red" href="<?php echo site_url('account/logout');?>">Logout</a></li>
				</ul>

				<div class="user clear">
					<img alt="" class="avatar" src="<?php echo subdomain('assets_url')?>/images/avatar.jpg">
					<span class="user-detail">
						<span class="name"><?php echo anchor('account/info',$user->fullname)?></span>
						<span class="text">Logged as admin</span>
						<span class="text" style="display: none;">You have <a href="#">5 messages</a></span>
					</span>
				</div>
			</div>

			<div class="logo clear">
			<a title="View dashboard" href="index.html">
				<img class="picture" alt="" src="<?php echo subdomain('assets_url')?>/images/logo_earth.png">
				<span class="textlogo">
					<span class="title"><?php echo $this->config->item('site-name');?></span>
					<span class="text"><?php echo $this->config->item('version');?></span>
				</span>
			</a>
			</div>
		</div>

		<div class="menu">
			<ul class="clear">
			<li><?php echo anchor('',$this->lang->line('Home') );?></li>
			<li>
				<?php echo anchor('vehicle/motor','Thiết Bị GPS');?>
				<ul style="visibility: hidden; display: block;">
					<li><?php echo anchor('vehicle/motor',lang('Motor Management') );?></li>
					<li><?php echo anchor('vehicle/car',lang('Car Management') );?></li>
					<li><?php echo anchor('user/manager',lang('User Management') );?></li>
					<li><?php echo anchor('vehicle/fuel',lang('Fuel Price') );?></li>
				</ul>
			</li>
			<li><?php echo anchor('article/manager','Quản Lý Bài Viết' );?>
				<ul style="visibility: hidden; display: block;">
					<li><?php echo anchor('article/manager','Bài Viết');?>
						<ul>
							<li><?php echo anchor('article/manager','Thùng Rác',null,'status=removed');?></li>
						</ul>
					</li>
					<li><?php echo anchor('article/news','Tin Tức' );?>
						<ul>
							<li><?php echo anchor('article/manager','Thùng Rác',null,'status=removed');?></li>
						</ul>
					</li>
					<li><?php echo anchor('article/category','Danh Mục' );?></li>
					<li><?php echo anchor('article/question','Câu Hỏi' );?></li>
				</ul>
			</li>
			<li>
				<?php echo anchor('advertising/manager',lang('Advertising Management') );?>
				<ul style="visibility: hidden; display: block;">
					<li><?php echo anchor('advertising/slides','Slide Show');?></li>
					<li><?php echo anchor('advertising/manager','Block');?></li>
					<li style="display:none;"><a href="columns.html">Columns</a>
						<ul>
							<li><a href="columns1.html">Boxes in Columns</a></li>
							<li><a href="columns2.html">Columns in Boxes</a></li>
						</ul>
					</li>

				</ul>
			</li>
			<li><?php echo anchor('config/system',$this->lang->line('Config') );?></li>
			</ul>
		</div>
	</div>
</div>

<div class="breadcrumb">
	<div class="bread-links pagesize">
		<ul class="clear">
		<?php
		 if(is_array($this->page_title) && count($this->page_title) > 0 ){
		 	echo '<li class="first">'.$this->page_title[0].'</li>';
		 	for($i=1;$i < count($this->page_title);$i++){
		 		echo '<li >'.$this->page_title[$i].'</li>';
		 	}
		 }
		?>
		</ul>
	</div>
</div>