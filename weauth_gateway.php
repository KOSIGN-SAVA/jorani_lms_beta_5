<?php
header('Content-type: text/plain; charset=utf-8');





function httpPost($url, $data)
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

/*
$url = "http://211.217.152.189/weAuthGate";

$rmd_key =  $_POST["RDM_KEY"];

$data = array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>$rmd_key));
//$data = array("JSONData" => array("_tran_cd"=>"wac_1001_01_t003","_req_data"=>array("RDM_KEY"=>"B-82e6e5d2-ba81-4ecf-b069-bb710bf4b0e9")));

$result = httpPost($url, $data);
//redirect('/controller/home');
*/


$result = "{\"_res_data\":[{\"RES_MSG\":\"정상처리 되었습니다.\",\"RES_CD\":\"0000\",\"USER_DATA\":\"{\\\"SVC_PTRN\\\":\\\"M\\\",\\\"USE_INTT_SRNO\\\":\\\"UTLZ_4\\\",\\\"CNTS_IDNT_ID\\\":\\\"KOSIGN_LMS\\\",\\\"MNGR_DSNC\\\":\\\"A\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CNTS_ID\\\":\\\"CNTS_1036\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"USER_NM\\\":\\\"홍길동\\\",\\\"CHNL_NM\\\":\\\"bizplay\\\",\\\"PTL_URL\\\":\\\"http:\\\\/\\\\/sportal.dev.weplatform.co.kr:19990\\\\/\\\",\\\"BSUN_REC\\\":[{\\\"ADRS\\\":\\\"서울영등포구여의도동\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"TPBS\\\":\\\"정보제공\\\",\\\"BSNN_NM\\\":\\\"웹케시(주)\\\",\\\"DTL_ADRS\\\":\\\"26-4교보빌딩11층575\\\",\\\"BLNG_YN\\\":\\\"N\\\",\\\"ZPCD\\\":\\\"150879\\\",\\\"DVSN_CD\\\":\\\"\\\",\\\"RPPR_NM\\\":\\\"석창규\\\",\\\"ACVT_YN\\\":\\\"Y\\\",\\\"DVSN_NM\\\":\\\"\\\",\\\"MAIN_BLNG_YN\\\":\\\"\\\",\\\"BSST\\\":\\\"서비스 외\\\",\\\"BSUN_ID\\\":\\\"4\\\"}],\\\"USE_INTT_NM\\\":\\\"웹케시(주)\\\",\\\"BSNN_NO\\\":\\\"2148635102\\\",\\\"CCTN_CHNL_NM\\\":\\\"bizplay\\\",\\\"CNTS_NM\\\":\\\"Leave Management System\\\",\\\"USER_ID\\\":\\\"s_admin1\\\",\\\"BASE_AMNN_DTTM\\\":\\\"20151112175153\\\",\\\"PTL_NM\\\":\\\"bizplay\\\",\\\"PF_HOST_TYPE\\\":\\\"\\\",\\\"RSVD4\\\":\\\"\\\",\\\"RSVD3\\\":\\\"\\\",\\\"PTL_STS\\\":\\\"O\\\",\\\"RSVD5\\\":\\\"M\\\",\\\"PTL_RCGN_CD\\\":\\\"6050d5c4-5ac2-480a-b701-cb4c5eb2e1f0\\\",\\\"RSVD2\\\":\\\"\\\",\\\"RSVD1\\\":\\\"\\\",\\\"LNGG_DSNC\\\":\\\"DF\\\",\\\"PNT_RSPR_ID\\\":\\\"s_admin1\\\",\\\"CCTN_CHNL_ID\\\":\\\"CHNL_1\\\",\\\"CHNL_ZONE_ID\\\":\\\"CHNL_1\\\",\\\"PTL_ID\\\":\\\"PTL_51\\\"}\"}],\"_res_msg\":\"정상처리 되었습니다.\",\"_tran_cd\":\"wac_1001_01_t003\",\"_res_cd\":\"0000\"}";
$test = '{"name":"prakvirak"}';
//$json_result = json_decode(stripslashes($result),true);
$json_result = json_decode($result, true);
$json_test = json_decode($test);
//echo $json_result["_res_cd"];

//get user_data
$str_user_data = $json_result["_res_data"][0]["USER_DATA"];
$json_user_data = json_decode($str_user_data, true);
//echo $json_result["_res_data"][0]["USER_DATA"];
echo $json_user_data["USE_INTT_SRNO"]; echo "\n";
echo $json_user_data["CNTS_IDNT_ID"];echo "\n";
echo $json_user_data["CHNL_ID"];echo "\n";
echo $json_user_data["USER_NM"];echo "\n";
echo $json_user_data["PTL_ID"];echo "\n";
echo $json_user_data["USER_ID"];echo "\n";
echo $str_user_data;
//echo $str_user_data;
//$CI->session->set_userdata("logged_in", true);
/* ob_start();
echo "test===============\n";
require_once('system/core/CodeIgniter.php');
//require_once 'index.php';
//define("REQUEST", "external");
ob_end_clean();
//$CI =& get_instance();

 CI_Controller::get_instance()->load->library('session');
$this->session->set_userdata("logged_in", true);

echo "===".$this->session->userdata("logged_in") . "===";  */
//echo $result;


// get bp_user_id, bp_ptl_id, bp_use_intt_srno, bp_chnl_id from USER_DATA


// check if user exists with bp_user_id, bp_ptl_id, bp_use_intt_srno

// select * from {user table} 
// where bp_user_id = ?
// and bp_ptl_id = ?
// and bp_use_intt_srno = ?


// if user is not exists
// redirect to join form
// (must redirect with bp_user_id, bp_ptl_id, bp_use_intt_srno, bp_chnl_id)



?>