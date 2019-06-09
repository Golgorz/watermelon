<?php
namespace core\controllers;


/**
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class FCMController {


	public static function sendPushTo($to, $title, $subtitle, $body, $extra) {

	    $registrationIds = is_array($to) ? $to : [$to];
	    // prep the bundle
	   
	    $fcmMsg = array(
	    		'body' => $body,
	    		'title' => $title,
	    		'sound' => "default",
	    		'vibrate'	=> 1,
	    		"largeIcon" => "large_icon",
	    		"smallIcon" => "small_icon"
	    );
	    
	    $fields = array
	    (
	        'registration_ids' 	=> $registrationIds,
	    	'notification' => $fcmMsg,
	    );
	    
	    if(count($extra) > 0) {
	    	$fields['data'] = $extra;
	    }

	    $headers = array
	    (
	        'Authorization: key=' . $_ENV['FCM_API_ACCESS_KEY'],
	        'Content-Type: application/json'
	    );

	    $ch = curl_init();
	    curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
	    curl_setopt( $ch,CURLOPT_POST, true );
	    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	    $result = curl_exec($ch );
	    curl_close( $ch );
        return $result;

	}


}