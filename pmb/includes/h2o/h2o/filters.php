<?php

class FilterCollection {};


class CoreFilters extends FilterCollection {
    function first($value) {
        return $value[0];
    }
    
    function last($value) {
        return $value[count($value) - 1];
    }
    
    function join($value, $delimiter = ', ') {
        return join($delimiter, $value);
    }
    
    function length($value) {
    	return count($value);
    }
    
    function urlencode($data) {
    	global $charset;
        if (is_array($data)) {
            $result;
            foreach ($data as $name => $value) {
                $result .= $name.'='.urlencode($value).'&'.$querystring;
            }
            $querystring = substr($result, 0, strlen($result)-1);
            return htmlentities($result, ENT_QUOTES, $charset);
        } else {
            return urlencode($data);
        }
    }
    
    function hyphenize ($string) {
        $rules = array('/[^\w\s-]+/'=>'','/\s+/'=>'-', '/-{2,}/'=>'-');
        $string = preg_replace(array_keys($rules), $rules, trim($string));
        return $string = trim(strtolower($string));
    }
 
    function urlize($url, $truncate = false) {
        if (preg_match('/^(http|https|ftp:\/\/([^\s"\']+))/i', $url, $match))
            $url = "<a href='{$url}'>". ($truncate ? truncate($url,$truncate): $url).'</a>';
        return $url;
    }

    function set_default($object, $default) {
        return !$object ? $default : $object;
    }
}

class StringFilters extends FilterCollection {

    function humanize($string) {
        $string = preg_replace('/\s+/', ' ', trim(preg_replace('/[^A-Za-z0-9()!,?$]+/', ' ', $string)));
        return capfirst($string);
    }
    
    function capitalize($string) {
        return ucwords(strtolower($string)) ;
    }
    
    function titlize($string) {
        return self::capitalize($string);
    }
    
    function capfirst($string) {
        $string = strtolower($string);
        return strtoupper($string{0}). substr($string, 1, strlen($string));
    }
    
    function tighten_space($value) {
        return preg_replace("/\s{2,}/", ' ', $value);
    }
    
    function escape($value, $attribute = false) {
    	global $charset;
        return htmlspecialchars($value, $attribute ? ENT_QUOTES : ENT_NOQUOTES,$charset);
    }
    
    function e($value, $attribute = false) {
        return self::escape($value, $attribute);
    }
    
    function truncate ($string, $max = 50, $ends = '...') {
        return substr_replace($string, $ends, $max - strlen($ends));
    }
    
    function limitwords($text, $limit = 50, $ends = '...') {
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);

            if (isset($pos[$limit])) {
                $text = substr($text, 0, $pos[$limit]) . $ends;
            }
        }
        return $text;
    }
}

class NumberFilters extends FilterCollection {
    function filesize ($bytes, $round = 1) {
        if ($bytes==0)
            return '0 bytes';
        elseif ($bytes==1)
            return '1 byte';
    
        $units = array(
            'bytes' => pow(2, 0), 'kB' => pow(2, 10),
            'BM' => pow(2, 20), 'GB' => pow(2, 30),
            'TB' => pow(2, 40), 'PB' => pow(2, 50),
            'EB' => pow(2, 60), 'ZB' => pow(2, 70)
        );

        $lastUnit = 'bytes';
        foreach ($units as $unitName => $unitFactor) {
            if ($bytes >= $unitFactor) {
                $lastUnit = $unitName;
            } else {
                $number = round( $bytes / $units[$lastUnit], $round );
                return number_format($number) . ' ' . $lastUnit;
            }
        }
    }

    function currency($amount, $currency = 'USD', $precision = 2, $negateWithParentheses = false) {
        $definition = array(
            'EUR' => array('�','.',','), 'GBP' => '�', 'JPY' => '�', 
            'USD'=>'$', 'AU' => '$', 'CAN' => '$'
        );
        $negative = false;
        $separator = ',';
        $decimals = '.';
        $currency = strtoupper($currency);
    
        // Is negative
        if (strpos('-', $amount) !== false) {
            $negative = true;
            $amount = str_replace("-","",$amount);
        }
        $amount = (float) $amount;

        if (!$negative) {
            $negative = $amount < 0;
        }
        if ($negateWithParentheses) {
            $amount = abs($amount);
        }

        // Get rid of negative zero
        $zero = round(0, $precision);
        if (round($amount, $precision) == $zero) {
            $amount = $zero;
        }
    
        if (isset($definition[$currency])) {
            $symbol = $definition[$currency];
            if (is_array($symbol))
                @list($symbol, $separator, $decimals) = $symbol;
        } else {
            $symbol = $currency;
        }
        $amount = number_format($amount, $precision, $decimals, $separator);

        return $negateWithParentheses ? "({$symbol}{$amount})" : "{$symbol}{$amount}";
    }
}

class HtmlFilters extends FilterCollection {
    function base_url($url, $options = array()) {
        return $url;
    }
    
    function asset_url($url, $options = array()) {
        return self::base_url($part, $options);
    }
    
    function image_tag($url, $options = array()) {
        $attr = self::htmlAttribute(array('alt','width','height','border'), $options);
        return sprintf('<img src="%s" %s/>', $url, $attr);
    }

    function css_tag($url, $options = array()) {
        $attr = self::htmlAttribute(array('media'), $options);
        return sprintf('<link rel="stylesheet" href="%s" type="text/css" %s />', $url, $attr);
    }

    function script_tag($url) {
        return sprintf('<script src="%s" type="text/javascript"></script>', $url);
    }
    
    function links_to($text, $url, $options = array()) {
        $attrs = self::htmlAttribute(array('ref'), $options);
        $url = self::base_url($url, $options);
        return sprintf('<a href="%s" %s>%s</a>', $url, $attrs, $text);
    }
    
    function links_with ($url, $text, $options = array()) {
        return self::links_to($text, $url, $options);
    }
    
    function strip_tags($text) {
        $text = preg_replace(array('/</', '/>/'), array(' <', '> '),$text);
        return strip_tags($text);
    }

    function linebreaks($value, $format = 'p') {
        if ($format == 'br')
        return h2o_nl2br($value);
        return nl2pbr($value);
    }
    
    function nl2br($value) {
        return str_replace("\n", "<br />\n", $value);
    }
    
    function nl2pbr($value) {
        $result = array();
        $parts = preg_split('/(\r?\n){2,}/m', $value);
        foreach ($parts as $part) {
            array_push($result, '<p>' . h2o_nl2br($part) . '</p>');
        }
        return implode("\n", $result);
    }

    private static function htmlAttribute($attrs = array(), $data = array()) {
        $attrs = self::extract(array_merge(array('id', 'class', 'title', "style"), $attrs), $data);
        
        $result = array();
        foreach ($attrs as $name => $value) {
            $result[] = "{$name}=\"{$value}\"";
        }
        return join(' ', $result);
    }

    private static function extract($attrs = array(), $data=array()) {
        $result = array();
        if (empty($data)) return array();
        foreach($data as $k => $e) {
            if (in_array($k, $attrs)) $result[$k] = $e;
        }
        return $result;
    }
}

class DatetimeFilters extends FilterCollection {
    function date($time, $format = 'jS F Y H:i') {
        return date($format, strtotime($time));
    }

    function relative_time($timestamp, $format = 'g:iA') {
        $timestamp = strtotime($timestamp);
        $time   = mktime(0, 0, 0);
        $delta  = time() - $timestamp;
        $string = '';
        
        if ($timestamp < $time - 86400) {
            return date("F j, Y, g:i a", $timestamp);
        }
        if ($delta > 86400 && $timestamp < $time) {
            return "Yesterday at " . date("g:i a", $timestamp);
        }

        if ($delta > 7200)
            $string .= floor($delta / 3600) . " hours, ";
        else if ($delta > 3660)
            $string .= "1 hour, ";
        else if ($delta >= 3600)
            $string .= "1 hour ";
        $delta  %= 3600;
        
        if ($delta > 60)
            $string .= floor($delta / 60) . " minutes ";
        else
            $string .= $delta . " seconds ";
        return "$string ago";
    }

    function relative_date($time) {
        $time = strtotime($time);
        $today = strtotime(date('M j, Y'));
        $reldays = ($time - $today)/86400;
        if ($reldays >= 0 && $reldays < 1) {
            return 'today';
        } else if ($reldays >= 1 && $reldays < 2) {
            return 'tomorrow';
        } else if ($reldays >= -1 && $reldays < 0) {
            return 'yesterday';
        }
        
        if (abs($reldays) < 7) {
            if ($reldays > 0) {
                $reldays = floor($reldays);
                return 'in ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
            } else {
                $reldays = abs(floor($reldays));
                return $reldays . ' day'  . ($reldays != 1 ? 's' : '') . ' ago';
            }
        }
        if (abs($reldays) < 182) {
            return date('l, F j',$time ? $time : time());
        } else {
            return date('l, F j, Y',$time ? $time : time());
        }
    }
    
    function relative_datetime($time) {
        $date = self::relative_date($time);
        if ($date == 'today') {
            return self::relative_time($time);
        }
        return $date;
    }
}

/*  Ultizie php funciton as Filters */
h2o::addFilter(array('md5', 'sha1', 'numberformat'=>'number_format', 'wordwrap', 'trim', 'upper' => 'strtoupper', 'lower' => 'strtolower'));

/* Add filter collections */
h2o::addFilter(array('CoreFilters', 'StringFilters', 'NumberFilters', 'DatetimeFilters', 'HtmlFilters'));

/* Alias default to set_default */
h2o::addFilter('default', array('CoreFilters', 'set_default'));