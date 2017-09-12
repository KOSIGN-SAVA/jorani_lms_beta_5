<?php
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

class weauth extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model("users_model");
		$this->load->library("session");
		
		
		$this->load->library('polyglot');
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
		$this->lang->load('session', $this->session->userdata('language'));
		$this->lang->load('global', $this->session->userdata('language'));
		//echo $languageCode;
		
	}
	
	public function index(){
		//get value from configeration
		$api_id = $this->config->item('API_KEY');
		$cnts_id = $this->config->item('CNTS_ID');
		echo "test";
		
		//$url = "http://211.217.152.189/weAuthGate";
		if(!isset($_POST["RDM_KEY"])){
			redirect("error");
		}else{
			echo $_POST["RDM_KEY"];
		}
		
		$rmd_key =  $_POST["RDM_KEY"];
		
		
		//$data = array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>$rmd_key));
		
		$result = "{\"_res_data\":[{\"RES_MSG\":\"정상처리 되었습니다.\",\"RES_CD\":\"0000\",\"USER_DATA\":\"{\\\"SVC_PTRN\\\":\\\"M\\\",\\\"USE_INTT_SRNO\\\":\\\"UTLZ_4\\\",\\\"CNTS_IDNT_ID\\\":\\\"KOSIGN_LMS\\\",\\\"MNGR_DSNC\\\":\\\"A\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CNTS_ID\\\":\\\"CNTS_1036\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"USER_NM\\\":\\\"홍길동\\\",\\\"CHNL_NM\\\":\\\"bizplay\\\",\\\"PTL_URL\\\":\\\"http:\\\\/\\\\/sportal.dev.weplatform.co.kr:19990\\\\/\\\",\\\"BSUN_REC\\\":[{\\\"ADRS\\\":\\\"서울영등포구여의도동\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"TPBS\\\":\\\"정보제공\\\",\\\"BSNN_NM\\\":\\\"웹케시(주)\\\",\\\"DTL_ADRS\\\":\\\"26-4교보빌딩11층575\\\",\\\"BLNG_YN\\\":\\\"N\\\",\\\"ZPCD\\\":\\\"150879\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"RPPR_NM\\\":\\\"석창규\\\",\\\"ACVT_YN\\\":\\\"Y\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"MAIN_BLNG_YN\\\":\\\"\\\",\\\"BSST\\\":\\\"서비스 외\\\",\\\"BSUN_ID\\\":\\\"4\\\"}],\\\"USE_INTT_NM\\\":\\\"웹케시(주)\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"CCTN_CHNL_NM\\\":\\\"bizplay\\\",\\\"CNTS_NM\\\":\\\"Leave Management System\\\",\\\"USER_ID\\\":\\\"s_admin1\\\",\\\"BASE_AMNN_DTTM\\\":\\\"20151112175153\\\",\\\"PTL_NM\\\":\\\"bizplay\\\",\\\"PF_HOST_TYPE\\\":\\\"\\\",\\\"RSVD4\\\":\\\"\\\",\\\"RSVD3\\\":\\\"\\\",\\\"PTL_STS\\\":\\\"O\\\",\\\"RSVD5\\\":\\\"M\\\",\\\"PTL_RCGN_CD\\\":\\\"6050d5c4-5ac2-480a-b701-cb4c5eb2e1f0\\\",\\\"RSVD2\\\":\\\"\\\",\\\"RSVD1\\\":\\\"\\\",\\\"LNGG_DSNC\\\":\\\"DF\\\",\\\"PNT_RSPR_ID\\\":\\\"s_admin1\\\",\\\"CCTN_CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CHNL_ZONE_ID\\\":\\\"CHNL_1\\\",\\\"PTL_ID\\\":\\\"PTL_51\\\"}\"}],\"_res_msg\":\"정상처리 되었습니다.\",\"_tran_cd\":\"wac_1001_01_t003\",\"_res_cd\":\"0000\"}";
		
		//$json_result = json_decode(stripslashes($result),true);
		$json_result = json_decode($result, true);
		//$json_test = json_decode($test);
		//echo $json_result["_res_cd"];
		
		//get user_data
		$str_user_data = $json_result["_res_data"][0]["USER_DATA"];
		$json_user_data = json_decode($str_user_data, true);
		//echo $json_result["_res_data"][0]["USER_DATA"];
		$use_intt_id = $json_user_data["USE_INTT_SRNO"];
		$chnl_id = $json_user_data["CHNL_ID"];echo "\n";
		$user_nm =  $json_user_data["USER_NM"];echo "\n";
		$ptl_id = $json_user_data["PTL_ID"];echo "\n";
		//$user_id = $json_user_data["USER_ID"];echo "\n";
		$user_id = "uweauth3";
		if(empty($user_id)){
			redirect("error");
		}else{
			$data = array(
					"user_nm" => $user_nm,
					"user_id" => $user_id,
					"ptl_id" => $ptl_id,
					"chnl_id" => $chnl_id,
					"use_intt_id" => $use_intt_id,
					"logged_in" => TRUE
					
			);
			
			
			$this->session->set_userdata($data);
		}
		
		//checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id)
		$loggedin = $this->users_model->checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id);
		if($loggedin){
			redirect("home");
		}else{
			redirect("join");
		}
		
		
		//$url = "http://211.217.152.189/weAuthGate";
		/* if(empty($_POST['RDM_KEY'])){
			redirect('error');
			//$rmd_key = "dkslkdlkjfls";
		}else{
			$rmd_key = $_POST["RDM_KEY"];
		} */
		
		//$rmd_key = "dkslflsd";
	
		//$data = array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>$rmd_key));
		//$data = array("JSONData" => array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>"B-82e6e5d2-ba81-4ecf-b069-bb710bf4b0e9")));
		//$this->session->set_userdata("logged_in",True);
		//$result = $this->httpPost($url, $data);
		//echo $result;
		//redirect('users/create');
			
		
		
		
	}
	
	private function httpPost($url, $data)
	{
		$data_string = "";
	
		$data_string = "JSONData=".json_encode($data);
	
		//open connection
		$ch = curl_init($url."?".$data_string);
	
		//set the url, number of POST vars, POST data
		// curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		//curl_setopt($ch,CURLOPT_HEADER , true);
		curl_setopt($ch,CURLOPT_HTTPHEADER  ,  array('Content-Type:application/json', 'Content-Length: ' . strlen($data_string)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	
	
		//execute post
		$result = curl_exec($ch);
	
		//close connection
		curl_close($ch);
		return $result;
	
	
	}
}