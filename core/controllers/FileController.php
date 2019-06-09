<?php

namespace core\controllers;



use core\controllers\http\psr\Response;
use core\controllers\http\psr\Stream;

/**
 * UNDER DEVELOPMENT
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class FileController {


	/**
	 * Send file for download to client
	 * @param string $path for file
	 */
	public function sendFile($path) {
		$file = $path;
		if (is_file($file)) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			$fileResponse = new Response();
			$this->sendFileHeaders($fileResponse,$file, finfo_file($finfo, $file), basename($file));
			$chunkSize = 1024 * 1024;
			$fileStream = new Stream(fopen($file, 'rb'));
			while (!$fileStream->eof()) {
				$buffer = $fileStream->read($chunkSize);
				$fileResponse->getBody()->writeSized($buffer, $chunkSize);
				$fileResponse->getBody()->flush();
			}
			$fileStream->close();
		} else {
			ExceptionController::customError("File not found", 400);
		}



	}

	function sendFileHeaders(Response $fileResponse,$file, $type, $name=NULL) {
		if (empty($name))
		{
			$name = basename($file);
		}
		$fileResponse->putHeaderValue("Pragma", "public");
		$fileResponse->putHeaderValue("Expires", "0");
		$fileResponse->putHeaderValue("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$fileResponse->putHeaderValue("Cache-Control", "");
		$fileResponse->putHeaderValue("Content-Transfer-Encoding", "binary");
		$fileResponse->putHeaderValue("Content-Disposition", 'attachment; filename="'.$name.'";');
		$fileResponse->putHeaderValue("Content-Type", $type);
		$fileResponse->putHeaderValue("Content-Length", filesize($file));
		$fileResponse->sendHeaders();
	}


}