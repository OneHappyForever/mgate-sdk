在程序部署目录下执行下方安装命令

```
curl -fsSL https://raw.githubusercontent.com/mGate/mgate-sdk/master/card-system/install.php | php
```

在风铃发卡后台->管理中心->支付渠道中添加新支付渠道

|参数名|描述|
|----|----|
|名称|用于支付时显示使用|
|费率|根据需求设置|
|驱动|MGate|
|方式|mgate|
|配置|{"api":"MGate后台->开发中心->接口地址","app_id":"MGate后台->开发中心->App ID","app_secret":"MGate后台->开发中心->App Secret(密钥)"}|

在风铃发卡后台->管理中心->支付渠道中选择前台支付根据如下内容新增

|参数名|描述|
|----|----|
|名称|用于支付时显示使用|
|图片|用于支付时显示使用|
|子渠道|选择上方添加的支付渠道|
|启用|勾选电脑端和手机端|