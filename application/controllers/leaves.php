<?php
/**
 * This controller contains the actions allowing an employee to list and manage its leave requests
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

//We can define custom triggers before saving the leave request into the database
require_once FCPATH . "local/triggers/leave.php";

/**
 * This class allows an employee to list and manage its leave requests
 * Since 0.4.3 a trigger is called at the creation, if the function triggerCreateLeaveRequest is defined
 * see content of /local/triggers/leave.php
 */
class Leaves extends CI_Controller {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->lang->load('leaves', $this->language);
        $this->lang->load('global', $this->language);
    }

    /**
     * Display the list of the leave requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->checkIfOperationIsAllowed('list_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['leaves'] = $this->leaves_model->getLeavesOfEmployee($this->session->userdata('id'));
        $data['title'] = lang('leaves_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_requests_list');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the history of changes of a leave request
     * @param int $id Identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function history($id) {
        $this->auth->checkIfOperationIsAllowed('list_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['leave'] = $this->leaves_model->getLeaves($id);
        $this->load->model('history_model');
        $data['events'] = $this->history_model->getLeaveRequestsHistory($id);
        $this->load->view('leaves/history', $data);
    }

    /**
     * Display the details of leaves taken/entitled for the connected user
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($refTmp = NULL) {
        $this->auth->checkIfOperationIsAllowed('counters_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
            $data['isDefault'] = 0;
        } else {
            $data['isDefault'] = 1;
        }
        $data['refDate'] = $refDate;
        $data['summary'] = $this->leaves_model->getLeaveBalanceForEmployee($this->user_id, FALSE, $refDate);

        if (!is_null($data['summary'])) {
            $data['title'] = lang('leaves_summary_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_my_summary');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/counters', $data);
            $this->load->view('templates/footer');
        } else {
            $this->session->set_flashdata('msg', lang('leaves_summary_flash_msg_error'));
            redirect('leaves');
        }
    }

    /**
     * Display a leave request
     * @param string $source Page source (leaves, requests) (self, manager)
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($source, $id) {
        $this->auth->checkIfOperationIsAllowed('view_leaves');
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->getLeaves($id);
        if (empty($data['leave'])) {
            redirect('notfound');
        }
        //If the user is not its not HR, not manager and not the creator of the leave
        //the employee can't see it, redirect to LR list
        if ($data['leave']['employee'] != $this->user_id) {
            if ((!$this->is_hr)) {
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($data['leave']['employee']);
                if ($employee['manager'] != $this->user_id) {
                    $this->load->model('delegations_model');
                    if (!$this->delegations_model->isDelegateOfManager($this->user_id, $employee['manager'])) {
                        log_message('error', 'User #' . $this->user_id . ' illegally tried to view leave #' . $id);
                        redirect('leaves');
                    }
                }
            } //Admin
        } //Current employee
        $data['source'] = $source;
        $data['title'] = lang('leaves_view_html_title');
        if ($source == 'requests') {
            if (empty($employee)) {
                $this->load->model('users_model');
                $data['name'] = $this->users_model->getName($data['leave']['employee']);
            } else {
                $data['name'] = $employee['firstname'] . ' ' . $employee['lastname'];
            }
        } else {
            $data['name'] = '';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->checkIfOperationIsAllowed('create_leaves');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leaves_create_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');

        $this->form_validation->set_rules('startdate', lang('leaves_create_field_start'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddate', lang('leaves_create_field_end'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('leaves_create_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('type', lang('leaves_create_field_type'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('leaves_create_field_cause'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('leaves_create_field_status'), 'required|xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('contracts_model');
            $leaveTypesDetails = $this->contracts_model->getLeaveTypesDetailsOTypesForUser($this->session->userdata('id'));
            $data['defaultType'] = $leaveTypesDetails->defaultType;
            $data['credit'] = $leaveTypesDetails->credit;
            $data['types'] = $leaveTypesDetails->types;
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/create');
            $this->load->view('templates/footer');
        } else {
            if (function_exists('triggerCreateLeaveRequest')) {
                triggerCreateLeaveRequest($this);
            }
            $leave_id = $this->leaves_model->setLeaves($this->session->userdata('id'));
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMailOnLeaveRequestCreation($leave_id);
                $this->pushNotiFromWeb(0, null, $this->input->post('cause'), $this->input->post('type'));
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }

    /**
     * Edit a leave request
     * @param int $id Identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->checkIfOperationIsAllowed('edit_leaves');
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->getLeaves($id);
        //Check if exists
        if (empty($data['leave'])) {
            redirect('notfound');
        }
        //If the user is not its own manager and if the leave is 
        //already requested, the employee can't modify it
        if (!$this->is_hr) {
            if (($this->session->userdata('manager') != $this->user_id) &&
                $data['leave']['status'] != 1) {
                if ($this->config->item('edit_rejected_requests') == FALSE ||
                    $data['leave']['status'] != 4) {//Configuration switch that allows editing the rejected leave requests
                    log_message('error', 'User #' . $this->user_id . ' illegally tried to edit leave #' . $id);
                    $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_error'));
                    redirect('leaves');
                }
            }
        } //Admin

        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('startdate', lang('leaves_edit_field_start'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddate', lang('leaves_edit_field_end'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('leaves_edit_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('type', lang('leaves_edit_field_type'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('leaves_edit_field_cause'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('leaves_edit_field_status'), 'required|xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = lang('leaves_edit_html_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');
            $data['id'] = $id;
            $this->load->model('contracts_model');
            $leaveTypesDetails = $this->contracts_model->getLeaveTypesDetailsOTypesForUser($this->session->userdata('id'), $data['leave']['type']);
            $data['defaultType'] = $leaveTypesDetails->defaultType;
            $data['credit'] = $leaveTypesDetails->credit;
            $data['types'] = $leaveTypesDetails->types;
            $this->load->model('users_model');
            $data['name'] = $this->users_model->getName($data['leave']['employee']);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->leaves_model->updateLeaves($id);       //We don't use the return value
            $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_success'));
            //If the status is requested, send an email to the manager
            if ($this->input->post('status') == 2) {
                $this->sendMailOnLeaveRequestCreation($id);
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }

    /**
     * Send a leave request creation email to the manager of the connected employee
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMailOnLeaveRequestCreation($id) {
        $this->load->model('users_model');
        $this->load->model('types_model');
        $this->load->model('delegations_model');
        //We load everything from DB as the LR can be edited from HR/Employees
        $leave = $this->leaves_model->getLeaves($id);
        $user = $this->users_model->getUsers($leave['employee']);
        $manager = $this->users_model->getUsers($user['manager']);
        if (empty($manager['email'])) {
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_error'));
        } else {
            //Send an e-mail to the manager
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($manager['language']);

            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);

            $this->sendGenericMail($leave,$user,$manager,$lang_mail,$lang_mail->line('email_leave_request_creation_title'),
                $lang_mail->line('email_leave_request_creation_subject'),'request');
        }
    }

    /**
     * Send a leave request cancellation email to the manager of the connected employee
     * @param int $id Leave request identifier
     * @author Guillaume Blaquiere <guillaume.blaquiere@gmail.com>
     */
    private function sendMailOnLeaveRequestCancellation($id) {
        $this->load->model('users_model');
        $this->load->model('types_model');
        $this->load->model('delegations_model');
        //We load everything from DB as the LR can be edited from HR/Employees
        $leave = $this->leaves_model->getLeaves($id);
        $user = $this->users_model->getUsers($leave['employee']);
        $manager = $this->users_model->getUsers($user['manager']);
        if (empty($manager['email'])) {
            $this->session->set_flashdata('msg', lang('leaves_cancel_flash_msg_error'));
        } else {
            //Send an e-mail to the manager
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($manager['language']);

            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);

            $this->sendGenericMail($leave,$user,$manager,$lang_mail,$lang_mail->line('email_leave_request_cancellation_title'),
                $lang_mail->line('email_leave_request_cancellation_subject'),'cancel');
        }
    }

    /**
     * Send a generic email from the collaborator to the manager (delegate in copy) when a leave request is created or cancelled
     * @param $leave Leave request
     * @param $user Connected employee
     * @param $manager Manger of connected employee
     * @param $lang_mail Email language library
     * @param $title Email Title
     * @param $detailledSubject Email detailled Subject
     * @param $emailModel template email to use
     * @author Guillaume Blaquiere <guillaume.blaquiere@gmail.com>
     *
     */
    private function sendGenericMail($leave,$user,$manager,$lang_mail,$title,$detailledSubject,$emailModel) {

        $date = new DateTime($leave['startdate']);
        $startdate = $date->format($lang_mail->line('global_date_format'));
        $date = new DateTime($leave['enddate']);
        $enddate = $date->format($lang_mail->line('global_date_format'));

        $this->load->library('parser');
        $data = array(
            'Title' => $title,
            'Firstname' => $user['firstname'],
            'Lastname' => $user['lastname'],
            'StartDate' => $startdate,
            'EndDate' => $enddate,
            'StartDateType' => $lang_mail->line($leave['startdatetype']),
            'EndDateType' => $lang_mail->line($leave['enddatetype']),
            'Type' => $this->types_model->getName($leave['type']),
            'Duration' => $leave['duration'],
            'Balance' => $this->leaves_model->getLeavesTypeBalanceForEmployee($leave['employee'] , $leave['type_name'], $leave['startdate']),
            'Reason' => $leave['cause'],
            'BaseUrl' => $this->config->base_url(),
            'LeaveId' => $leave['id'],
            'UserId' => $this->user_id
        );
        $message = $this->parser->parse('emails/' . $manager['language'] . '/'.$emailModel, $data, TRUE);

        $to = $manager['email'];
        if ($this->config->item('subject_prefix') != FALSE) {
            $subject = $this->config->item('subject_prefix');
        } else {
            $subject = '[Jorani] ';
        }
        //Copy to the delegates, if any
        $cc=null;
        $delegates = $this->delegations_model->listMailsOfDelegates($manager['id']);
        if ($delegates != '') {
            $cc = $delegates;
        }

        sendMailByWrapper($this, $subject, $message, $to, $cc);
    }



    /**
     * Delete a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $can_delete = FALSE;
        //Test if the leave request exists
        $leaves = $this->leaves_model->getLeaves($id);
        if (empty($leaves)) {
            redirect('notfound');
        } else {
            if ($this->is_hr) {
                $can_delete = TRUE;
            } else {
                if ($leaves['status'] == 1 ) {
                    $can_delete = TRUE;
                }
                if ($this->config->item('delete_rejected_requests') == TRUE ||
                    $leaves['status'] == 4) {
                    $can_delete = TRUE;
                }
            }
            if ($can_delete === TRUE) {
                $this->leaves_model->deleteLeave($id);
            } else {
                $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_error'));
                if (isset($_GET['source'])) {
                    redirect($_GET['source']);
                } else {
                    redirect('leaves');
                }
            }
        }
        $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_success'));
        if (isset($_GET['source'])) {
            redirect($_GET['source']);
        } else {
            redirect('leaves');
        }
    }

    /**
     * Cancel a leave request
     * @param int $id identifier of the leave request
     * @author Guillaume Blaquiere <guillaume.blaquiere@gmail.com>
     */
    public function cancel($id) {
        $can_cancel = FALSE;
        //Test if the leave request exists
        $leaves = $this->leaves_model->getLeaves($id);
        if (empty($leaves)) {
            redirect('notfound');
        } else {
            if ($this->is_hr) {
                $can_cancel = TRUE;
            } else {
                //If the first leave day is in the past, the collaborator can't cancel himself the leave. Only his manager can do it
                //if the user is a manager or a delegate, he can cancel the leave

                $this->load->model('delegations_model');
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($leaves['employee']);
                $is_delegate = $this->delegations_model->isDelegateOfManager($this->user_id, $employee['manager']);

                if (!$this->config->item('cancel_past_requests') &&
                    ($this->user_id != $employee['manager']) && !($is_delegate) && new DateTime($leaves['startdate']) < new DateTime()){
                    $this->session->set_flashdata('msg', lang('leaves_cancel_unauthorized_msg_error'));
                    if (isset($_GET['source'])) {
                        redirect($_GET['source']);
                    } else {
                        redirect('leaves');
                    }
                }
                if ($this->config->item('cancel_leave_request') == TRUE &&
                    $leaves['status'] == 2) {
                    $can_cancel = TRUE;
                }
                if ($this->config->item('cancel_accepted_leave') == TRUE &&
                    $leaves['status'] == 3) {
                    $can_cancel = TRUE;
                }
            }
            if ($can_cancel === TRUE) {
                $this->leaves_model->cancelLeave($id);
                if($this->config->item('notify_cancelled_requests')){
                    $this->sendMailOnLeaveRequestCancellation($id);
                }
            } else {
                $this->session->set_flashdata('msg', lang('leaves_cancel_flash_msg_error'));
                if (isset($_GET['source'])) {
                    redirect($_GET['source']);
                } else {
                    redirect('leaves');
                }
            }
        }
        $this->session->set_flashdata('msg', lang('leaves_cancel_flash_msg_success'));
        if (isset($_GET['source'])) {
            redirect($_GET['source']);
        } else {
            redirect('leaves');
        }
    }

    /**
     * Export the list of all leaves into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->load->library('excel');
        $this->load->view('leaves/export');
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $id employee id or connected user (from session)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($id = 0) {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        if ($id == 0) $id =$this->session->userdata('id');
        echo $this->leaves_model->individual($id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->workmates($this->session->userdata('manager'), $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->collaborators($this->user_id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization($entity_id) {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
        echo $this->leaves_model->department($entity_id, $start, $end, $children);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        header("Content-Type: application/json");
        $this->load->model('organization_model');
        $department = $this->organization_model->getDepartment($this->user_id);
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->department($department[0]['id'], $start, $end);
    }

    /**
     * Ajax endpoint. Result varies according to input :
     *  - difference between the entitled and the taken days
     *  - try to calculate the duration of the leave
     *  - try to detect overlapping leave requests
     *  If the user is linked to a contract, returns end date of the yearly leave period or NULL
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function validate() {
        header("Content-Type: application/json");
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        $startdate = $this->input->post('startdate', TRUE);
        $enddate = $this->input->post('enddate', TRUE);
        $startdatetype = $this->input->post('startdatetype', TRUE);     //Mandatory field checked by frontend
        $enddatetype = $this->input->post('enddatetype', TRUE);       //Mandatory field checked by frontend
        $leave_id = $this->input->post('leave_id', TRUE);
        $leaveValidator = new stdClass;
        if (isset($id) && isset($type)) {
            if (isset($startdate) && $startdate !== "") {
                $leaveValidator->credit = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type, $startdate);
            } else {
                $leaveValidator->credit = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type);
            }
        }
        if (isset($id) && isset($startdate) && isset($enddate)) {
            if (isset($leave_id)) {
                $leaveValidator->overlap = $this->leaves_model->detectOverlappingLeaves($id, $startdate, $enddate, $startdatetype, $enddatetype, $leave_id);
            } else {
                $leaveValidator->overlap = $this->leaves_model->detectOverlappingLeaves($id, $startdate, $enddate, $startdatetype, $enddatetype);
            }
        }

        //Returns end date of the yearly leave period or NULL if the user is not linked to a contract
        $this->load->model('contracts_model');
        $startentdate = NULL;
        $endentdate = NULL;
        $hasContract = $this->contracts_model->getBoundaries($id, $startentdate, $endentdate);
        $leaveValidator->PeriodStartDate = $startentdate;
        $leaveValidator->PeriodEndDate = $endentdate;
        $leaveValidator->hasContract = $hasContract;

        //Add non working days between the two dates (including their type: morning, afternoon and all day)
        if (isset($id) && ($startdate!='') && ($enddate!='')  && $hasContract===TRUE) {
            $this->load->model('dayoffs_model');
            $leaveValidator->listDaysOff = $this->dayoffs_model->listOfDaysOffBetweenDates($id, $startdate, $enddate);
            //Sum non-working days and overlapping with day off detection
            $result = $this->leaves_model->actualLengthAndDaysOff($id, $startdate, $enddate, $startdatetype, $enddatetype, $leaveValidator->listDaysOff);
            $leaveValidator->overlapDayOff = $result['overlapping'];
            $leaveValidator->lengthDaysOff = $result['daysoff'];
            $leaveValidator->length = $result['length'];
        }
        //If the user has no contract, simply compute a date difference between start and end dates
        if (isset($id) && isset($startdate) && isset($enddate)  && $hasContract===FALSE) {
            $leaveValidator->length = $this->leaves_model->length($id, $startdate, $enddate, $startdatetype, $enddatetype);
        }

        //Repeat start and end dates of the leave request
        $leaveValidator->RequestStartDate = $startdate;
        $leaveValidator->RequestEndDate = $enddate;

        echo json_encode($leaveValidator);
    }

    /**
     * Push notification from web to mobile
     * @author Vansa PHA <vansa.jm@gmail.com>
     */
    function pushNotiFromWeb($index, $leaveOrOvertimeId=null, $sms=null, $type=null){
        $url="https://onesignal.com/api/v1/notifications";

        /*
         * $index = 0 => Leave request
         * $index = 1 => Overtime request
         * $index = 2 => Accept leave
         * $index = 3 => Reject leave
         * $index = 4 => Accept overtime
         * $index = 5 => Reject overtime
         */
        if ($index === 0) {
            $typeName = $this->leaves_model->getLeaveTypeById($type);
            $userData = json_decode($this->getListDevicesForSendingFromWeb($this->session->userdata('id')), true);
            $heading = array("en" => $userData["firstname"] . " " . $userData["lastname"] . " request " . $typeName["name"]);
            $content = array("en" => $sms);
        }else if ($index === 1) {
            $userData = json_decode($this->getListDevicesForSendingFromWeb($this->session->userdata('id')), true);
            $heading = array("en" => $userData["firstname"] . " " . $userData["lastname"] . " request Overtime");
            $content = array("en" => $sms);
        }else if ($index === 2) {
            $userData = json_decode($this->getListDevicesForSendingFromWebOfManager($leaveOrOvertimeId), true);
            $heading = array("en" => "Leave request");
            $content = array("en" => "Your Leave request was accepted.");
        }else if ($index === 3) {
            $userData = json_decode($this->getListDevicesForSendingFromWebOfManager($leaveOrOvertimeId), true);
            $heading = array("en" => "Leave request");
            $content = array("en" => "Your Leave request was rejected.");
        }else if ($index === 4) {
            $userData = json_decode($this->getListDevicesForSendingFromWebOfManagerForOvertime($leaveOrOvertimeId), true);
            $heading = array("en" => "Overtime request");
            $content = array("en" => "Your Overtime request was accepted.");
        }else if ($index === 5) {
            $userData = json_decode($this->getListDevicesForSendingFromWebOfManagerForOvertime($leaveOrOvertimeId), true);
            $heading = array("en" => "Overtime request");
            $content = array("en" => "Your Overtime request was rejected.");
        }

        $fields = array(
            "app_id" => "0ac0b79a-048b-4358-8c7b-e99f7f512cb8",
            "include_player_ids" => $userData["device_player_id"],
            "headings" => $heading,
            "contents" => $content
        );

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'charset=utf-8',
                'Authorization: Basic M2EzZmJiMTktMDAyOC00NTg2LTg2N2QtNzgwMjQxMmEwNmEz'
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($ch);
        curl_close($ch);
        // $obj = json_decode($result);
        //return $obj;
    }

    /**
     * @return List of parents devices
     * @author Vansa PHA <vansa.jm@gmail.com>
     */
    public function getListDevicesForSendingFromWeb($id) {
        $this->load->model('users_model');
        $results = $this->users_model->getUserProfile($id);
        $deviceIdArr = array();
        $source = json_decode(json_encode($results[0]), true);
        $isManager = $this->session->userdata('is_manager');
        if (! $isManager) {
            foreach($results as $result) {
                $jsonArr = json_decode(json_encode($result), true);
                array_push($deviceIdArr, $jsonArr['device_player_id']);
            }
            $source["device_player_id"] = $deviceIdArr;
        }else {
            $source["device_player_id"] = [];
        }
        return json_encode($source);
    }

    /**
     * @return List of children devices
     * @author Vansa PHA <vansa.jm@gmail.com>
     */
    public function getListDevicesForSendingFromWebOfManager($id) {
        $this->load->model('onesignal_model');
        $isManager = $this->onesignal_model->isManagerCondition($id);
        $arr = array();
        if (! $isManager) {
            $children = $this->onesignal_model->pushNotiBackToChild($id);
            foreach($children as $child) {
                $json = json_decode(json_encode($child), true);
                array_push($arr, $json["player_id"]);
            }
        }else {
            $arr = [];
        }
        return json_encode(array("device_player_id" => $arr));
    }

    /**
     * @return List of children devices of OT
     * @author Vansa PHA <vansa.jm@gmail.com>
     */
    public function getListDevicesForSendingFromWebOfManagerForOvertime($id){
        $this->load->model('onesignal_model');
        $isManager = $this->onesignal_model->isManagerConditionExtra($id);
        if (! $isManager) {
            $children = $this->onesignal_model->pushNotiExtraBackToChild($id);
            $arr = array();
            foreach($children as $child) {
                $json = json_decode(json_encode($child), true);
                array_push($arr, $json["player_id"]);
            }
        }else {
            $arr = [];
        }
        return json_encode(array("device_player_id" => $arr));
    }

}