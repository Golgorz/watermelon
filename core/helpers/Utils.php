<?php
namespace core\helpers;

/**
 * UNDER DEVELOPMENT
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.2
 */
class Utils {


    private static $reform;


    public static function isValidEmail($email) {
    	if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		return true;
    	} else {
    		return false;
    	}
    }


	public static function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}

	/**
	 * Returns a random string of a specified length
	 * @param int $length
	 * @return string $key
	 */
	public static function getRandomKey($length = 20)
	{
		$chars = "A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6";
		$key = "";

		for ($i = 0; $i < $length; $i++) {
			$key .= $chars{mt_rand(0, strlen($chars) - 1)};
		}

		return $key;
	}

	public static function getFileExtension($path) {
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		return $ext;
	}

	public static function getFileMimeType($file) {
		$ftype = 'unknown';
		$finfo = @finfo_open(FILEINFO_MIME);
		if ($finfo !== FALSE) {
   			$fres = @finfo_file($finfo, $file);
   			if ( ($fres !== FALSE)  && is_string($fres)  && (strlen($fres)>0)) {
            	$ftype = $fres;
        	}
   			@finfo_close($finfo);
		}
		return $ftype;
	}

	public static function array_utf8_decoder($array)
	{
		array_walk_recursive($array, function(&$item, $key){
			if(is_string($item)){
				$item = utf8_decode($item);
			}
		});

			return $array;
	}
	
	public static function array_utf8_encoder($array)
	{
	    array_walk_recursive($array, function(&$item, $key){
	        if(is_string($item)){
	            $item = utf8_encode($item);
	        }
	    });

	        return $array;
	}


	public static function stringToUTF8 ($str) {
		$decoded = utf8_decode($str);
			return $decoded;
	}


	public static function convertArrayKeysToUtf8(array $array) {
		$convertedArray = array();
		foreach($array as $key => $value) {
			if(!mb_check_encoding($key, 'UTF-8')) $key = utf8_encode($key);
			if(is_array($value)) $value = self::convertArrayKeysToUtf8($value);

			$convertedArray[$key] = $value;
		}
		return $convertedArray;
	}

	public static function formatDateTime($fecha = null){
		if($fecha == null) {
			$fecha = date('d/m/Y H:i:s');
		}
		$date = str_replace('/', '-', $fecha);
		$date = date('Y-m-d H:i:s', strtotime($date));
		$time = strtotime($date);
		$date = new \DateTime();
		if($fecha != null) {
			$date->setTimestamp($time);
		}
		return $date;
	}

	public static function xssafe($data,$encoding='UTF-8') {
	    if(self::$reform == null) self::$reform = new Reform();
		if(is_array($data)){
			foreach ($data as &$value) {
				if (!is_array($value)) { $value = self::xssafe($value); }
				else { self::xssafe($value); }
			}
		} else {
			$data = self::$reform->HtmlEncode($data);//htmlspecialchars($data,ENT_QUOTES | ENT_HTML401);
			return $data;
		}

	}



	/**
	 * This is the list of currently registered HTTP status codes.
	 *
	 * @var array
	 */
	public static $statusCodes = [
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authorative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status', // RFC 4918
			208 => 'Already Reported', // RFC 5842
			226 => 'IM Used', // RFC 3229
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot', // RFC 2324
			421 => 'Misdirected Request', // RFC7540 (HTTP/2)
			422 => 'Unprocessable Entity', // RFC 4918
			423 => 'Locked', // RFC 4918
			424 => 'Failed Dependency', // RFC 4918
			426 => 'Upgrade Required',
			428 => 'Precondition Required', // RFC 6585
			429 => 'Too Many Requests', // RFC 6585
			431 => 'Request Header Fields Too Large', // RFC 6585
			451 => 'Unavailable For Legal Reasons', // draft-tbray-http-legally-restricted-status
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version not supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage', // RFC 4918
			508 => 'Loop Detected', // RFC 5842
			509 => 'Bandwidth Limit Exceeded', // non-standard
			510 => 'Not extended',
			511 => 'Network Authentication Required', // RFC 6585
	];
	
	
	public static function getIp()
	{
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	public static function addTrailingSlash(&$url) {
		if (substr($url, -1) !== '/') {
			$url .= '/';
		}
		return $url;
	}

	public static function addLeadingSlash(&$url) {
		if (strpos($url, "\/") != 0) {
			$url = '/' . $url;
		}
		return $url;
	}
	
	public static function stringStartsWith($string, $subString, $caseSensitive = true) {
	
		if ($caseSensitive === false) {
	
			$string		= mb_strtolower($string);
			$subString  = mb_strtolower($subString);
	
		}
	
		if (mb_substr($string, 0, mb_strlen($subString)) == $subString) {
	
			return true;
	
		} else {
	
			return false;
	
		}
	
	}
	
	public static function stringEndsWith($string, $subString, $caseSensitive = true) {
	
		if ($caseSensitive === false) {
	
			$string		= mb_strtolower($string);
			$subString  = mb_strtolower($subString);
	
		}
	
		$strlen 			= strlen($string);
		$subStringLength 	= strlen($subString);
	
		if ($subStringLength > $strlen) {
	
			return false;
	
		}
	
		return substr_compare($string, $subString, $strlen - $subStringLength, $subStringLength) === 0;
	
	}
	
	public static function stringContains($haystack, $needle, $caseSensitive = true) {
	
		if ($caseSensitive === false) {
	
			$haystack	= mb_strtolower($haystack);
			$needle    	= mb_strtolower($needle);
	
		}
	
		if (mb_substr_count($haystack, $needle) > 0) {
	
			return true;
	
		} else {
	
			return false;
	
		}
	
	}
	

}