<?php 
require_once __DIR__ . '/lib/KdtApiClient.php';

$appId = '45180df80b914de7f8';
$appSecret = '82778e43a9dbb94b0b75616526e22747';
$client = new KdtApiClient($appId, $appSecret);


$method = 'kdt.shop.basic.get';
$params = [
	'num_iid' => 78552,
	
	'title' => 'api 测试商品 编辑 __ 22',
	'desc' => 'description here',
	'post_fee' => 0.2,
];
/**
$files = [
	[
		'url' => __DIR__ . '/file1.png',
		'field' => 'images[]',
	],
	[
		'url' => __DIR__ . '/file2.jpg',
		'field' => 'images[]',
	],
];
*/

echo '<pre>';
var_dump( 
	$client->post($method, $params)
);
echo '</pre>';
