<?php
namespace core\controllers\http\psr\interfaces;

/**
 * Cookies Interface
 *
 */
interface CookiesInterface
{
    public function get($name, $default = null);
    public function set($name, $value);
    public function toHeaders();
    public static function parseHeader($header);
}
