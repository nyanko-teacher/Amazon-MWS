<?php

// PHPのバージョンが新しくて、エラーがたくさん出る場合コメントアウトする
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once 'amazon.php';

$obj = new Amazon();

// アクセスキー、シークレットキー、セラーID、マーケットプレイスIDをセットする
$obj->setAccessKeyId("");
$obj->setSecretAccessKey("");
$obj->setSellerId('');
$obj->setMarketplaceId('');

//使用したいオペレーションをコメントアウトする
//$hoge = $obj->getServiceStatus();
//$hoge = $obj->getMatchingProduct('');
//$hoge = $obj->getCompetitivePricingForASIN('');
//$hoge = $obj->getLowestOfferListingsForASIN('Used', '');
//$hoge = $obj->getProductCategoriesForASIN('');

echo "<pre>";
var_dump($hoge);
echo "</pre>"

?>