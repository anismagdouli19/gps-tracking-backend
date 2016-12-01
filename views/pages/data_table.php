
<div class="main-wrap">
		<div class="page clear">
			<div style="display: none;">
				<h1>Table Styles <a href="#modal-label" class="label modal-link">INFO</a></h1>
	
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				
				<!-- MODAL WINDOW -->
				<div id="modal-label" class="modal-window"  >
					<div class="notification note-attention">
						<a href="#" class="close" title="Close notification"><span>close</span></a>
						<span class="icon"></span>
						<p><strong>Attention:</strong> More about settings of modal windows is described in Dashboard - Open Modal icon.</p>
	
					</div>
					<h2>Modal Window : size undefined (auto)</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique, lorem id hendrerit sodales, nisl felis sollicitudin lacus, et facilisis felis quam at quam. Nullam vel nunc at sapien sagittis feugiat. Vestibulum est eros, condimentum ac sodales vel, iaculis vitae neque.</p>
					<p>Nam nisl odio, scelerisque non venenatis quis, venenatis a leo. Cras non vehicula justo. Nam vel arcu sem. Suspendisse quam enim, dictum quis lacinia sed, lobortis eget libero. Suspendisse potenti. Suspendisse et ante vitae turpis vestibulum fermentum nec nec elit. Suspendisse ullamcorper lacus in arcu mollis fringilla porta mi placerat. Ut at elit non diam tristique scelerisque. </p>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tristique, lorem id hendrerit sodales, nisl felis sollicitudin lacus, et facilisis felis quam at quam. Nullam vel nunc at sapien sagittis feugiat. Vestibulum est eros, condimentum ac sodales vel, iaculis vitae neque.</p>
				</div>
			</div>
			<div class="content-box">
			<div class="box-body">
				<div class="box-header clear">
				<h2 class="fl" ><?php echo $this->backend->pageTitle();?></h2>
				<?php if(isset($actions)) echo '<div class="tabs" >'.$this->backend->showButtons($actions);?>
				</div>
			</div>
				<?php $this->load->view('modules/datatable',array('table'=>$table));?>
			</div>
		</div>
</div>

