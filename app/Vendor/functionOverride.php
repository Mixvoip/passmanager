<?php
/**Overrides standard Functions
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 26.04.16
 * Time: 08:08
 */

/**
 * Implement array_column for php < 5.5
 * http://php.net/manual/en/function.array-column.php#118831
 */
if (!function_exists('array_column')) {
    function array_column($input, $column_key, $index_key = null)
    {
        $arr = array_map(function ($d) use ($column_key, $index_key) {
            if (!isset($d[$column_key])) {
                return null;
            }
            if ($index_key !== null) {
                return array($d[$index_key] => $d[$column_key]);
            }
            return $d[$column_key];
        }, $input);

        if ($index_key !== null) {
            $tmp = array();
            foreach ($arr as $ar) {
                if (isset($ar)) $tmp[key($ar)] = current($ar);
            }
            $arr = $tmp;
        }
        return $arr;
    }
}
/**
 * Is url valid
 */
function isUrlValid($url)
{
    //Filter don't look at the protocol so ftp:// or ssh:// or even blub:// is valid 
    $url_filter = filter_var($url, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE);
    if (isset($url_filter)) {
        return 1;
    } else {
        return 0;
    }
}
