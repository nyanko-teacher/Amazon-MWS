<?php

class Amazon {

    private $access_key_id;
    private $secret_access_key;
    private $seller_id;
    private $marketplace_id;

    // アクセスキーをセットする
    public function setAccessKeyId($access_key_id) {
        $this->access_key_id = $access_key_id;
    }

    // シークレットキーをセットする
    public function setSecretAccessKey($secret_access_key) {
        $this->secret_access_key = $secret_access_key;
    }

    // セラーIDをセットする
    public function setSellerId($seller_id) {
        $this->seller_id = $seller_id;
    }

    // マーケットプレイスIDをセットする
    public function setMarketplaceId($marketplace_id) {
        $this->marketplace_id = $marketplace_id;
    }

    public function getServiceStatus() {
        $params = array();
        $params['Action'] = 'GetServiceStatus';
        $params['SellerId'] = $this->seller_id;

        return $this->createURL($params);
    }

    // 商品情報を取得する
    public function getMatchingProduct($ASIN) {
        $params = array();
        $params['Action'] = 'GetMatchingProduct';
        $params['MarketplaceId'] = $this->marketplace_id;
        $params['ASINList.ASIN.1'] = $ASIN;

        return $this->createURL($params);
    }

    public function getCompetitivePricingForASIN($ASIN) {
        $params = array();
        $params['Action'] = 'GetCompetitivePricingForASIN';
        $params['MarketplaceId'] = $this->marketplace_id;
        $params['ASINList.ASIN.1'] = $ASIN;

        return $this->createURL($params);
    }

    public function getLowestOfferListingsForASIN($condition, $ASIN) {
        $params = array();
        $params['Action'] = 'GetLowestOfferListingsForASIN';
        $params['MarketplaceId'] = $this->marketplace_id;
        $params['ItemCondition'] = $condition;
        $params['ASINList.ASIN.1'] = $ASIN;

        return $this->createURL($params);
    }

    public function getProductCategoriesForASIN($ASIN) {
        $params = array();
        $params['Action'] = 'GetProductCategoriesForASIN';
        $params['MarketplaceId'] = $this->marketplace_id;
        $params['ASIN'] = $ASIN;

        return $this->createURL($params);
    }

    // RFC3986 形式で URL エンコードする関数
    private function urlencode_rfc3986($str) {
        return str_replace('%7E', '~', rawurlencode($str));
    }

    // Amazonデータを取得するURLの作成
    private function createURL($params) {
        $baseurl = 'https://mws.amazonservices.jp/Products/2011-10-01';
        $params['AWSAccessKeyId'] = $this->access_key_id;
        $params['SellerId'] = $this->seller_id;
        $params['SignatureVersion'] = '2';
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        $params['Version'] = '2011-10-01';
        $params['SignatureMethod'] = 'HmacSHA256';

        // Timestamp パラメータを追加します
        // - 時間の表記は ISO8601 形式、タイムゾーンは UTC(GMT)
        // パラメータの順序を昇順に並び替えます
        ksort($params);

        // canonical string を作成します
        $canonical_string = '';
        foreach ($params as $k => $v) {
            $canonical_string .= '&' . $this->urlencode_rfc3986($k) . '=' . $this->urlencode_rfc3986($v);
        }
        $canonical_string = substr($canonical_string, 1);

        // 署名を作成します
        // - 規定の文字列フォーマットを作成
        // - HMAC-SHA256 を計算
        // - BASE64 エンコード
        $parsed_url = parse_url($baseurl);
        $string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
        $signature = base64_encode(hash_hmac('sha256', $string_to_sign, $this->secret_access_key, true));

        // URL を作成します
        // - リクエストの末尾に署名を追加
        $url = $baseurl . '?' . $canonical_string . '&Signature=' . $this->urlencode_rfc3986($signature);

        $xml = file_get_contents($url);

        // XMLをパースして、配列に格納する
        include_once 'UnSerializer.php';
        $options = array(
            XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => 'parseAttributes'
        );
        $unserializer = new XML_Unserializer($options);
        $unserializer->unserialize($xml);
        $result = $unserializer->getUnserializedData();

        return $result;
    }

}

?>