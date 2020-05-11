<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务直播视频截图结果查询接口地址 */
define("API_URL", "http://as.dun.163yun.com/v1/livevideo/query/image");
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
	$params = array(
        // 直播taskId
        "taskId"=>"c633a8cb6d45497c9f4e7bd6d8218443",
        // 截图级别，1 嫌疑 2 删除确定
        "levels"=>"[1,2]",
        // 回调状态，1 待回调
        "callbackStatus"=>1,
        "pageNum"=>1,
        "pageSize"=>10
    );
	var_dump($params);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
		// 状态
        $status = $result["status"];
        // 截图结果
        $images = $result["images"];
        // 截图总数
        $count = $images["count"];
        // 截图详情
        $rows = $images["rows"];
    }else{
    	var_dump($ret);
    }
}
main();
?>
