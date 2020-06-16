<?php
// 用于进行代理访问
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
                $item['thumb'] = str_replace(parse_url($item['thumb'], PHP_URL_HOST), $config['cdn_address'], $item['thumb']);
            } else {
                $item['downloadUrl'] = str_replace(parse_url($item['downloadUrl'], PHP_URL_HOST), $config['cdn_address'], $item['downloadUrl']);
            }
        }
        return $item;
    }
}
