<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller {
	function User(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->load->model('User_Model');
		$this->load->model('Motor_Model');
	}
	function index(){
		redirect('user/manager','refresh');
	}
	
	public function track(){
		if( $this->input->post() ){
			$userData = $this->User_Model->getUser($this->input->get('user'));
			if($userData) { // remove tracking relation
				jsonData(array('action'=>( ($this->Motor_Model->removeTracking($this->input->post('id')) )?true:false )));
			} else {
				$updateData = array( 'id'=>$this->input->post('id'), 'status'=>$this->input->post('publish'));
				jsonData(array('action'=>( ($this->Motor_Model->updateMotorData($updateData) )?true:false )));
			}
		}
	}
	
	public function manager(){
		$this->page_title[] = $this->lang->line('User Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::userFields();
			$opt = array(
				'item-name'=>'User',
				'model'=>'User_Model',
				'model-get'=>'getUser',
				'model-update'=>'updateUser',
				'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = lang('User Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = lang('User Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'status'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->User_Model->updateUser($updateData) )?true:false )));
			} else if ($action=='changepass'){
				$this->page_title[] = $this->lang->line('Change Password');
				$opt['model-update'] = 'updatePassword';
				$this->backend->formField = self::userPassFields();
				$this->backend->form($opt);
			} else if( $action =='view' ){
				if($this->uri->segment(5) && $this->uri->segment(5) == 'publish'){
					echo jsonData(array('action'=>( ($this->Motor_Model->removeTracking($this->input->post('id')) )?true:false )));
				}
				$uid = $this->uri->segment(4);
				$userData = $this->User_Model->getUser( $uid );
				if ($this->input->get('format') =='json'){
					
					if($this->input->get('publish') !=1){
						$sWhere = null;
					} else {
						$sWhere['s.publish'] = 1;
					}
					$sWhere['tr.owner'] = $uid;
					return $this->backend->dataTableAjax(array('m.id','tr.taget','m.plate_number','m.name','m.owner','m.status'),'Motor_Model','track_data_ajax',$sWhere);
				}
				self::userForm($userData);
// 				$this->template->render();
			}
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['s.publish'] = 1;
			}
			return $this->backend->dataTableAjax(array('u.id','u.username','u.email'),'User_Model','user_ajax',$sWhere);
		} else {
			$data['table'] = array(
				'id'=>array('ID',false,20,'center'),
				'username'=>array('Tên Đăng Nhập',true,100),
				'title'=>array('Title',true,300),
				'status'=>array($this->lang->line('Status'),false,50,'center'),
				'vehicle'=>array($this->lang->line('Add Vehicle'),false,50,'center'),
				'uactions'=>array('Actions',false,100,'center'),
			);
			$data['actions'][] = array('add-new'=>'User Add New');
			$data['title'] =  $this->lang->line('User Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	
	protected function userForm($user=array()){
		$this->page_title[] = lang('Manager Device of').' '.$user->fullname;
		$uid = $user->id;
		foreach($user AS $key=>$value){
			switch($key){
				case 'password':
				case 'password':
				case 'id':
				case 'active_key':
				case 'modified':
				case 'modified_by':
				case 'created':
					unset($user->$key);break;
				case 'birthday':
					$user->$key = (is_date($value))?date("d/m/Y",strtotime($value)):'<span class="value-incorrect" >'.$this->lang->line('value incorrect').'</span>';
					break;
				case 'register_date':
				case 'lastvisit_date':
//				case 'expiry':
					$user->$key = (is_date($value))?date("d/m/Y h:m:s",strtotime($value) ):'<span class="value-incorrect" >'.$this->lang->line('value incorrect').'</span>';
					break;
				default: break;
			}
		}
		//$data['fields'] = $user;
	
		$data['table'] = array(
				'id'=>array('#',false,20,'center'),
				'device'=>array(lang('ID Device'),false,20,'center'),
				'plate_number'=>array(lang('Plate Number'),true,50),
				'motor_name'=>array(lang('Device Name'),true,50),
				'owner'=>array(lang('Vehicle of'),true,100),
				//'expiry'=>array(lang('Expiry'),true,30),
				'powers'=>array(lang('Powers'),false,10,'center'),
				'actions'=>array('Actions',false,50,'center'),
		);
		
		$view = '<div class="content-box">'
					.'<div class="box-body">'
					.'<div class="box-header clear"><h2>'.$this->backend->pageTitle().'</h2>'
				.'</div>'
				.'<div class="box-wrap clear">'
					.'<table class="style1"><thead><tr>'
					.'<th>'.$this->backend->button('Update','button',' class="button red  fl" id="update-user" ').'</th>'
					.'<th class="full"></th><th></th>'
					.'</tr></thead><tbody>';
			
			foreach ($user AS $key=>$value){
				$view .= '<tr><th>'.lang( ucfirst($key) ).'</th><td class="edit-field edit-textfield long">'.$value.'</td></tr>';
			}
		$view .= 	'</tbody></table>'
			.'</div><div class="box-wrap clear">'
			.$this->backend->button('Add Motor Vehicle','button',' class="button green  fl add-device" vtype="motor" ')
			.$this->backend->button('Add Motor Tracking','button',' class="button green  fl add-tracking" vtype="motor" ')
			.$this->backend->button('Add Car Vehicle','button',' class="button green  fl add-device" vtype="car" ')
			.$this->backend->button('Add Car Tracking','button',' class="button green  fl add-tracking" vtype="car" ')
			.'</div>'
			.$this->load->view('modules/datatable',array('table'=>$data['table']),TRUE)
			.'</div></div>';
	
		$this->template->write('content',$view);
		$this->template->write('title', $this->backend->pageTitle() );
		
		$script = '$(".add-device").click(function(){'
					.'if($(this).attr("vtype") == "car" ){'
						.'window.location.href = vt.site+"vehicle/car/add-new?user='.$uid.'";'
					.'} else {'
						.' window.location.href = vt.site+"vehicle/motor/add-new?user='.$uid.'";'
					.'}'
				.'});'
				.'$(".add-tracking").click(function(){'
					.'if($(this).attr("vtype") == "car" ){'
						.'window.location.href = vt.site+"vehicle/car/add-tracking?user='.$uid.'";'
					.'} else {'
						.' window.location.href = vt.site+"vehicle/motor/add-tracking?user='.$uid.'";'
					.'}'
				.'});'
				.' $("#update-user").click(function(){'
					.'window.location.href = vt.site+"user/manager/update/'.$uid.'";'
				.'});'
		
		;
		$this->template->add_js_ready($script);
	
	}
	
	protected function userFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>0),
			'username'=>array('request'=>true),
// 				'password'=>'',
			'email'=>array('type'=>'email'),
			'fullname'=>'',
			'gender'=>array('type'=>'gender'),
			'phone'=>'',
			'phone_2'=>'',
			'phone_3'=>'',
			'phone_4'=>'',
			'address'=>'',
			'register_date'=>array('type'=>'date','request'=>true,'value'=>date('Y-m-d')),
// 			'expiry'=>array('title'=>lang('Expiry'), 'type'=>'date','request'=>true,'value'=>date('Y-m-d',strtotime("+1 year"))),
		);
		return (object)$this->form->bindFields($fields);
	}
	
	protected function userPassFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>$this->uri->segment(4)),
			'password'=>array('request'=>true,'type'=>'password'),
			'repassword'=>array('request'=>true,'type'=>'password'),
		);
		return (object)$this->form->bindFields($fields);
	}
	
	
}