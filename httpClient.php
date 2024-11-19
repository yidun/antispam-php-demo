<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class HttpClient
{
    private static $instance = null;

    // GuzzleHttp Client
    private $client;

    private function __construct()
    {
        // 创建 Guzzle Client 实例
        $this->client = new Client([
            'base_uri' => 'http://as.dun.163.com',
            'timeout'  => 10.0,
            'connect_timeout' => 10.0, // 设置连接超时
            'headers' => [
                'Connection' => 'keep-alive'
            ]
        ]);
    }

    // 获取单例实例
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function post($url, $data, $timeout)
    {
        try {
            $response = $this->client->request('POST', $url, [
                'form_params' => $data,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Connection' => 'keep-alive'
                ],
                'timeout' => $timeout
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function get($url, $params, $timeout, $headersMap)
    {
        $queryString = http_build_query($params);
        $url = $url . "?" . $queryString;

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headersMap,
                'verify' => false // 禁用SSL证书验证（生产环境中不推荐）
            ]);
            
            $output = $response->getBody()->getContents();
            echo "output:{$output}";
            return $output;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}

?>