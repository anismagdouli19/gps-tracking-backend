<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account extends CI_Controller {
	function Account(){
		parent::__construct();
		$this->userSession = array(
				'uid'=> 0,'vid'=> 0,
				'logged_in'=>false,
		);
		//$this->userFields = self::userFields();
	}
	public function index(){
		$form['fields'] = array( 'email'=>array('type'=>'email'),'password'=>array('type'=>'password') );
		$this->template->write_view('content', 'account/view',$form);
		$this->template->render();
	}
	public function login(){
		if($this->session->userdata('uid')){
			redirect('user/manager');
		}

		if($this->input->post()) {
			return self::checkLogin();
		}


		$form['fields'] = array( 'email'=>array('type'=>'email'),'password'=>array('type'=>'password') );
		$this->template->write_view('content', 'account/login-form',$form);
		$script = ''
			."$('body').css({'padding-top':50,'background': ' url(".subdomain('assets_url')."/images/header.png) top left repeat-x'});"
			."$('.header').css('background','none');"
		;
		$this->template->add_js_ready($script);
		$this->template->render();
	}
	public function logout(){

		$this->session->unset_userdata(array_keys($this->userSession));
// bug($this->userSession);die;
		redirect(base_url(), 'refresh');

	}
	protected function checkLogin(){
		$user['email'] = $this->input->post('email');
		$user['password'] = $this->input->post('password');

		$userData = $this->Account_Model->getUser($user);

		if(isset($userData) && $userData){

			$this->userSession =  array(
					'fullname'  => $userData->fullname,
					'uid'=> $userData->uid,
					'logged_in' => TRUE
			);
				$this->session->set_userdata($this->userSession);
				$continue = ($this->input->get('r'))?$this->input->get('r'):'';
				if( strlen($continue) < 4 ){
				    $continue = "user/manager";
				}
				if($this->input->get('format')=='json'){
					return returnJson(true);
				} else {
					redirect($continue);
				}


		} else {
			unset($_POST);
			$this->msg[]= array('type'=>'error','text'=> $this->lang->line('Login False') );
			if($this->input->get('format')=='json'){
				return returnJson(false);
			} else {
				return self::login();
			}

		}

	}

	private function userFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>$this->session->userdata('uid')),
			'email'=>array('disabled'=>true),
			'username'=>'',
			'fullname'=>'',
			'gender'=>array('type'=>'gender'),
			'phone'=>'',
			'lastvisit_date'=>array('disabled'=>true),
			'password'=>array('type'=>'password'),
			'repassword'=>array('type'=>'password','title'=>lang('Retype Password')),

		);
		return (object)$this->form->bindFields($fields);
	}
	public function info(){
		$this->backend->checkLogin();
		$this->backend->formField = self::userFields();
		$opt = array(
			'item-name'=>'Administrator',
			'model'=>'Account_Model',
			'model-get'=>'getinfo',
			'model-update'=>'updateinfo',
			'uri-back'=>1,
			'id'=>$this->session->userdata('uid'),
		);

		$this->page_title[] = lang('Update User Info');
		$this->backend->form($opt);
		$this->template->render();
	}
}