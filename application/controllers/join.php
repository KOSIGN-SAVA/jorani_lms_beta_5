<?php
class join extends CI_Controller{
	function __construct(){
		parent::__construct();
		//setUserContext($this);
		//$this->session->set_userdata('database','default');
		
		if ($this->session->userdata('language') === FALSE) {
			$availableLanguages = explode(",", $this->config->item('languages'));
			$languageCode = $this->polyglot->language2code($this->config->item('language'));
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				if (in_array($_SERVER['HTTP_ACCEPT_LANGUAGE'], $availableLanguages)) {
					$languageCode = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				}
			}
			$this->session->set_userdata('language_code', $languageCode);
			$this->session->set_userdata('language', $this->polyglot->code2language($languageCode));
		}
		$this->lang->load('users', $this->config->item('language'));
		$this->load->model("users_model");
	}
	public function index(){
		//$data = getUserContext($this);
		$data['language_code'] = $this->session->userdata('language_code');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$data['public_key'] = file_get_contents('./assets/keys/public.pem', TRUE);
		
		//load list of language
		$this->load->library('polyglot');
		
		$this->form_validation->set_rules('firstname', lang('users_create_field_firstname'), 'required|xss_clean|strip_tags');
		$this->form_validation->set_rules('lastname', lang('users_create_field_lastname'), 'required|xss_clean|strip_tags');
		$this->form_validation->set_rules('login', lang('users_create_field_login'), 'required|callback_checkLogin|xss_clean|strip_tags');
		$this->form_validation->set_rules('email', lang('users_create_field_email'), 'required|xss_clean|strip_tags');
		if (!$this->config->item('ldap_enabled')) $this->form_validation->set_rules('CipheredValue', lang('users_create_field_password'), 'required');
		//$this->form_validation->set_rules('role[]', lang('users_create_field_role'), 'required');
		//$this->form_validation->set_rules('manager', lang('users_create_field_manager'), 'required|xss_clean|strip_tags');
		//$this->form_validation->set_rules('contract', lang('users_create_field_contract'), 'xss_clean|strip_tags');
		//$this->form_validation->set_rules('position', lang('users_create_field_position'), 'xss_clean|strip_tags');
		//$this->form_validation->set_rules('entity', lang('users_create_field_entity'), 'xss_clean|strip_tags');
		$this->form_validation->set_rules('datehired', lang('users_create_field_hired'), 'xss_clean|strip_tags');
		//$this->form_validation->set_rules('identifier', lang('users_create_field_identifier'), 'xss_clean|strip_tags');
		$this->form_validation->set_rules('language', lang('users_create_field_language'), 'xss_clean|strip_tags');
		$this->form_validation->set_rules('timezone', lang('users_create_field_timezone'), 'xss_clean|strip_tags');
		
		
		if ($this->config->item('ldap_basedn_db')) $this->form_validation->set_rules('ldap_path', lang('users_create_field_ldap_path'), 'xss_clean|strip_tags');
		
		if ($this->form_validation->run() === FALSE) {
			
			$this->load->view('join/join', $data);
			
		} else {
			/* $config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'ssl://smtp.googlemail.com',
					'smtp_port' => 465,
					'smtp_user' => 'prakvirak54@gmail.com', // change it to yours
					'smtp_pass' => '04011990@com', // change it to yours
					'mailtype' => 'html',
					'charset' => 'iso-8859-1',
					'wordwrap' => TRUE
			); */
			$password = $this->users_model->setUsersWeauth();
		
			//Send an e-mail to the user so as to inform that its account has been created
			//$this->load->library('email',$config);
			//$usr_lang = $this->polyglot->code2language($this->input->post('language'));
			
			//setUserContext($this);
	
			redirect('home');
		}
		
	}
}