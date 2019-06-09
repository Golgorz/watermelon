<?php
namespace core\controllers;



/**
 * UNDER DEVELOPMENT
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class i18nController {
	
	/**
	 *
	 * @param string $localization custom language code to load (default DEFAULT_LOCALIZATION constant)
	 * @return array key values translations
	 */
	public static function localization($localization = DEFAULT_LOCALIZATION) {
		static $localeData = NULL;
		if (is_null($localeData)) {
			$langFile = ROOT_PATH . '/app/Localization/' . $localization . '.json';
			$langBase = ROOT_PATH . '/app/Localization/' . DEFAULT_LOCALIZATION . '.json';
			if (!file_exists($langFile) || $localization == DEFAULT_LOCALIZATION) {
				$jsonLang = file_get_contents($langBase);
				$langData = json_decode($jsonLang, true);
				$localeData = $langData;
			} else {
				$jsonLang = file_get_contents($langFile);
				$langData = json_decode($jsonLang, true);

				$jsonLangBase = file_get_contents($langBase);
				$localeDataBase = json_decode($jsonLangBase, true);


				$merged = self::array_merge_recursive_distinct($localeDataBase,$langData);
				$localeData = $merged;

			}

		}
		return $localeData;
	}

	public static function array_merge_recursive_distinct(array $array1, array $array2) {
		$merged = array_replace_recursive($array1,$array2);
		return $merged;
	}

	public static function getClientDefaultLang() {
		if(isset($_SERVER ["HTTP_ACCEPT_LANGUAGE"]))
			return self::parseDefaultLanguage($_SERVER ["HTTP_ACCEPT_LANGUAGE"]);
		else
			return self::parseDefaultLanguage(NULL);
	}

	public static function parseDefaultLanguage($http_accept, $deflang = DEFAULT_LOCALIZATION) {
		if(isset($http_accept) && strlen($http_accept) > 1)  {
			# Split possible languages into array
			$x = explode(",",$http_accept);
			foreach ($x as $val) {
				#check for q-value and create associative array. No q-value means 1 by rule
				if(preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i",$val,$matches))
					$lang[$matches[1]] = (float)$matches[2];
					else
						$lang[$val] = 1.0;
			}

			#return default language (highest q-value)
			$qval = 0.0;
			foreach ($lang as $key => $value) {
				if ($value > $qval) {
					$qval = (float)$value;
					$deflang = $key;
				}
			}
		}
		return strtolower($deflang);

	}
}