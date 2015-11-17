<?php

define ("TOKEN", "miss_token");

# 签名校验
function checkSignature() {
	$signature = $_GET['signature'];
	$echostr = $_GET['echostr'];

	$nonce = $_GET['nonce'];
	$timestamp = $_GET['timestamp'];
	$ary = array($nonce, $timestamp, TOKEN);
	sort($ary);
	$str = implode($ary);

	$codeStr = sha1($str);
	if ($codeStr == $signature) {
		return true;
	} else {
		return false;
	}
}

if (false == checkSignature()) {
	exit(0);
}

# 用户发送请求信息不携带此字段
$echostr = $_GET['echostr'];
if ($echostr) {
	echo $echostr;
	exit(0);
}

// TODO
// 分析用户输入并作出处理

function getPostData() {
	global $HTTP_RAW_POST_DATA;
	return $HTTP_RAW_POST_DATA;
}
$postData = getPostData();

# 解析XML字符串
$xmlObject = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
if (!$xmlObject) {
	echo "错误输入, 即将退出.";
	exit(0);
}

$fromName = $xmlObject->FromUserName;
$toName   = $xmlObject->ToUserName;
$msgType  = $xmlObject->MsgType;

if ('text' == $msgType) {
	$content = $xmlObject->Content;
	$returnMsg = '开启复读机模式. 您刚才好像在说: "' . $content . '" .';
} else {
	$returnMsg = '对不起, 目前仅支持文本消息.';
}

$returnXmlTemplate = <<<XTEMP
<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[text]]></MsgType>
	<Content><![CDATA[%s]]></Content>
	<FuncFlag>0</FuncFlag>
</xml>
XTEMP;

$returnXml = sprintf($returnXmlTemplate, $fromName, $toName, time(), $returnMsg);
echo $returnXml;


?>
