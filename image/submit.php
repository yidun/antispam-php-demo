<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务图片抄送接口地址 */
define("API_URL", "http://as.dun.163yun.com/v1/image/submit");
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
	$images = array();
	array_push($images, array(// level=1表示传图片嫌疑
		"name" => "https://nos.netease.com/yidun/2-0-0-e22106dfc9914a758b47a14fe86c80a9.jpg",
		"level" => 1,
		"data" => "https://nos.netease.com/yidun/2-0-0-e22106dfc9914a758b47a14fe86c80a9.jpg",
		// "account"=>"php@163.com",
        // "ip"=>"123.115.77.137",
        // "deviceId"=>"deviceId",
	));
	array_push($images, array( // level=2表示传图片删除
		"name" => "{\"imageId\": 33451123, \"contentId\": 78978}",
		"level" => 2,
		"data" => "https://nos.netease.com/yidun/2-0-0-a6133509763d4d6eac881a58f1791976.jpg"
	));
	$params = array(
		"images"=>json_encode($images)
	);
	var_dump($params);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {

		$resultArray = $ret["result"];
		foreach($resultArray as $index => $image_ret){
		    $name = $image_ret["name"];
		    $taskId = $image_ret["taskId"];
		    echo "图片提交返回name={$name}，taskId:{$taskId}\n";
		}
    }else{
    	var_dump($ret);
    }
}
main();
?>
