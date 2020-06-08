<?php
/** 名单添加接口 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 易盾反垃圾云服务名单添加接口地址 */
define("API_URL", "http://as.dun.163.com/v1/list/submit");
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
	$params["version"] = VERSION;
	$params["timestamp"] = time() * 1000;// time in milliseconds
	$params["nonce"] = sprintf("%d", rand()); // random int

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
    $lists = array("用户黑名单1","用户黑名单2");
	$params = array(
	    // 1: 白名单，2: 黑名单，4: 必审名单，8: 预审名单
		"listType"=>"2",
		// 1: 用户名单，2: IP名单
		"entityType"=>"1",
		"lists"=>implode(",",$lists)
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200 && $ret["result"]) {
        echo "提交成功";
    }else{
    	var_dump($ret);
    }
}

main();
?>
