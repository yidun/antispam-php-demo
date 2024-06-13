# 网易易盾
http://dun.163.com
## 接口说明
- php 版本 >= 5.x
- 文件说明

```
├── list 名单添加接口演示
│   └── list_submit.php 名单添加
├── keyword 敏感词添加接口演示
│   └── keyword_submit.php 敏感词添加
│   └── keyword_delete.php 敏感词删除
├── audio 点播语音接口演示
│   ├── audio_callback.php 点播语音结果获取
│   └── audio_check.php 点播语音提交
│   └── audio_query.php 点播语音taskId查询
├── liveaudio 直播语音接口演示
│   ├── liveaudio_callback.php 直播语音结果获取
│   └── liveaudio_check.php 直播语音提交
├── filesolution 文档解决方案提交接口演示
│   ├── filesolution_callback.php 文档解决方案提交结果获取
│   └── filesolution_check.php 文档解决方案提交
│   └── filesolution_query.php 文档解决方案taskId查询
├── videosolution 点播音视频解决方案接口演示
│   ├── videosolution_callback.php 点播音视频解决方案结果获取
│   └── videosolution_check.php 点播音视频解决方案提交
│   └── videosolution_query.php 点播音视频taskId查询
├── mediasolution 融媒体解决方案接口演示
│   ├── mediasolution_callback.php 融媒体解决方案结果获取
│   └── mediasolution_check.php 融媒体解决方案提交
├── crawlersolution 网站检测解决方案接口演示
│   ├── crawersolution_callback.php 网站检测解决方案结果获取
│   └── crawersolution_submit.php 网站检测解决方案提交
├── image 图片接口演示
│   ├── callback.php 图片回调
│   └── check.php 图片检测
│   └── submit.php 图片数据抄送 
│   └── query.php 图片taskId查询
├── video
│   ├── livevideo_callback.php 直播视频流结果获取
│   ├── livevideo_check.php 直播视频流提交
│   ├── livevideo_query.php 直播视频流taskId查询
│   ├── livedata_query.php 直播截图taskId查询
│   ├── livewall_callback.php 直播电视墙回调
│   ├── livewall_check.php 直播电视墙回调流提交
│   ├── video_callback.php 视频结果获取
│   └── video_check.php 视频流提交
│   └── video_query.php 视频流taskId查询
│   └── videodata_query.php 视频截图taskId查询
└── text 文本接口演示
    ├── submit.php 文本数据抄送
    ├── query.php 文本taskId查询
    ├── callback.php 文本回调
    ├── check.php 文本检测
    └── batch_check.php 文本批量检测
└── aigc aigc解决方案接口演示
    ├── check.php aigc文本流式检测
    └── callback.php aigc文本流式检测结果获取
```

## 使用说明
- demo程序仅做接口演示，生产环境请根据实际情况补充异常处理逻辑细节。
- 检测接口签名支持MD5和国密SM3,可以在util.php里面指定，默认为MD5,如果指定后，主动回调以指定的签名形式推送审核数据。
