<?php
/** 调用易盾反垃圾云服务查询点播语音结果接口API示例-v4版本 */
/** 产品密钥ID，产品标识 */
define("SECRETID", "your_secret_id");
/** 产品私有密钥，服务端生成签名信息使用，请严格保管，避免泄露 */
define("SECRETKEY", "your_secret_key");
/** 业务ID，易盾根据产品业务特点分配 */
define("BUSINESSID", "your_business_id");
/** 接口地址 */
define("API_URL", "http://as.dun.163.com/v4/audio/query/task");
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
    // var_dump($result);
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
    $taskIds = array("202b1d65f5854cecadcb24382b681c1a", "0f0345933b05489c9b60635b0c8cc721");
    $params = array(
        "taskIds" => json_encode($taskIds)
    );
    var_dump($params);

    $ret = check($params);
    var_dump($ret);
    if ($ret["code"] == 200) {
        $resultArray = $ret["result"];
        foreach ($resultArray as $index => $audio_ret) {
            // 反垃圾检测结果
            $antispam = $audio_ret["antispam"];
            if ($antispam != null) {
                $name = $antispam["name"];
                $taskId = $antispam["taskId"];
                // 检测状态，2-检测成功，3-检测失败
                $status = $antispam["status"];
                if ($status == 2) {
                    // 建议结果 0-通过 1-嫌疑 2-删除
                    $suggestion = $antispam["suggestion"];
                    $labelArray = $antispam["labels"];
                    echo "taskId={$taskId}，name={$name}，suggestion={$suggestion}\n";
                    foreach ($labelArray as $index => $label) {
                        // subLabels为二级分类数组，根据需要解析
                        $subLabels = $label["subLabels"];
                        echo "label:{$label["label"]}, level={$label["level"]}, rate={$label["rate"]}\n";
                    }
                    if ($suggestion == 0) {
                        echo "#机器检测结果：最高等级为：正常\n";
                    } else if ($suggestion == 1) {
                        echo "#机器检测结果：最高等级为：嫌疑\n";
                    } else if ($suggestion == 2) {
                        echo "#机器检测结果：最高等级为：确定\n";
                    }
                } else {
                    echo "检测失败,taskId:{$taskId}, status={$status}, name={$name}\n";
                }
            }
            // 语种检测数据
            $language = $audio_ret["language"];
            if ($language != null) {
                echo "语种检测数据: " . json_encode($language);
            }
            // 语音翻译数据
            $asr = $audio_ret["asr"];
            if ($asr != null) {
                echo "语音翻译数据: " . json_encode($asr);
            }
            // 人声属性检测数据
            $voice = $audio_ret["voice"];
            if ($voice != null) {
                echo "人声属性检测数据: " . json_encode($voice);
            }
        }
    } else {
        var_dump($ret);
    }
}

main();
?>
