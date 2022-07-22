<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务文本結果查詢接口地址 */
define("API_URL", "http://as.dun.163.com/v1/text/query/task");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params){
	$params["secretId"] = SECRETID;
	$params["businessId"] = BUSINESSID;
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

	$params["signatureMethod"] = SIGNATURE_METHOD;
	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$taskIds = array("c679d93d4a8d411cbe3454214d4b1fd7","49800dc7877f4b2a9d2e1dec92b988b6");
	$params = array(
		"taskIds"=>json_encode($taskIds)
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
		foreach($result as $index => $value){
		    $action = $value["action"];
		    $taskId = $value["taskId"];
		    $status = $value["status"];
		    $callback = $value["callback"];
		    $labelArray = $value["labels"];
		    if ($action == 0) {
			echo "taskId={$taskId}，status={$status}，callback={$callback}，文本查询结果：通过\n";
		    } else if ($action == 2) {
			echo "taskId={$taskId}，status={$status}，callback={$callback}，文本查询结果：不通过，分类信息如下：".json_encode($labelArray)."\n";
	            }
		}
    	}else{
    		var_dump($ret); // error handler
    	}
}

main();
?>
