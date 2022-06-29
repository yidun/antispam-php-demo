<?php
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 易盾反垃圾云服务点播結果查詢接口地址 */
define("API_URL", "http://as.dun.163.com/v4/video/query/task");
/** api version */
define("VERSION", "v4");
/** API timeout*/
define("API_TIMEOUT", 10);
require("../util.php");

/**
 * 反垃圾请求接口简单封装
 * $params 请求参数
 */
function check($params)
{
    $params["secretId"] = SECRETID;
    $params["businessId"] = BUSINESSID;
    $params["version"] = VERSION;
    $params["timestamp"] = time() * 1000;// time in milliseconds
    $params["nonce"] = sprintf("%d", rand()); // random int

    $params = toUtf8($params);
    $params["signature"] = gen_signature(SECRETKEY, $params);
    // var_dump($params);

    $result = curl_post($params, API_URL, API_TIMEOUT);
    if ($result === FALSE) {
        return array("code" => 500, "msg" => "file_get_contents failed.");
    } else {
        return json_decode($result, true);
    }
}

// 简单测试
function main()
{
    echo "mb_internal_encoding=" . mb_internal_encoding() . "\n";
    $taskIds = array("a42d1727d40f47cba8de3458820cb814", "01fb38b09d694dbaae0560e62c1fc2af");
    $params = array(
        "taskIds" => json_encode($taskIds)
    );

    $ret = check($params);
    //var_dump($ret);
    if ($ret["code"] == 200) {
        $result_array = $ret["result"];
        foreach ($result_array as $res_index => $result) {
            $status = $result["status"];
            $taskId = $result["taskId"];
            // status任务状态， 0：正常，1：已过期，2：数据不存在，3：检测中
            if ($status == 1) {
                echo "数据已过期，status=" . $status . "taskId=" . $taskId;
            } else if ($status == 2) {
                echo "数据不存在，status=" . $status . "taskId=" . $taskId;
            } else if ($status == 3) {
                echo "数据检测中，status=" . $status . "taskId=" . $taskId;
            } else {
                $antispam = $result["antispam"];
                $suggestion = $result["suggestion"];
                if ($suggestion == 0) {
                    echo "taskId={$taskId}，结果：通过";
                } else if ($suggestion == 1 || $suggestion == 2) {
                    // // 从pictures里获取证据信息，详细说明见接口文档
                    $pictures = $result["pictures"];
                    foreach ($pictures as $res_index => $picture) {
                        $startTime = $picture["startTime"];
                        $endTime = $picture["endTime"];
                        $type = $picture["type"];
                        $url = $picture["url"];
                        $labelArray = $picture["labels"];
                        foreach ($labelArray as $res_index => $labelEle) {
                            $label = $labelEle["label"];
                            $level = $labelEle["level"];
                            $rate = $labelEle["rate"];
                        }
                    }
                }

            }
        }
    } else {
        var_dump($ret); // error handler
    }
}

main();
?>
