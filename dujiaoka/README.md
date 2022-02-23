Install Dujiaoka for MGate
```
curl -fsSL https://raw.githubusercontent.com/mGate/mgate-sdk/master/dujiaoka/install.php | php
```

在独角数卡后台->配置->支付配置中新增如下支付通道

|参数名|描述|
|----|----|
|支付名称|随意填写将在支付时展示|
|商户ID|MGate后台->开发中心->App ID|
|商户KEY|MGate后台->开发中心->接口地址|
|商户密钥|MGate后台->开发中心->App Secret(密钥)|
|支付标识|mgate|
|支付场景|通用|
|处理方式|跳转|
|支付处理路由|pay/mgate|