<?php
//header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
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
		
		$url = "http://211.217.152.189/weAuthGate";
		
		//get value from configeration
		$api_key = $this->config->item('API_KEY');
		$cnts_id = $this->config->item('CNTS_ID');
		
		
		
		if(isset($_POST["RDM_KEY"])){
			$rmd_key =  $_POST["RDM_KEY"];
			$data = array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>$rmd_key));
			$result = $this->httpPost($url, $data);
		}else{
			$result = "{\"_res_data\":[{\"RES_MSG\":\"정상처리 되었습니다.\",\"RES_CD\":\"0000\",\"USER_DATA\":\"{\\\"SVC_PTRN\\\":\\\"M\\\",\\\"USE_INTT_SRNO\\\":\\\"UTLZ_4\\\",\\\"CNTS_IDNT_ID\\\":\\\"KOSIGN_LMS\\\",\\\"MNGR_DSNC\\\":\\\"A\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CNTS_ID\\\":\\\"CNTS_1036\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"USER_NM\\\":\\\"홍길동\\\",\\\"CHNL_NM\\\":\\\"bizplay\\\",\\\"PTL_URL\\\":\\\"http:\\\\/\\\\/sportal.dev.weplatform.co.kr:19990\\\\/\\\",\\\"BSUN_REC\\\":[{\\\"ADRS\\\":\\\"서울영등포구여의도동\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"TPBS\\\":\\\"정보제공\\\",\\\"BSNN_NM\\\":\\\"웹케시(주)\\\",\\\"DTL_ADRS\\\":\\\"26-4교보빌딩11층575\\\",\\\"BLNG_YN\\\":\\\"N\\\",\\\"ZPCD\\\":\\\"150879\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"RPPR_NM\\\":\\\"석창규\\\",\\\"ACVT_YN\\\":\\\"Y\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"MAIN_BLNG_YN\\\":\\\"\\\",\\\"BSST\\\":\\\"서비스 외\\\",\\\"BSUN_ID\\\":\\\"4\\\"}],\\\"USE_INTT_NM\\\":\\\"웹케시(주)\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"CCTN_CHNL_NM\\\":\\\"bizplay\\\",\\\"CNTS_NM\\\":\\\"Leave Management System\\\",\\\"USER_ID\\\":\\\"s_admin1\\\",\\\"BASE_AMNN_DTTM\\\":\\\"20151112175153\\\",\\\"PTL_NM\\\":\\\"bizplay\\\",\\\"PF_HOST_TYPE\\\":\\\"\\\",\\\"RSVD4\\\":\\\"\\\",\\\"RSVD3\\\":\\\"\\\",\\\"PTL_STS\\\":\\\"O\\\",\\\"RSVD5\\\":\\\"M\\\",\\\"PTL_RCGN_CD\\\":\\\"6050d5c4-5ac2-480a-b701-cb4c5eb2e1f0\\\",\\\"RSVD2\\\":\\\"\\\",\\\"RSVD1\\\":\\\"\\\",\\\"LNGG_DSNC\\\":\\\"DF\\\",\\\"PNT_RSPR_ID\\\":\\\"s_admin1\\\",\\\"CCTN_CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CHNL_ZONE_ID\\\":\\\"CHNL_1\\\",\\\"PTL_ID\\\":\\\"PTL_51\\\"}\"}],\"_res_msg\":\"정상처리 되었습니다.\",\"_tran_cd\":\"wac_1001_01_t003\",\"_res_cd\":\"0000\"}";
		}
		
		
		//$json_result = json_decode(stripslashes($result),true);
		$json_result = json_decode($result, true);
		//$json_test = json_decode($test);
		//echo $json_result["_res_cd"];
		
		//get user_data
		$str_user_data = $json_result["_res_data"][0]["USER_DATA"];
		$json_user_data = json_decode($str_user_data, true);
		//echo $json_result["_res_data"][0]["USER_DATA"];
		$use_intt_id = $json_user_data["USE_INTT_SRNO"];
		$chnl_id = $json_user_data["CHNL_ID"];
		$user_nm =  $json_user_data["USER_NM"];
		$ptl_id = $json_user_data["PTL_ID"];
		$user_id = $json_user_data["USER_ID"];
		
		
		if(empty($user_id)){
			redirect("home");
		}else{
			$data = array(
					"user_nm" => $user_nm,
					"user_id" => $user_id,
					"ptl_id" => $ptl_id,
					"chnl_id" => $chnl_id,
					"use_intt_id" => $use_intt_id
			
			);
				
			$this->session->set_userdata($data);
			
			$loggedin = $this->users_model->checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id);
			
			if(!$loggedin){
			
				redirect("join");
				
			}else{
				
				$loggedinActive = $this->users_model->checkCredentialsWeauthActive($ptl_id, $user_id, $use_intt_id);
				
				redirect("home");
			}
			
			//request user data
				
			$user_info_api_data = array(
					"CNTS_ID"=> $cnts_id,
					"API_KEY" => $api_key,
					"CHNL_ID" => "",// $this->session->userdata("chnl_id"),
					"CCTN_CHNL_ID" => "CHNL_1",// $this->session->userdata("chnl_id"),
					"USE_INTT_ID" => "UTLZ_4",//  $this->session->userdata("use_intt_id"),
					"USER_ID" => "s_admin1",// $this->session->userdata("user_id"),
					"API_ID" => "user_infm_srch_r001" ,
					"REQ_DATA" =>  array(
							"USER_ID" => "s_admin1" //$this->session->userdata("user_id")
								
					)
						
			);
			$url_user_info = "http://sportal.dev.weplatform.co.kr:19990/BizplayGate";
				
			$result_user_infor = $this->httpPost($url_user_info, $user_info_api_data);
				
			$result_user_infor_str = urldecode ($result_user_infor);
				
			$result_user_infor_json = json_decode($result_user_infor_str);
			
			
			try{
				if(!isset($result_user_infor_json->RSLT_CD)){
					throw new Exception('date error');
				}
				if ($result_user_infor_json->RSLT_CD != "0000"){
					echo $result_user_infor_str;
					echo "error";
					return;
				}else{
					//echo $result_user_infor_str;
			
					$user_data = $result_user_infor_json->RESP_DATA;
					if(sizeof($user_data)>0){
						$user_data = $user_data[0];
					}
			
					echo $result_user_infor_str;
					//echo $user_data->PTL_URL . "<br/>";
					//echo $user_data->ATHZ_DT . "<br/>";
						
					return;
				}
			}catch (Exception $e){
				echo "external error";
				return;
			}
			
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function test(){
		//get value from configeration
		$api_key = $this->config->item('API_KEY');
		$cnts_id = $this->config->item('CNTS_ID');
		
		//$url = "http://211.217.152.189/weAuthGate";
		
		//$rmd_key =  $_POST["RDM_KEY"];
		
		//$url = "http://211.217.152.189/weAuthGate";
		if(!isset($_POST["RDM_KEY"])){
			//redirect("home");
			echo "no RDM_KEY";
			//return;
		}else{
			//echo $_POST["RDM_KEY"];
		}
		
		//$rmd_key =  $_POST["RDM_KEY"];
		
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
		$chnl_id = $json_user_data["CHNL_ID"];
		$user_nm =  $json_user_data["USER_NM"];
		$ptl_id = $json_user_data["PTL_ID"];
		$user_id = $json_user_data["USER_ID"];
		//$user_id = "prakvirak2";
		
		if(empty($user_id)){
			redirect("home");
		}else{
			$data = array(
					"user_nm" => $user_nm,
					"user_id" => $user_id,
					"ptl_id" => $ptl_id,
					"chnl_id" => $chnl_id,
					"use_intt_id" => $use_intt_id,
						
			);
			
			$this->session->set_userdata($data);
			
			$loggedin = $this->users_model->checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id);
			
			if(!$loggedin){
				
				
				
				redirect("join");
				return;
			}
			echo $loggedin . "<br/>";
			echo "finished <br/>";
			return;
			$data = array(
					"user_nm" => $user_nm,
					"user_id" => $user_id,
					"ptl_id" => $ptl_id,
					"chnl_id" => $chnl_id,
					"use_intt_id" => $use_intt_id,
					"logged_in" => TRUE
					
			);
			
			
			$this->session->set_userdata($data);
			
			//request user data
			
			$user_info_api_data = array(
					"CNTS_ID"=> $cnts_id,
					"API_KEY" => $api_key,
					"CHNL_ID" => "",// $this->session->userdata("chnl_id"),
					"CCTN_CHNL_ID" => "CHNL_1",// $this->session->userdata("chnl_id"),
					"USE_INTT_ID" => "UTLZ_4",//  $this->session->userdata("use_intt_id"),
					"USER_ID" => "s_admin1",// $this->session->userdata("user_id"),
					"API_ID" => "user_infm_srch_r001" ,
					"REQ_DATA" =>  array(
							"USER_ID" => "s_admin1" //$this->session->userdata("user_id")
							
					) 
					
			 );
			$url_user_info = "http://sportal.dev.weplatform.co.kr:19990/BizplayGate";
			
			$result_user_infor = $this->httpPost($url_user_info, $user_info_api_data);
			
			$result_user_infor_str = urldecode ($result_user_infor);
			
			$result_user_infor_json = json_decode($result_user_infor_str);
			try{
				if(!isset($result_user_infor_json->RSLT_CD)){
					throw new Exception('date error');
				}
				if ($result_user_infor_json->RSLT_CD != "0000"){
					echo $result_user_infor_str;
					echo "error";
					return;
				}else{
					//echo $result_user_infor_str;
	
					$user_data = $result_user_infor_json->RESP_DATA;
					if(sizeof($user_data)>0){
						$user_data = $user_data[0];
					}
						
					echo $result_user_infor_str;
					//echo $user_data->PTL_URL . "<br/>";
					//echo $user_data->ATHZ_DT . "<br/>";
					
					return;
				}
			}catch (Exception $e){
				echo "external error";
				return;
			}
			
			
			//return;
			
		}
		
		//checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id)
// 		$loggedin = $this->users_model->checkCredentialsWeauth($ptl_id, $user_id, $use_intt_id);
// 		if($loggedin){
// 			redirect("home");
// 		}else{
// 			redirect("join");
// 		}
		
		
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
	
	//post request api 2
	private function httpPost2($url, $data)
	{
		$data_string = "";
	
		$data_string = json_encode($data);
	
		//open connection
		$ch = curl_init($url."?JSONData=");
	
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

