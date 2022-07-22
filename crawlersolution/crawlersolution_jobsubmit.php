<?php
/** 网站检测解决方案 任务检测提交接口V1 API */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v1/crawler/job/submit");
/** api version */
define("VERSION", "v1.0");
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
	$params = array(
		// 主站URL
		"siteUrl" => "http://xxx.com",
		"dataId" => "6a7c754f9de34eb8bfdf03f209fcfc02",
		//  爬虫深度/网站层级
		"level" => "1,3",
		// 单次任务周期内爬取页面的最大数量
		"maxResourceAmount" => "1000",
		// 任务类型
		"type" => "1",
		// 回调接口地址
		"callbackUrl" => "主动将结果推送给调用方的接口"
	);

	$ret = check($params);
	var_dump($ret);
	if ($ret["code"] == 200) {
		$result = $ret["result"];
        $dataId = $result["dataId"];
        $jobId = $result["jobId"];
        echo "提交成功，jobId={$jobId},dataId={$dataId}";
    }else{
    	var_dump($ret);
    }
}

main();
?>
