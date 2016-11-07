<?php

namespace Hirudoki;

class Client
{
    /**
     * @param string $uri
     * @param array $params
     * @param string $format
     * @return mixed
     */
    protected static function api($uri, $params = [], $format = 'json')
    {
        // アクセストークン
        $params['keyid']  = getenv('GNAVI_ACCESS_TOKEN');

        $params['format'] = $format;

        $q = http_build_query($params);

        $url = $uri . '?' . $q;

        $json = file_get_contents($url);

        return json_decode($json, true);
    }

    /**
     * @param array|SearchParameter $params
     * @return array
     */
    public static function getStoreList($params)
    {
        if ($params instanceof SearchParameter) {
            $params = $params->getParams();
        }

        //エンドポイントのURIとフォーマットパラメータを変数に入れる
        $uri = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";

        //取得した結果をオブジェクト化
        $obj = static::api($uri, $params);

        $result = [
            'total' => false,
            'items' => [],
        ];

        //結果をパース
        //トータルヒット件数、店舗番号、店舗名、最寄の路線、最寄の駅、最寄駅から店までの時間、店舗の小業態を出力
        foreach ((array)$obj as $key => $val) {
            if (strcmp($key, "total_hit_count") == 0) {
                $result['total'] = $val;
            }

            if (strcmp($key, "rest") == 0) {
                foreach ((array)$val as $restArray) {
                    $result['items'][] = new Rest($restArray);
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function fetchLargeCategories()
    {
        $uri = "http://api.gnavi.co.jp/master/CategoryLargeSearchAPI/20150630/";

        return static::api($uri);
    }

    /**
     * @return array
     */
    public static function fetchSmallCategories()
    {
        $uri = "http://api.gnavi.co.jp/master/CategorySmallSearchAPI/20150630/";

        return static::api($uri);
    }
}