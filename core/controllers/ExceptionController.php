<?php
namespace core\controllers;


use core\helpers\Utils;
use core\controllers\monolog\LoggerController;
use core\controllers\http\psr\Response;

/**
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class ExceptionController {

	private static $msg;
	private static $code;

	public static function PermissionDenied() {
		self::$code = 401;
		self::$msg = "No tiene permisos para esta operacion.";
		self::showMessage();

	}
	public static function noOPFound() {
		self::$code = 404;
		self::$msg = "Operacion OP no encontrada.";
		self::showMessage();
	}

	public static function notOwnerOfResource() {
		self::$code = 400;
		self::$msg = "Not owner of requested resource";
		self::showMessage();
	}

	public static function noSOPFound() {
		self::$code = 404;
		self::$msg = "Operacion SOP no encontrada.";
		self::showMessage();
	}
	public static function noContent() {
		self::$code = 404;
		self::$msg = "Sin contenidos.";
		self::showMessage();
	}
	public static function missingParams($param) {
		self::$code = 400;
		self::$msg = "Invalid request, missing " . $param;
		self::showMessage();
	}
	public static function entityNotFound() {
		self::$code = 404;
		self::$msg = "Solicitud entidad no encontrada.";
		self::showMessage();
	}

	public static function classNotFound() {
		self::$code = 404;
		self::$msg = "Class not found exception";
		self::showMessage();
	}

	public static function subdomainNotFound() {
		self::$code = 404;
		self::$msg = "Subdominio no encontrado.";
		self::showMessage();
	}

	public static function routeNotFound() {
		self::$code = 400;
		self::$msg = "Route not found";
		self::showMessage();
	}

	public static function invalidUrl() {
		self::$code = 400;
		self::$msg = "Url no valida.";
		self::showMessage();
	}

	public static function routeBlocked() {
		self::$code = 400;
		self::$msg = "Sin permisos para esta ruta";
		self::showMessage();
	}

	public static function invalidEntityLogic() {
		self::$code = 404;
		self::$msg = "Archivo de logica asociada a la entidad no encontrada.";
		self::showMessage();
	}

	public static function invalidEntityLogicAsociation() {
		self::$code = 404;
		self::$msg = "La entidad no tiene logica asociada.";
		self::showMessage();
	}

	public static function invalidUserType() {
		self::$code = 403;
		self::$msg = "Tipo de usuario no valido.";
		self::showMessage();
	}

	public static function entityDataError() {
		self::$code = 400;
		self::$msg = utf8_encode("Datos de entidad incorrectos, formulario erroneo");
		self::showMessage();
	}

	public static function customError($msg, $code) {
		self::$code = $code;
		self::$msg = Utils::stringToUTF8($msg);
		self::showMessage();
	}

	public static function missingCSRF() {
		self::$code = 400;
		self::$msg = utf8_encode("Missing CSRF form token");
		self::showMessage();
	}
	public static function invalidCSRF() {
		self::$code = 400;
		self::$msg = utf8_encode("Invalid CSRF token");
		self::showMessage();
	}
	public static function jwtError() {
		self::$code = 401;
		self::$msg = utf8_encode("Invalid jwt token");
		self::showMessage();
	}

	public static function jwtOutofDate() {
		self::$code = 401;
		self::$msg = utf8_encode("Outdated jwt token");
		self::showMessage();
	}

	public static function passwordMissmatch() {
		self::$code = 400;
		self::$msg = utf8_encode("Password missmatch");
		self::showMessage();
	}


	public static function show404() {
		self::$code = 404;
		self::$msg = utf8_encode("PAGE NOT FOUND");
		self::showMessage();
		//new PAGPublic404();
	}

	private static function showMessage() {
		LoggerController::get()->logDebug(self::$msg);
		$response = new Response();
		$response->withStatus(self::$code, self::$msg)
		->withAddedHeader("Content-type", "application/json")
		->setResponseBody(json_encode(array(
				"code" => self::$code,
				"msg" => self::$msg)
				))
				->sendResponse();
	}

}