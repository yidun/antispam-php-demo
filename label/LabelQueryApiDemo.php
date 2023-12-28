<?php
/** 商户密钥，请联系支持人员配置，对应客户秘钥-AccessKey SECRETID */
define("SECRETID", "your_secret_id");
/** 商户密钥，请联系支持人员配置，对应客户秘钥-AccessKey */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务标签获取在线接口地址 */
define("API_URL", "https://openapi.dun.163.com/openapi/v2/antispam/label/query");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 标签获取接口简单封装
 * $params 请求参数
 */
function queryLabel($params){

    $params["businessId"] = BUSINESSID;
    $params = toUtf8($params);

    $headerMap = array(
        'X-YD-SECRETID' => SECRETID,
        'X-YD-TIMESTAMP' => strval(time() * 1000),
        'X-YD-NONCE' => sprintf("%d", rand()),
    );

    $sign = genOpenApiSignature(SECRETKEY, $params, $headerMap);
    $headerMap["X-YD-SIGN"] = $sign;

    $result = curl_get_set_header($params, API_URL, API_TIMEOUT, $headerMap);
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
        // 客户ID，请联系您的易盾策略经理获取
        "clientId" => "clientId",
        //指定标签支持的业务类型  文本-100，图片-200，视频-300，音频-400
        "businessTypes" => "100,200",
        //指定标签的最大层级
        "maxDepth" => "3"
    );
    var_dump($params);

    $ret = queryLabel($params);
    var_dump($ret);
    if ($ret["code"] == 200) {
        // do something
    }else{
        var_dump($ret);
    }
}
main();
?>
