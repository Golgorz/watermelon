<?php
namespace core\controllers\http\psr;


use InvalidArgumentException;
use RuntimeException;
use Core\Controllers\Http\Psr\Interfaces\StreamInterface;

/**
 * Represents a data stream as defined in PSR-7.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/StreamInterface.php
 */
class Stream implements StreamInterface
{


    /**
     * The underlying stream resource
     *
     * @var resource
     */
    protected $stream;



    /**
     * The size of the stream if known
     *
     * @var null|int
     */
    protected $size;


    /**
     * Create a new Stream.
     *
     * @param  resource $stream A PHP resource handle.
     *
     * @throws InvalidArgumentException If argument is not a resource.
     */
    public function __construct($stream)
    {
        $this->attach($stream);
    }
	
	/**
	 * Reads all data from the stream into a string, from the beginning to end.
	 *
	 * This method MUST attempt to seek to the beginning of the stream before
	 * reading data and read the stream until the end is reached.
	 *
	 * Warning: This could attempt to load a large amount of data into memory.
	 *
	 * This method MUST NOT raise an exception in order to conform with PHP's
	 * string casting operations.
	 *
	 * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @return string
	 */
	public function __toString() {
		if(! $this->isAttached()) {
			return '';
		}
		
		try {
			$this->rewind();
			return $this->getContents();
		} catch(RuntimeException $e) {
			return '';
		}
	}
    /**
     * Is a resource attached to this stream?
     *
     * @return bool
     */
    protected function isAttached()
    {
        return is_resource($this->stream);
    }
	
	/**
	 * Attach new resource to this object.
	 *
	 * Note: This method is not part of the PSR-7 standard.
	 *
	 * @param resource $newStream
	 *        	A PHP resource handle.
	 *
	 * @throws InvalidArgumentException If argument is not a valid PHP resource.
	 */
	protected function attach($stream) {
		if (is_resource($stream) === false) {
			throw new InvalidArgumentException(__METHOD__ . ' argument must be a valid PHP resource');
		}
		
		if ($this->isAttached() === true) {
			$this->detach();
		}
		
		$this->stream = $stream;
	}



    /**
     * Closes the stream and any underlying resources.
     */
    public function close()
    {
        if($this->isAttached()) {
        	fclose($this->stream);
        }
        $this->detach();
    }
    
    
    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
    	$oldStream = $this->stream;
    	$this->stream = null;
    	$this->size = null;
    
    	return $oldStream;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->isAttached() && $this->size == null) {
            $info = fstat($this->stream);
            $this->size = isset($info['size']) ? $info['size'] : null;
        }

        return $this->size;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     *
     * @throws RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->isAttached() || ($position = ftell($this->stream)) === false) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }

        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return  feof($this->stream);
    }

   


    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
    	$seekable = false;
		if($this->isAttached()) {
			$seekable = $this->getMetadata('seekable');
        }
        return $seekable;
    }
	
	/**
	 * Seek to a position in the stream.
	 *
	 * @link http://www.php.net/manual/en/function.fseek.php
	 *
	 * @param int $offset
	 *        	Stream offset
	 * @param int $whence
	 *        	Specifies how the cursor position will be calculated
	 *        	based on the seek offset. Valid values are identical to the built-in
	 *        	PHP $whence values for `fseek()`. SEEK_SET: Set position equal to
	 *        	offset bytes SEEK_CUR: Set position to current location plus offset
	 *        	SEEK_END: Set position to end-of-stream plus offset.
	 *
	 * @throws RuntimeException on failure.
	 */
	public function seek($offset, $whence = SEEK_SET) {
		if(!$this->isSeekable() || fseek($this->stream, $offset, $whence) === - 1) {
			throw new RuntimeException('Could not seek in stream');
		}
	}
	
	/**
	 * Seek to the beginning of the stream.
	 *
	 * If the stream is not seekable, this method will raise an exception;
	 * otherwise, it will perform a seek(0).
	 *
	 * @see seek()
	 *
	 * @link http://www.php.net/manual/en/function.fseek.php
	 *
	 */
	public function rewind() {
		if(!$this->isSeekable() || rewind($this->stream) === false) {
			throw new RuntimeException('Could not rewind stream');
		}
	}
	
	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @return bool
	 */
	public function isWritable() {
		$mode = $this->getMetaData('mode');
		return $mode !== 'r';
	}
	
	/**
	 * Write data to the stream.
	 *
	 * @param string $string
	 *        	The string that is to be written.
	 *
	 * @return int Returns the number of bytes written to the stream.
	 *
	 */
	public function write($string) {
		if(!$this->isWritable() || ($data = fwrite($this->stream, $string)) === false) {
			throw new RuntimeException('Could not write to stream');
		}
		
		return $data;
	}
	
	/**
	 * Write data to the stream.
	 *
	 * @param string $string
	 *        	The string that is to be written.
	 * @param int $size
	 * 			The lenght to write.
	 *
	 * @return int Returns the number of bytes written to the stream.
	 *
	 */
	public function writeSized($string, $size) {
		if(!$this->isWritable() || ($data = fwrite($this->stream, $string, $size)) === false) {
			throw new RuntimeException('Could not write to stream');
		}
	
		return $data;
	}
	
	public function flush() {
		fflush($this->stream);
	}
	
	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @return bool
	 */
	public function isReadable() {
		if ($this->isAttached()) {
			$mode = $this->getMetaData('mode');
			return strpos($mode,'r') !== false || strpos($mode,'+') !== false;
		}
		return false;
	}
	
	/**
	 * Read data from the stream.
	 *
	 * @param int $length
	 *        	Read up to $length bytes from the object and return
	 *        	them. Fewer than $length bytes may be returned if underlying stream
	 *        	call returns fewer bytes.
	 *
	 * @return string Returns the data read from the stream, or an empty string
	 *         if no bytes are available.
	 *
	 * @throws RuntimeException if an error occurs.
	 */
	public function read($length) {
		if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
			throw new RuntimeException('Could not read from stream');
		}
		
		return $data;
	}
	
	/**
	 * Returns the remaining contents in a string
	 *
	 * @return string
	 *
	 * @throws RuntimeException if unable to read or an error occurs while
	 *         reading.
	 */
	public function getContents() {
		if (!$this->isReadable() || ($contents = stream_get_contents($this->stream)) === false) {
			throw new RuntimeException('Could not get contents of stream');
		}
		
		return $contents;
	}
	/**
	 * Get stream metadata as an associative array or retrieve a specific key.
	 *
	 * The keys returned are identical to the keys returned from PHP's
	 * stream_get_meta_data() function.
	 *
	 * @link http://php.net/manual/en/function.stream-get-meta-data.php
	 *
	 * @param string $key
	 *        	Specific metadata to retrieve.
	 *
	 * @return array|mixed|null Returns an associative array if no key is
	 *         provided. Returns a specific key value if a key is provided and the
	 *         value is found, or null if the key is not found.
	 */
	public function getMetadata($key = null) {
		$meta = stream_get_meta_data($this->stream);
		if(is_null($key)) {
			return $meta;
		}
		return isset($meta[$key]) ? $meta[$key] : null;
	}
}
