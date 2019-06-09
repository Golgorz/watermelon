<?php

namespace core\controllers;


use Core\Helpers\Utils;
use Core\Controllers\Http\Psr\Response;
use Core\Controllers\Http\Psr\Stream;
use Core\Controllers\Http\Psr\Interfaces\UploadedFileInterface;
use Core\Controllers\Http\Psr\UploadedFile;

/**
 * UNDER DEVELOPMENT
 * @author Diego Lopez Rivera <forgin50@gmail.com>
 * @version 0.0.1
 */
class FileController {

	public static $PUBLIC_FILES_PATH = ROOT_PATH . '/Public/files';
	public static $PRIVATE_FILES_PATH = ROOT_PATH . '/App/PrivateFiles';

	public static function putFilesPublic(UploadedFile $file, $customPath = "") {
		$targetPath = self::$PUBLIC_FILES_PATH . DS . $customPath . DS;
		if(!file_exists($targetPath)) mkdir($targetPath,0775,true);

		print_r($file); die(); // TODO: Diego pre
		$file->moveTo($targetPath . DS . $file->getName());


	}
	public static function putFilesPrivate(UploadedFile $file, $customPath = "") {
		$targetPath = self::$PRIVATE_FILES_PATH . DS . $customPath . DS;
		if(!file_exists($targetPath)) mkdir($targetPath,0775,true);

		print_r($file); die(); // TODO: Diego pre
		$file->moveTo($targetPath . DS . $file->getName());
	}


	public static function exact_time() {
		$t = explode(' ',microtime());
		return ($t[0] + $t[1]);
	}


	public function deletePublicFile(string $ruta) {

		Utils::addLeadingSlash($ruta);

		if(file_exists(ROOT_PATH . '/Public/files' . $ruta)) {
			return unlink(ROOT_PATH . '/Public/files' . $ruta);
		}
		return false;
	}

	public function deletePrivateFile($ruta) {

		Utils::addLeadingSlash($ruta);

		if(file_exists( ROOT_PATH . '/App/Files' . $ruta)) {
			return unlink( ROOT_PATH . '/App/Files' . $ruta);
		}
		return false;
	}

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