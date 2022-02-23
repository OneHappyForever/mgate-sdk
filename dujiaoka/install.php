<?php

function writeFileByUrl($url, $filepath) {
	$file = @file_get_contents($url);
	if (!$file) {
		showLog("{$url} 下载失败");
		return false;
	}
	if (!file_put_contents($filepath, $file)) {
		showLog("{$filepath} 写入失败");
		return false;	
	}
	showLog("{$filepath} 下载完毕");
	return true;
}

function showLog($text) {
	$date = date('Y-m-d H:i:s');
	echo "[$date]" . $text . PHP_EOL;
}

function insertAfterTarget($filePath, $insertCont, $target)
{
    $result = null;
    $fileCont = file_get_contents($filePath);
    if (strpos($fileCont, "mgate")) return;
    $targetIndex = strpos($fileCont, $target) - 1;
    if ($targetIndex !== false) {
        $chLineIndex = strpos(substr($fileCont, $targetIndex), "\n") + $targetIndex;
        if ($chLineIndex !== false) {
            $result = substr($fileCont, 0, $chLineIndex + 1) . $insertCont . "\n" . substr($fileCont, $chLineIndex + 1);
            $fp = fopen($filePath, "w+");
            fwrite($fp, $result);
            fclose($fp);
        }
    }
}


$gatewayDir = "app/Http/Controllers/Pay";



if (!is_dir($gatewayDir)) {
	mkdir($gatewayDir, 0755, true);
}

$baseUrl = "https://raw.githubusercontent.com/mgate/mgate-sdk/master/dujiaoka/resources";

$files = [
	"MgateController.php"
];

showLog("SDK部署中...");

foreach ($files as $file) {
	if (!writeFileByUrl("{$baseUrl}/{$file}", "{$gatewayDir}/{$file}")) {
		$fail = true;
		break;
	}
}

showLog("设置路由...");
insertAfterTarget('routes/common/pay.php', "	// Mgate
    Route::get('mgate/{payway}/{orderSN}', 'MgateController@gateway');
    Route::post('mgate/notify_url', 'MgateController@notifyUrl');
    Route::get('mgate/return_url', 'MgateController@returnUrl');", "});");

if ($fail) {
	showLog("安装失败");
} else {
	showLog("安装成功");
}
