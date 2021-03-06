<?php
/**
 * This controller allows a manager to list and manage leave requests submitted to him
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class allows a manager to list and manage leave requests submitted to him.
 * Since 0.3.0, we expose the list of collaborators and allow a manager to access to some reports:
 *  - presence report of an employee.
 *  - counters of an employee (leave balance).
 *  - Yearly calendar of an employee.
 * But those reports are not served by this controller (either HR or Calendar controller).
 */
class Requests extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->lang->load('requests', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of all requests submitted to you
     * Status is submitted or accepted/rejected depending on the filter parameter.
     * @param string $name Filter the list of submitted leave requests (all or requested)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index($filter = 'requested') {
        $this->auth->checkIfOperationIsAllowed('list_requests');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['filter'] = $filter;
        $data['title'] = lang('requests_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_validation');
        ($filter == 'all')? $showAll = TRUE : $showAll = FALSE;
        $data['requests'] = $this->leaves_model->getLeavesRequestedToManager($this->user_id, $showAll);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Accept a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function accept($id) {
        $this->auth->checkIfOperationIsAllowed('accept_requests');
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $leave = $this->leaves_model->getLeaves($id);
        if (empty($leave)) {
            redirect('notfound');
        }
        $employee = $this->users_model->getUsers($leave['employee']);
        $is_delegate = $this->delegations_model->isDelegateOfManager($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->leaves_model->acceptLeave($id);
            $this->sendMail($id);
            $this->load->library('../controllers/leaves');
            $this->leaves->pushNotiFromWeb(2, $id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to accept leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_accept_flash_msg_error'));
            redirect('leaves');
        }
    }

    /**
     * Reject a leave request
     * @param int $id leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function reject($id) {
        $this->auth->checkIfOperationIsAllowed('reject_requests');
        $this->load->model('users_model');
        $this->load->model('delegations_model');
        $leave = $this->leaves_model->getLeaves($id);
        if (empty($leave)) {
            redirect('notfound');
        }
        $employee = $this->users_model->getUsers($leave['employee']);
        $is_delegate = $this->delegations_model->isDelegateOfManager($this->user_id, $employee['manager']);
        if (($this->user_id == $employee['manager']) || ($this->is_hr)  || ($is_delegate)) {
            $this->leaves_model->rejectLeave($id);
            $this->sendMail($id);
            $this->load->library('../controllers/leaves');
            $this->leaves->pushNotiFromWeb(3, $id);
            $this->session->set_flashdata('msg',  lang('requests_reject_flash_msg_success'));
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('requests');
            }
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to reject leave #' . $id);
            $this->session->set_flashdata('msg', lang('requests_reject_flash_msg_error'));
            redirect('leaves');
        }
    }
    
    /**
     * Display the list of all requests submitted to the line manager (Status is submitted)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        $this->auth->checkIfOperationIsAllowed('list_collaborators');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('requests_collaborators_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_collaborators_list');
        $this->load->model('users_model');
        $data['collaborators'] = $this->users_model->getCollaboratorsOfManager($this->user_id);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/collaborators', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the list of delegations
     * @param int $id Identifier of the manager (from HR/Employee) or 0 if self
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delegations($id = 0) {
        if ($id == 0) $id = $this->user_id;
        //Self modification or by HR
        if (($this->user_id == $id) || ($this->is_hr)) {
            $data = getUserContext($this);
            $this->lang->load('datatable', $this->language);
            $data['title'] = lang('requests_delegations_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_delegations');
            $this->load->model('users_model');
            $data['name'] = $this->users_model->getName($id);
            $data['id'] = $id;
            $this->load->model('delegations_model');
            $data['delegations'] = $this->delegations_model->listDelegationsForManager($id);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('requests/delegations', $data);
            $this->load->view('templates/footer');
        } else {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to list_delegations');
            $this->session->set_flashdata('msg', sprintf(lang('global_msg_error_forbidden'), 'list_delegations'));
            redirect('leaves');
        }
    }
    
    /**
     * Ajax endpoint : Delete a delegation for a manager
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteDelegations() {
        $manager = $this->input->post('manager_id', TRUE);
        $delegation = $this->input->post('delegation_id', TRUE);
        if (($this->user_id != $manager) && ($this->is_hr == FALSE)) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if (isset($manager) && isset($delegation)) {
                $this->output->set_content_type('text/plain');
                $this->load->model('delegations_model');
                $id = $this->delegations_model->deleteDelegation($delegation);
                echo $id;
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Ajax endpoint : Add a delegation for a manager
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function addDelegations() {
        $manager = $this->input->post('manager_id', TRUE);
        $delegate = $this->input->post('delegate_id', TRUE);
        if (($this->user_id != $manager) && ($this->is_hr === FALSE)) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if (isset($manager) && isset($delegate)) {
                $this->output->set_content_type('text/plain');
                $this->load->model('delegations_model');
                if (!$this->delegations_model->isDelegateOfManager($delegate, $manager)) {
                    $id = $this->delegations_model->addDelegate($manager, $delegate);
                    echo $id;
                } else {
                    echo 'null';
                }
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Create a leave request in behalf of a collaborator
     * @param int $id Identifier of the employee
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createleave($id) {
        $this->lang->load('hr', $this->language);
        $this->load->model('users_model');
        $employee = $this->users_model->getUsers($id);
        if (($this->user_id != $employee['manager']) && ($this->is_hr === FALSE)) {
            log_message('error', 'User #' . $this->user_id . ' illegally tried to access to collaborators/leave/create  #' . $id);
            $this->session->set_flashdata('msg', lang('requests_summary_flash_msg_forbidden'));
            redirect('leaves');
        } else {
            $data = getUserContext($this);
            $this->load->helper('form');
            $this->load->library('form_validation');
            $data['title'] = lang('hr_leaves_create_title');
            $data['form_action'] = 'requests/createleave/' . $id;
            $data['source'] = 'requests/collaborators';
            $data['employee'] = $id;

            $this->form_validation->set_rules('startdate', lang('hr_leaves_create_field_start'), 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('enddate', lang('leaves_create_field_end'), 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('duration', lang('hr_leaves_create_field_duration'), 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('type', lang('hr_leaves_create_field_type'), 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('cause', lang('hr_leaves_create_field_cause'), 'xss_clean|strip_tags');
            $this->form_validation->set_rules('status', lang('hr_leaves_create_field_status'), 'required|xss_clean|strip_tags');

            $data['credit'] = 0;
            $default_type = $this->config->item('default_leave_type');
            $default_type = $default_type == FALSE ? 0 : $default_type;
            $data["defaultType"] = $default_type;
            if ($this->form_validation->run() === FALSE) {
                $this->load->model('types_model');
                $data['types'] = $this->types_model->getTypes();
                foreach ($data['types'] as $type) {
                    if ($type['id'] == $default_type) {
                        $data['credit'] = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type['name']);
                        break;
                    }
                }
                $data['types'] = $this->types_model->getTypesAsArray();
                $this->load->model('users_model');
                $data['name'] = $this->users_model->getName($id);
                $this->load->view('templates/header', $data);
                $this->load->view('menu/index', $data);
                $this->load->view('hr/createleave');
                $this->load->view('templates/footer');
            } else {
                $this->leaves_model->setLeaves($id);       //We don't use the return value
                $this->session->set_flashdata('msg', lang('hr_leaves_create_flash_msg_success'));
                //No mail is sent, because the manager would set the leave status to accepted
                redirect('requests/collaborators');
            }
        }
    }
    
    /**
     * Create an overtime request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function createovertime($id) {
    	$this->auth->checkIfOperationIsAllowed('create_extra');
    	$data = getUserContext($this);
    	
    	$this->lang->load('hr', $this->language);
    	$this->load->model('users_model');
    	$employee = $this->users_model->getUsers($id);
    	
    	if (($this->user_id != $employee['manager']) && ($this->is_hr === FALSE)) {
    		log_message('error', 'User #' . $this->user_id . ' illegally tried to access to collaborators/leave/create  #' . $id);
    		$this->session->set_flashdata('msg', lang('requests_summary_flash_msg_forbidden'));
    		redirect('requests/collaborators');
    	}else{
    		
    		$this->load->helper('form');
    		$this->load->library('form_validation');
    		$this->load->model('overtime_model');
    		$this->lang->load('extra', $this->language);
    		$data['form_action'] = 'requests/createovertime/' . $id;
    		
    		$this->form_validation->set_rules('date', lang('extra_create_field_date'), 'required|xss_clean|strip_tags');
    		$this->form_validation->set_rules('duration', lang('extra_create_field_duration'), 'required|xss_clean|strip_tags');
    		$this->form_validation->set_rules('cause', lang('extra_create_field_cause'), 'required|xss_clean|strip_tags');
    		$this->form_validation->set_rules('status', lang('extra_create_field_status'), 'required|xss_clean|strip_tags');
    		if ($this->form_validation->run() === FALSE) {
    			$data['title'] = lang('extra_create_title');
    			$data['help'] = $this->help->create_help_link('global_link_doc_page_create_overtime');
    			$data['name'] = $this->users_model->getName($id);
    			$this->load->view('templates/header', $data);
    			$this->load->view('menu/index', $data);
    			$this->load->view('hr/createovertime', $data);
    			$this->load->view('templates/footer');
    		} else {
    			if (function_exists('triggerCreateExtraRequest')) {
    				triggerCreateExtraRequest($this);
    			}
    			$extra_id = $this->overtime_model->setExtra($id);
    			$this->session->set_flashdata('msg', lang('extra_create_msg_success'));
    			//If the status is requested, send an email to the manager
    			if ($this->input->post('status') == 2) {
    				$this->sendMailOvertime($extra_id);
    			}
    			if (isset($_GET['source'])) {
    				redirect($_GET['source']);
    			} else {
    				redirect('requests/collaborators');
    			}
    		}
    		
    	}
    	
    }
    
    /**
     * Send a leave request email to the employee that requested the leave
     * The method will check if the leave request was accepted or rejected 
     * before sending the e-mail
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMail($id)
    {
        $this->load->model('users_model');
        $this->load->model('organization_model');
        $leave = $this->leaves_model->getLeaves($id);
        $employee = $this->users_model->getUsers($leave['employee']);
        $supervisor = $this->organization_model->getSupervisor($employee['organization']);

        //Send an e-mail to the employee
        $this->load->library('email');
        $this->load->library('polyglot');
        $usr_lang = $this->polyglot->code2language($employee['language']);
        
        //We need to instance an different object as the languages of connected user may differ from the UI lang
        $lang_mail = new CI_Lang();
        $lang_mail->load('email', $usr_lang);
        $lang_mail->load('global', $usr_lang);
        
        $date = new DateTime($leave['startdate']);
        $startdate = $date->format($lang_mail->line('global_date_format'));
        $date = new DateTime($leave['enddate']);
        $enddate = $date->format($lang_mail->line('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => $lang_mail->line('email_leave_request_validation_title'),
            'Firstname' => $employee['firstname'],
            'Lastname' => $employee['lastname'],
            'StartDate' => $startdate,
            'EndDate' => $enddate,
            'StartDateType' => $lang_mail->line($leave['startdatetype']),
            'EndDateType' => $lang_mail->line($leave['enddatetype']),
            'Cause' => $leave['cause'],
            'Type' => $leave['type_name']
        );
        
        if ($leave['status'] == 3) {    //accepted
            $message = $this->parser->parse('emails/' . $employee['language'] . '/request_accepted', $data, TRUE);
            $subject = $lang_mail->line('email_leave_request_accept_subject');
        } else {    //rejected
            $message = $this->parser->parse('emails/' . $employee['language'] . '/request_rejected', $data, TRUE);
            $subject = $lang_mail->line('email_leave_request_reject_subject');
        }
        sendMailByWrapper($this, $subject, $message, $employee['email'], is_null($supervisor)?NULL:$supervisor->email);
    }
    
    /**
     * Send a overtime request email to the manager of the connected employee
     * @param int $id overtime request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMailOvertime($id) {
    	$this->load->model('users_model');
    	$this->load->model('delegations_model');
    	$extra = $this->overtime_model->getExtras($id);
    	$user = $this->users_model->getUsers($extra['employee']);
    	$manager = $this->users_model->getUsers($user['manager']);
    	$sesInfor = $this->users_model->getUsers($this->session->userdata('id'));
    
    	//Test if the manager hasn't been deleted meanwhile
    	if (empty($manager['email'])) {
    		$this->session->set_flashdata('msg', lang('extra_create_msg_error'));
    	} else {
    		$acceptUrl = base_url() . 'overtime/accept/' . $id;
    		$rejectUrl = base_url() . 'overtime/reject/' . $id;
    
    		//Send an e-mail to the manager
    		$this->load->library('email');
    		$this->load->library('polyglot');
    		$usr_lang = $this->polyglot->code2language($manager['language']);
    		//We need to instance an different object as the languages of connected user may differ from the UI lang
    		$lang_mail = new CI_Lang();
    		$lang_mail->load('email', $usr_lang);
    		$lang_mail->load('global', $usr_lang);
    
    		$date = new DateTime($this->input->post('date'));
    		$startdate = $date->format($lang_mail->line('global_date_format'));
    		
    		$strDurationSms = "";
    		
    		$sTime = explode(":", $this->input->post('start_time'));
    		$eTime = explode(":", $this->input->post('end_time'));
    		 
    		$sH = str_pad($sTime[0], 2, "0", STR_PAD_LEFT);
    		$sM = str_pad($sTime[1], 2, "0", STR_PAD_LEFT);
    		$eH = str_pad($eTime[0], 2, "0", STR_PAD_LEFT);
    		$eM = str_pad($eTime[1], 2, "0", STR_PAD_LEFT);
    		
    		$sMM = intval($sH) * 60 + intval($sM);
    		$eMM = intval($eH) * 60 + intval($eM);
    		
    		$diffMM = $eMM - $sMM;
    		$diffH = intval($diffMM / 60);
    		$diffM = intval($diffMM % 60);
    		
    		$strDurationSms .= $this->input->post('duration')
    		. " (" . $sH . ":" . $sM . " ~ " . $eH . ":" . $eM
    		. ", " . $diffH . lang("extra_view_label_hours") . " " . $diffM. lang("extra_view_label_minute"). ")";
    
    		$this->load->library('parser');
    		$data = array(
    				'Title' => $lang_mail->line('email_extra_request_validation_title'),
    				'Firstname' => $sesInfor['firstname'],
    				'Lastname' =>  $sesInfor['lastname'],
    				'ForFirstname' => $user['firstname'],
    				'ForLastname' =>  $user['lastname'],
    				'Date' => $startdate,
    				'Duration' => $strDurationSms,
    				'Cause' => $this->input->post('cause'),
    				'UrlAccept' => $acceptUrl,
    				'UrlReject' => $rejectUrl
    		);
    		$message = $this->parser->parse('emails/' . $manager['language'] . '/overtime_request', $data, TRUE);
    		//Copy to the delegates, if any
    		$delegates = $this->delegations_model->listMailsOfDelegates($manager['id']);
    		$subject = $lang_mail->line('email_extra_request_reject_subject') . ' ' .
    				$user['firstname'] . ' ' .$user['lastname'];
    		sendMailByWrapper($this, $subject, $message, $manager['email'], $delegates);
    	}
    }
    
    /**
     * Export the list of all leave requests (sent to the connected user) into an Excel file
     * @param string $name Filter the list of submitted leave requests (all or requested)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export($filter = 'requested') {
        $this->load->library('excel');
        $data['filter'] = $filter;
        $this->load->view('requests/export', $data);
    }
    
    /**
     * Leave balance report limited to the subordinates of the connected manager
     * Status is submitted or accepted/rejected depending on the filter parameter.
     * @param int $dateTmp (Timestamp) date of report
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function balance($dateTmp = NULL) {
        $this->auth->checkIfOperationIsAllowed('list_requests');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['title'] = lang('requests_balance_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_balance_report');
        
        if ($dateTmp === NULL) {
            $refDate = date("Y-m-d");
            $data['isDefault'] = 1;
        } else {
            $refDate = date("Y-m-d", $dateTmp);
            $data['isDefault'] = 0;
        }
        $data['refDate'] = $refDate;

        $this->load->model('types_model');
        $data['types'] = $this->types_model->getTypes();
        
        $result = array();
        $this->load->model('users_model');
        $users = $this->users_model->getCollaboratorsOfManager($this->user_id);
        foreach ($users as $user) {
            $result[$user['id']]['identifier'] = $user['identifier'];
            $result[$user['id']]['firstname'] = $user['firstname'];
            $result[$user['id']]['lastname'] = $user['lastname'];
            $date = new DateTime($user['datehired']);
            $result[$user['id']]['datehired'] = $date->format(lang('global_date_format'));
            $result[$user['id']]['position'] = $user['position_name'];
            foreach ($data['types'] as $type) {
                $result[$user['id']][$type['name']] = '';
            }
            
            $summary = $this->leaves_model->getLeaveBalanceForEmployee($user['id'], TRUE, $refDate);
            if (count($summary) > 0 ) {
                foreach ($summary as $key => $value) {
                    $result[$user['id']][$key] = round($value[1] - $value[0], 3, PHP_ROUND_HALF_DOWN);
                }
            }
        }
        $data['result'] = $result;
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('requests/balance', $data);
        $this->load->view('templates/footer');
    }
}
