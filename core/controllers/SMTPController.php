<?php
namespace core\controllers;

use PHPMailer\PHPMailer\PHPMailer;

require './Core/Vendors/PHPMailer/PHPMailerAutoload.php';

/**
 * UNDER DEVELOPMENT
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class SMTPController {


	public static function sendTemplatedMail($dataArray) {
		$mail = new PHPMailer;
		$mail->isSMTP();

		$mail->Host = $_ENV['SMTP_HOST'];
		$mail->Username = $_ENV['SMTP_USER'];
		$mail->Password = $_ENV['SMTP_PASS'];

		$mail->SMTPAuth = true;
		$mail->SMTPOptions = array(
				'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				)
		);
		$mail->SMTPSecure = 'tsl';
		$mail->Port = 25;
		$mail->From = $_ENV['SMTP_FROM'];
		$mail->FromName = utf8_decode($_ENV['SMTP_FROM_NAME']);

		foreach ($dataArray["emails"] as $email) {
			$mail->addAddress($email);
		}
		$mail->isHTML(true);
		if(isset($dataArray["files"]) && count($dataArray["files"]) > 0) {
			foreach ($dataArray["files"] as $file) {
				$mail->AddAttachment($file["ruta"], $file["nombre"]);
			}
		}
		if(isset($dataArray["subject"]))
			$mail->Subject = utf8_decode($dataArray["subject"]);
		if(isset($dataArray["body"]))
			$mail->Body = utf8_decode($dataArray["body"]);
		if(isset($dataArray["textBody"]))
			$mail->AltBody = utf8_decode($dataArray["textBody"]);
		
		return $mail->send();



	}

	public static function sendMail($dataArray) {

		//Crear una instancia de PHPMailer
		$mail = new PHPMailer();
		//Definir que vamos a usar SMTP
		$mail->IsSMTP();
		//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
		// 0 = off (producción)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug  = 0;
		//Ahora definimos gmail como servidor que aloja nuestro SMTP
		$mail->Host       = $_ENV['SMTP_HOST'];
		//El puerto será el 587 ya que usamos encriptación TLS
		$mail->Port       = $_ENV['SMTP_PORT'];
		//Definmos la seguridad como TLS
		$mail->SMTPSecure = 'tls';
		//Tenemos que usar gmail autenticados, así que esto a TRUE
		$mail->SMTPAuth   = true;
		//Definimos la cuenta que vamos a usar. Dirección completa de la misma
		$mail->Username   = $_ENV['SMTP_USER'];
		//Introducimos nuestra contraseña de gmail
		$mail->Password   = $_ENV['SMTP_PASS'];
		//Definimos el remitente (dirección y, opcionalmente, nombre)
		$mail->SetFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
		//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
		
		if(isset($dataArray['cc'])) {
			$ccTitle = "copy of email";
			if(isset($dataArray['ccTitle']))$ccTitle = $dataArray['ccTitle'];
			$mail->AddReplyTo($dataArray['cc'],$ccTitle);
			
		}

		$mail->SMTPOptions = array(
				'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
				)
		);

		foreach ($dataArray["emails"] as $email) {
			$mail->addAddress($email);
		}
		$mail->isHTML(true);
		if(isset($dataArray["files"]) && count($dataArray["files"]) > 0) {
			foreach ($dataArray["files"] as $file) {
				$mail->AddAttachment($file["ruta"], $file["nombre"]);
			}
		}
		if(isset($dataArray["subject"]))
			$mail->Subject = utf8_decode($dataArray["subject"]);

			if(isset($dataArray["body"]))
				$mail->Body = utf8_decode($dataArray["body"]);

				if(isset($dataArray["textBody"]))
					$mail->AltBody = utf8_decode($dataArray["textBody"]);

					//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
					//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));

					return $mail->send();





	}
}