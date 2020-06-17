<?php
// 用于进行代理访问 借鉴自OneIndexMod魔改版
class cdn
{
    static function proxy($item)
    {
        $config = config('@base');
        if (!isset($config['cdn_address']) || !isset($config['cdn_regex'])) {
            return $item;
        }
        $cdnAddress = $config['cdn_address'];
        $cdnRegex = $config['cdn_regex'];
        if ($cdnAddress === '' || $cdnRegex === '') {
            return $item;
        }

        // var_dump($item);
        if(preg_match($cdnRegex, $item['name'])) {
            if (isset($item['thumb'])) {
                $host = parse_url($item['thumb'], PHP_URL_HOST);
                $item['thumb'] = str_replace($host, $config['cdn_address'], $item['thumb']) . '&odFileSize=' . $item['size'] . '&odHost='.$host;
            } else {
                $host = parse_url($item['downloadUrl'], PHP_URL_HOST);
                $item['downloadUrl'] = str_replace($host, $config['cdn_address'], $item['downloadUrl']) . '&odFileSize=' . $item['size'] . '&odHost='.$host;
            }
        }
        return $item;
    }
}
