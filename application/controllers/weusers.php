<?php
class weusers extends CI_Controller{
	function __construct(){
		parent::__construct();
		
		$this->load->model('users_model');
		
	}
	
	
	/**
	 * Form validation callback : prevent from login duplication
	 * @param string $login Login
	 * @return boolean TRUE if the field is valid, FALSE otherwise
	 * @author Benjamin BALET <benjamin.balet@gmail.com>
	 */
	public function checkLogin($login) {
		if (!$this->users_model->isLoginAvailable($login)) {
			$this->form_validation->set_message('checkLogin', lang('users_create_checkLogin'));
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/**
	 * Ajax endpoint : check login duplication
	 * @author Benjamin BALET <benjamin.balet@gmail.com>
	 */
	public function checkLoginByAjax() {
		header("Content-Type: text/plain");
		if ($this->users_model->isLoginAvailable($this->input->post('login'))) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
}


