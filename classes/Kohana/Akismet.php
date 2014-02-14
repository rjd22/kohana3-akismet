<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Akismet class. Based on http://akismet.com/development/api/
 *
 * @package 	Akismet
 * @author	 	Robert-Jan de Dreu<I_dont@want.mail>
 * @copyright  (c) 2014 Robert-Jan de Dreu
 * @license
 * The MIT License (MIT)
 */
class Kohana_Akismet {

	// Configuration settings
	protected $_config;

	/**
	 * Creates a new Akismet object.
	 *
	 * @param array $config
	 * @return Akismet
	 */
	public static function factory(array $config = array())
	{
		return new Akismet($config);
	}

	/**
	 * Creates a new Akismet object.
	 *
	 * @param array $config configuration information
	 * @throws Exception
	 */
	public function __construct(array $config = array())
	{
		// Load configuration from Akismet config file
		$this->_config = ( ! empty($config)) ? $config : Kohana::$config->load('akismet');

		// Verify key
		if ($this->verify_key($this->_config['key'], $this->_config['blog']) === FALSE)
		{
			throw new Exception("Your Akismet API key is not valid.");
		}
	}

	/**
	 * The key verification call should be made before beginning to use the
	 * service. It requires two variables, key and blog.
	 *
	 * @param string $key 	The API key being verified for use with the API
	 * @param string $blog 	Blog URL including http://
	 * @return bool
	 */
	public function verify_key($key, $blog)
	{
		$response = $this->response(http_build_query(array('key' => $key, 'blog' => $blog)), 'verify-key');
		return ($response == 'valid');
	}

	/**
	 * Check if Comment is Spam
	 *
	 * @param array $comment The comment to check
	 * @return bool
	 */
	public function is_spam(array $comment)
	{
		$response = $this->response(http_build_query($comment), 'comment-check');
		return ($response == 'true');
	}

	/**
	 * This call is for submitting comments that weren't marked as spam but
	 * should have been. It takes identical arguments as comment check.
	 *
	 * @param array $comment The comment to submit as spam
	 */
	public function submit_spam(array $comment)
	{
		$this->response(http_build_query($comment), 'submit-spam');
	}

	/**
	 * This call is intended for the marking of false positives, things that
	 * were incorrectly marked as spam. It takes identical arguments as comment
	 * check and submit spam.
	 *
	 * @param array $comment The comment to submit as ham
	 */
	public function submit_ham(array $comment)
	{
		$this->response(http_build_query($comment), 'submit-ham');
	}

	/**
	 * All calls to Akismet are POST requests much like a web form would send.
	 * The request variables should be constructed like a query string,
	 * key=value and multiple variables separated by ampersands. Don't forget to
	 * URL escape the values.
	 *
	 * @param $request
	 * @param $path
	 * @return mixed
	 * @throws Exception
	 */
	protected function response($request, $path)
	{
		// Establish connection
		if ( ! ($connection = @fsockopen($this->_config['server'], $this->_config['port'])))
		{
			throw new Exception("Could not connect to akismet server.");
		}

		if ($connection)
		{
			$http_request = "POST /1.1/$path HTTP/1.0\r\n"
				. "Host: ".(( ! empty($this->_config['key'])) ? $this->_config['key']."." : NULL).$this->_config['server']."\r\n"
				. "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n"
				. "Content-Length: ".strlen($request)."\r\n"
				. "User-Agent: ".$this->_config['user_agent']."\r\n"
				. "\r\n"
				. $request;

			$response = '';

			@fwrite($connection, $http_request);

			while( ! feof($connection))
			{
				$response .= @fgets($connection, 1160);
			}
			$response = explode("\r\n\r\n", $response, 2);

			// Close the connection
			@fclose($connection);

			return $response[1];
		}
		else
		{
			throw new Exception("The response could not be retrieved.");
		}
	}
}
