<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class vehicle extends CI_Controller {
	function vehicle(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->load->model('Motor_Model');
		$this->load->model('User_Model');
		$this->load->model('System_Model');
		$this->lang->load('vehicle', 'vietnamese');
	}
	function index(){
		redirect('vehicle/motor','refresh');
	}

	public function motor(){
		$this->page_title[] = lang('Motor Manager');
		$action = $this->uri->segment(3);
		$userData = $this->User_Model->getUser($this->input->get('user'));
		if($action){
			$this->backend->formField = self::motorFields();
// 			bug($this->backend->formField);exit;
			$opt = array(
				'item-name'=>'Motor',
				'model'=>'Motor_Model',
				'model-get'=>'getMotor',
				'model-update'=>'updateMotor',
				'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = $this->lang->line('Motor Add New');
				if($this->input->get('user') && $this->input->get('user') >0 ){
					$this->backend->formField->owner->value = $this->input->get('user');
				}
				$this->backend->form($opt);
			}else if($action=='update'){
				$this->backend->formField->confirm->value=1;
				$opt['id'] = $this->uri->segment(4);
				$this->backend->form($opt);
			} else if( $action=='add-tracking') {
				$this->backend->formField = self::motorTracking();
				if($this->input->post() ){
					//bug('call me'); exit;
					$this->Motor_Model->updateTracking(array( 'taget'=>$this->input->post('id'),'owner'=>$this->input->get('user'),'type'=>'track'));
					redirect('vehicle/motor','',302,'user='.$this->input->get('user'));
				} else {
					$this->page_title[] = lang('ThÃªm Xe Theo DÃµi');
					if($this->input->get('user') && $this->input->get('user') >0 ){
						$this->backend->formField->owner->value = $this->input->get('user');
					}
					$this->backend->form($opt);
				}

			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				if($userData) { // remove tracking relation
// 					exit('call me');
					jsonData(array('action'=>( ($this->Motor_Model->removeTracking($this->input->post('id')) )?true:false )));
				} else {
					$updateData = array( 'id'=>$this->input->post('id'), 'status'=>$this->input->post('publish'));
					jsonData(array('action'=>( ($this->Motor_Model->updateMotorData($updateData) )?true:false )));
				}

			} else if ($action =='report'){
				self::motorReport( $this->input->get('v') );
			}
		} else if( $this->input->get('user') ){

			if($userData){
				if ($this->input->get('format') =='json'){
					if($this->input->get('publish') !=1){
						$sWhere = null;
					} else {
						$sWhere['s.publish'] = 1;
					}
					$sWhere['tr.owner'] = $this->input->get('user');
					return $this->backend->dataTableAjax(array('m.id','tr.taget','m.plate_number','m.name','m.owner','m.status'),'Motor_Model','track_data_ajax',$sWhere);
				}
				self::userForm($userData);
			} else {
				goto listDvice;
			}
		} else {
			listDvice:

			if ($this->input->get('format') =='json'){
				if($this->input->get('publish') !=1){
					$sWhere = null;
				} else {
					$sWhere['s.publish'] = 1;
				}
				return $this->backend->dataTableAjax(array('m.id','m.plate_number','m.owner','m.expiry','m.status',null,null),'Motor_Model','motor_ajax',$sWhere);
			}
			$data['table'] = array(
				'id'=>array('ID',true,20,'center'),
				'plate_number'=>array($this->lang->line('Plate Number'),true,100),
				'owner'=>array($this->lang->line('Vehicle of'),true,40),
				'expiry'=>array($this->lang->line('Expiry'),true,40),
				'publish'=>array('Publish',false,10,'center'),
				'last-data'=>array('Last Data',false,80,'center'),
				'report'=>array('Report',false,10,'center'),
				'actions'=>array('Actions',false,50,'center'),
			);
			$this->page_title[] = lang('Motors Data');
			$data['DisplayLength'] = 25;
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}


	protected function motorFields(){
		$fields = array(
			'id'=>array('type'=>'text','value'=>0),
			'plate_number'=>array('request'=>true,'title'=>lang('Plate Number')),
			'owner'=>array('type'=>'owner','request'=>true),
			'name'=>array('title'=>lang('Vehicle Name')),
			//'imegps'=>array('title'=>$this->lang->line('imegps')),
			'simcard'=>array('title'=>lang('Vehicle_simcard')),
			'expiry'=>array('title'=>lang('Expiry'), 'type'=>'date','request'=>true, 'value'=>date('Y-m-d',strtotime("+1 year")) ),
			'confirm'=>array('type'=>'hidden','value'=>0),
			'fuel'=>array( 'title'=>lang('LiÌ�t XÄƒng / 100 km') ),
			'fuel_price'=>'',
			'type'=>array('type'=>'hidden','value'=>1),
		);
		return (object)$this->form->bindFields($fields);
	}

	public function car(){
		$this->page_title[] = lang('Car Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::carFields();
			$userData = $this->User_Model->getUser($this->input->get('user'));
			// 			bug($this->backend->formField);exit;
			$opt = array(
					'item-name'=>'Car',
					'model'=>'Motor_Model',
					'model-get'=>'getCar',
					'model-update'=>'updateCar',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = lang('Car Add New');
				if($this->input->get('user') && $this->input->get('user') >0 ){
					$this->backend->formField->owner->value = $this->input->get('user');
				}
				$this->backend->controllersActions($opt,$action);
			}else if($action=='update'){
				$this->backend->formField->confirm->value=1;
				$opt['id'] = $this->uri->segment(4);
				$this->backend->controllersActions($opt,$action);
			} else if( $action=='add-tracking') {
				$this->backend->formField = self::motorTracking();
				if($this->input->post() ){
					$this->Motor_Model->updateTracking(array(
							'taget'=>intval($this->input->post('id')) + 30000,
							'owner'=>$this->input->get('user'),'type'=>'track')
					);
					redirect('vehicle/car','',302,'user='.$this->input->get('user'));
				} else {
					$this->page_title[] = lang('Add new Car to tracking');
					if($this->input->get('user') && $this->input->get('user') >0 ){
						$this->backend->formField->owner->value = $this->input->get('user');
					}
					$this->backend->form($opt);
				}
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
					$updateData = array( 'id'=>$this->input->post('id'), 'status'=>$this->input->post('publish'));
					jsonData(array('action'=>( ($this->Motor_Model->updateMotorData($updateData) )?true:false )));
			}


		} else {
			if ($this->input->get('format') =='json'){
				if($this->input->get('publish') !=1){
					$sWhere = null;
				} else {
					$sWhere['s.publish'] = 1;
				}
				return $this->backend->dataTableAjax(array('m.id','m.plate_number','m.owner','m.expiry','m.status',null,null),'Motor_Model','car_ajax',$sWhere);
			}
			$data['table'] = array(
				'id'=>array('ID',true,20,'center'),
				'plate_number'=>array($this->lang->line('Plate Number'),true,100),
				'owner'=>array($this->lang->line('Vehicle of'),true,40),
				'expiry'=>array($this->lang->line('Expiry'),true,40),
				'publish'=>array('Publish',false,10,'center'),
				'last-data'=>array('Last Data',false,80,'center'),
				'report'=>array('Report',false,10,'center'),
				'actions'=>array('Actions',false,50,'center'),
			);
			$this->page_title[] = lang('Car Data');
			$data['DisplayLength'] = 100;
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}

	protected function carFields(){
		$fields = array(
				'id'=>array('type'=>'text','value'=>0),
				'plate_number'=>array('request'=>true,'title'=>$this->lang->line('Plate Number')),
				'owner'=>array('type'=>'owner','request'=>true),
				'name'=>array('title'=>lang('Vehicle Name')),
				//'imegps'=>array('title'=>$this->lang->line('imegps')),
				'simcard'=>array('title'=>lang('Vehicle_simcard')),
				'expiry'=>array('type'=>'date','request'=>true, 'value'=>date('Y-m-d',strtotime("+1 year")) ),
				'fuel'=>array( 'title'=>lang('Amount of Fuel') ),
				'fuel_type'=>array('type'=>'fuel_type'),
				'confirm'=>array('type'=>'hidden','value'=>0),
				'conditioner'=>array('type'=>'using','title'=>lang('The Conditioner')),
				'door'=>array('type'=>'using','title'=>lang('The Door'),'value'),
				'heat'=>array('type'=>'using','title'=>lang('The Heat')),
				'fuel_current'=>array('type'=>'using'),
				'type'=>array('type'=>'hidden','value'=>2),
		);
		return (object)$this->form->bindFields($fields);
	}

	public function user(){
		$uid = $this->uri->segment(3);
		$userData = $this->User_Model->getUser( $uid );
		if($userData){
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
			$this->template->render();
		} else {
			show_404();
		}
	}

	protected function motorTracking(){
		$fields = array(
			'id'=>array('type'=>'text','value'=>0),
			'owner'=>array('type'=>'owner','request'=>true),
		);
		return (object)$this->form->bindFields($fields);
	}

	protected function motorReport($id=1){
		$data['fields'] = $this->Motor_Model->getReport($id);
		$motor = $this->Motor_Model->getMotor($id);
// 		exit('iam in controll');
		$this->page_title[] = lang('Motor Report').': '.$motor->name;
		$this->template->write_view('content','pages/venhicle-report',$data);
	}
// 	function importTrackOwner(){
// 		$motors = $this->Motor_Model->motor_ajax(0,100);
// 		foreach($motors['data'] AS $m){
// 			$this->Motor_Model->updateTracking(array( 'taget'=>$m[0],'owner'=>$m[7],'type'=>'owner'));
// 		}
// 	}

	protected function userForm($user=array()){
		$this->page_title[] = lang('Manager Device of').' '.$user->fullname;
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
				case 'expiry':
					$user->$key = (is_date($value))?date("d/m/Y h:m:s",strtotime($value) ):'<span class="value-incorrect" >'.$this->lang->line('value incorrect').'</span>';
					// 									$userData->$key .='v='.$value;
					// 									$userData->$key .='strtotime='.strtotime($value);
					break;
				default: break;
			}
		}
		$data['fields'] = $user;

		$data['table'] = array(
			'id'=>array('#',false,20,'center'),
			'device'=>array(lang('ID Device'),false,20,'center'),
			'plate_number'=>array(lang('Plate Number'),true,50,'center'),
			'motor_name'=>array(lang('Device Name'),true,50),
			'owner'=>array(lang('Vehicle of'),true,100),
			'expiry'=>array(lang('Expiry'),true,30),
			'powers'=>array(lang('Powers'),false,10,'center'),
			'actions'=>array('Actions',false,50,'center'),
		);
		$this->template->write_view('content','pages/user_view',$data);
		$this->template->write('title', $this->backend->pageTitle() );

	}

	public function fuel(){
		$this->page_title[] = lang('Fuel Price Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::fuelFields();
			$opt = array(
					'item-name'=>'User',
					'model'=>'System_Model',
					'model-get'=>'getFuel',
					'model-update'=>'updateFuel',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = lang('Fuel Price Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = lang('Fuel Price Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				jsonData(array('action'=>( ($this->System_Model->changeStatus($this->input->post('id'),$this->input->post('publish')) )?true:false )));
			}
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['s.publish'] = 1;
			}
			return $this->backend->dataTableAjax(array('u.id','u.username','u.email'),'System_Model','fuel_ajax',$sWhere);
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'time'=>array(lang('Date'),true,80),
					'price'=>array(lang('Price'),true,350),
					'status'=>array(lang('Status'),false,50,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
			$data['actions'][] = array('add-new'=>'Add New Price');
			$data['title'] =  $this->lang->line('Fuel Price Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}

	protected function fuelFields(){

		$fields = array(
			'id'=>array('type'=>'hidden'),
			'time'=>array('type'=>'time'),
		);
		if( !class_exists('fuel_type') ){
			include APPPATH.DS.'libraries/form-field/fuel_type.php';
		}
		foreach( fuel_type::$value AS $val=>$title){
			$fields[$val] = array('title'=>$title,'type'=>'inputUnit','unit'=>'number','lable'=>'VND');
		}

		$fields['source'] = array('type'=>'url','value'=>'http://www.petrolimex.com.vn/');
		return (object)$this->form->bindFields($fields);
	}


}