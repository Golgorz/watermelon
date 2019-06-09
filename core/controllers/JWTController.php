<?php
namespace core\controllers;

use Firebase\JWT\JWT;
use core\helpers\Utils;

class JWTController {

	private static $key = "example_key";
	private $token;

	private $tokenId;
	private $issuedAt;
	private $notBefore;
	private $expire;
	private $issuer;

	public function __construct() {

		$this->tokenId    = base64_encode(mcrypt_create_iv(32));
		$this->issuedAt   = time();
		$this->notBefore  = $this->issuedAt + 1;
		$this->expire     = $this->notBefore + GF_JWT_AUTHENTICATION_EXPIRATION;
		$this->issuer 	  = DOMAIN_HOST;

	}

	public function initializeToken(array $data = array()) {


		$this->token = array(
				'iat'  => isset($data["iat"]) ? $data["iat"] : $this->issuedAt, // Issued at: time when the token was generated
				'aud'  => isset($data["aud"]) ? $data["aud"] : self::aud(),			// Audience claim, verifies
				'jti'  => isset($data["jti"]) ? $data["jti"] : $this->tokenId,  // Json Token Id: an unique identifier for the token
				'iss'  => isset($data["iss"]) ? $data["iss"] : $this->issuer,   // Issuer
				'nbf'  => isset($data["nbf"]) ? $data["nbf"] : $this->notBefore,// Not before
				'exp'  => isset($data["exp"]) ? $data["exp"] : $this->expire,   // Expire
				'data' => $data							// Custom data
		);

		return true;
	}


	public function encodeToken() {
		$jwt = JWT::encode($this->token, self::$key);
		return $jwt;

	}

	public static function decodeToken($token) {
		try {
			$decoded = JWT::decode($token, self::$key, array('HS256'));
			if($decoded->aud !== self::aud()) {
				return false;
			}
		} catch (\Exception $e) {
			return false;
		}

		return $decoded;
	}

	public function isValidToken($token) {
		return $this->decodeToken($token) === false ? false : true;
	}

	private static function aud()
	{
		$aud = Utils::getIp();
		$aud .= @$_SERVER['HTTP_USER_AGENT'];
		$aud .= gethostname();

		return sha1($aud);
	}

}