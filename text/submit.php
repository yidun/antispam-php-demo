<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务文本抄送接口地址 */
define("API_URL", "https://as.dun.163yun.com/v1/text/submit");
/** api version */
define("VERSION", "v1");
/** API timeout*/
define("API_TIMEOUT", 2);
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

	$params = toUtf8($params);
	$params["signature"] = gen_signature(SECRETKEY, $params);
	// var_dump($params);

	$result = curl_post($params, API_URL, API_TIMEOUT);
	// var_dump($result);
	if($result === FALSE){
		return array("code"=>500, "msg"=>"file_get_contents failed.");
	}else{
		return json_decode($result, true);	
	}
}

// 简单测试
function main(){
    echo "mb_internal_encoding=".mb_internal_encoding()."\n";
	$texts = array();
	array_push($texts, array(// level=1表示传图片嫌疑
		"dataId" => "ebfcad1c-dba1-490c-b4de-e784c2691768",
		"action" => 1,
		"content" => "易盾测试内容！v1接口!"
	));
	array_push($texts, array( // level=2表示传图片删除
		"dataId" => "ebfcad1c-dba1-490c-b4de-e784c2691768",
        "action" => 0,
        "content" => "易盾测试内容！v1接口!"
	));
	$params = array(
		"texts"=>json_encode($texts)
	);
	var_dump($params);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$resultArray = $ret["result"];
		foreach($resultArray as $index => $text_ret){
		    $dataId = $text_ret["dataId"];
            $taskId = $text_ret["taskId"];
            echo "文本提交返回dataId={$dataId}，taskId:{$taskId}\n";
		}
    }else{
    	var_dump($ret);
    }
}
main();
?>
