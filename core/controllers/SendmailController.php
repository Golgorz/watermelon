<?php
namespace core\controllers;

require './Core/Vendors/PHPMailer/PHPMailerAutoload.php';

/**
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class SendmailController {


	public static function sendBasicEmail($to, $subject, $body, $from, $isHtml = false, $replayTo = "", $sendCopyTo = "") {


	    if(is_array($to)) $to = implode(", ", $to);
	    $headers = "";
        if($isHtml) {
            $headers  .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        }

	    // Cabeceras adicionales
        $headers .= "From: $from" . "\r\n";

        if($sendCopyTo != "")
            $headers .= "Bcc: $sendCopyTo" . "\r\n";
        if($replayTo != "")
            $headers .= "Reply-To: $replayTo" . "\r\n";

        $headers .= 'X-Mailer: PHP/' . phpversion();

	    mail($to, $subject, $body, $headers);


	}


}