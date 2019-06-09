<?php

namespace core\controllers\http\psr;

use Core\Helpers\Collection;
use Core\Controllers\Http\Psr\Interfaces\ResponseInterface;
use Core\Controllers\Http\Psr\Interfaces\StreamInterface;

/**
 * Headers
 *
 * This class represents a collection of HTTP headers
 * that is used in both the HTTP request and response objects.
 * It also enables header name case-insensitivity when
 * getting or setting a header value.
 *
 * Each HTTP header can have multiple values. This class
 * stores values into an array for each header name. When
 * you request a header value, you receive an array of values
 * for that header.
 */
class Response extends Message implements ResponseInterface {

	
	
	/**
	 * Status code
	 *
	 * @var int
	 */
	protected $status = 200;
	
	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $statusText = '';
	
	/**
	 * Cookies
	 * @var Cookies
	 */
	protected $cookies;
	
	/**
	 * Status codes and reason phrases
	 *
	 * @var array
	 */
	
	/**
	 * Response body content to send back;
	 * @var string $responseBody
	 */
	protected $responseBody;
	
	protected static $messages = [
			//Informational 1xx
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			//Successful 2xx
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			208 => 'Already Reported',
			226 => 'IM Used',
			//Redirection 3xx
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			//Client Error 4xx
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
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			421 => 'Misdirected Request',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			444 => 'Connection Closed Without Response',
			451 => 'Unavailable For Legal Reasons',
			499 => 'Client Closed Request',
			//Server Error 5xx
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			508 => 'Loop Detected',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
			599 => 'Network Connect Timeout Error',
	];
	
	
	/**
	 * Create new HTTP response.
	 *
	 * @param int                   $status  The response status code.
	 * @param HeadersInterface|null $headers The response headers.
	 * @param StreamInterface|null  $body    The response body.
	 */
	public function __construct($status = 200, Headers $headers = null, StreamInterface $body = null, Cookies $cookies = null)
	{
		$this->status = $status;
		$this->headers = $headers ? $headers : new Headers();
		$this->body = $body ? $body : new Stream(fopen('php://output', 'wb'));
		$this->cookies = $cookies;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\Http\Psr\Interfaces\ResponseInterface::getStatusCode()
	 */
	public function getStatusCode() {
		return $this->status;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\Http\Psr\Interfaces\ResponseInterface::withStatus()
	 */
	public function withStatus($code, $reasonPhrase = '') {
		
		if (!is_string($reasonPhrase) && !method_exists($reasonPhrase, '__toString')) {
			throw new \InvalidArgumentException('ReasonPhrase must be a string');
		}
		
		$clone = clone $this;
		$clone->status = $code;
		if ($reasonPhrase === '' && isset(static::$messages[$code])) {
			$reasonPhrase = static::$messages[$code];
		}
		
		if ($reasonPhrase === '') {
			throw new \InvalidArgumentException('ReasonPhrase must be supplied for this code');
		}
		
		$clone->reasonPhrase = $reasonPhrase;
		
		return $clone;

	}

	/**
	 * {@inheritDoc}
	 * @see \Core\Controllers\Http\Psr\Interfaces\ResponseInterface::getReasonPhrase()
	 */
	public function getReasonPhrase() {
		if ($this->statusText) {
			return $this->statusText;
		}
		if (isset(static::$messages[$this->status])) {
			return static::$messages[$this->status];
		}
		return '';

	}
	

	/**
	 * Write data to the response body stream.
	 *
	 * @param string $data
	 * @return $this
	 */
	public function writeToBody($data)
	{
		$this->getBody()->write($data);
	
		return $this;
	}
	
	/**
	 * Sends the response back to cliente and finish execution
	 *
	 * @param data for body stream, use ase fast "set send exit";
	 *
	 * @return void
	 */
	public function sendResponse($dataBody = null) {
		$this->sendHeaders();
		if($dataBody == null) {
			$this->writeToBody($this->responseBody);
		}
		$this->getBody()->close();
		exit();
		
	}
	
	public function sendHeaders() {
		if($this->cookies != null) {
			$cookieData = $this->cookies->toHeaders();
			$this->headers->set("Set-Cookie", $cookieData);
		}
		header('HTTP/' . $this->protocolVersion . ' ' . $this->status. ' ' . $this->getReasonPhrase());
		foreach ($this->getHeaders() as $key => $value) {
	
			foreach ($value as $k => $v) {
				if ($k === 0) {
					header($key . ': ' . $v);
				} else {
					header($key . ': ' . $v, false);
				}
			}
	
		}
	}
	
	public function getResponseBody() {
		return $this->responseBody;
	}
	public function setResponseBody($responseBody) {
		$this->responseBody = $responseBody;
		return $this;
	}
	
	public static function getResponseInstance() {
		return Request::getInstance()->getResponse();
	}
	
	
}
