<?php
/**
 * paste
 * a simple web application for storing text online. all data stored on the sever is encrypted. 
 * and when creating a paste, the user is provided with the only decryption key via sharing url. 
 * if the url is lost so is the decryption key. keys are not stored on the server. 
 * this keeps liability down for the server admin, and security up for the users. 
 * urls are not listed or indexed, so search engines (and hackers) cannot spider them.
 *
 * public git repos:
 *      http://code.xero.nu/paste.git
 *      http://github.com/fontvirus/paste.git
 *
 * @author xero harrison <x@xero.nu>
 * @copyright (cc) creative commons - attribution-shareAlike 3.0 unported
 * @version 1.0
 */
bootstrap();
//___________________________________________________________________________________________
//                                                                          app configuration
/**
 * config
 * sets the default constant variables and sets up error/exception handling.
 */
function config() {
	define("VERSION",			"1.0");
	define("FORCE_DIRTY",		true);
	define("DEBUG",				true);
	define("SLASH",				DIRECTORY_SEPARATOR);
	define("ROOT",				dirname(__FILE__));
	define("BLOBS",				ROOT.SLASH."blobs".SLASH);
	define("VIEWS",				ROOT.SLASH."views".SLASH);

	set_error_handler('error_handler');
    set_exception_handler('exception_handler');
}
/**
 * exception handler
 * render exception page.
 *
 * @param object $exc the php exception object
 */
function exception_handler($exc) {
    $code = $exc->getCode();
    $msg = $exc->getMessage();
    render("error", array("msg" => DEBUG ? "Exception# $code<br/>$msg" : "Sorry, an exception has occured."));
    die();
}
/**
 * error handler
 * render errors page.
 *
 * @param int $num the error code
 * @param string $str the error message
 * @param string $file the file throwing the error
 * @param int $line the line number in the file throwing the error
 * @param array $ctx the context of the error
 */
function error_handler($num, $str, $file, $line, $ctx) {
    render("error", array("msg" => DEBUG ? "Error# $num<br/>$str<br/>File: $file <br/>Line: $line" : "Sorry, an error has occured."));
    die();
}
//___________________________________________________________________________________________
//                                                                                   app init
/**
 * bootstrap
 * initilizes the application
 */
function bootstrap() {
	config();
	testBlobs();
	$url = URLdecoder();
	URLlogic($url);
}
/**
 * test blobs
 * tests to see if the blobs directory exists and is writable
 */
function testBlobs() {
	if(!is_dir(BLOBS)) {
		throw new Exception("blobs directory not found!", 500);
	}
	if(!is_writable(BLOBS)) {
		throw new Exception("blobs directory is not writable!", 500);
	}
}
//___________________________________________________________________________________________
//                                                                                 url mining
/**
 * URL decoder
 * defines url related variables.
 * BASE_URL  : the server root url
 * RAW_URL   : the actual url entered
 * CLEAN_URL : the raw url minus the querystring
 * DIRTY     : a boolean of whether the querystring exists
 * 
 * @return array $url clean url values
 */
function URLdecoder() {
	define("BASE_URL",		strtolower("http://".dirname($_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"])."/"));
	define("RAW_URL",		strtolower(cleanExtraction("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], false)));
	define("CLEAN_URL",		strtolower(cleanExtraction("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"], true)));
	define("DIRTY",			checkCleanliness(RAW_URL));
	define("DOMAIN",		DIRTY ? BASE_URL."?/" : BASE_URL);

	$url = explode(BASE_URL, CLEAN_URL);
	if(isset($url[1])) {
		$url = @explode("/", $url[1]);
	} else {
		$url[0] = "";
	}
	return $url;
}
/**
 * the clean extraction function takes a raw url and
 * removes both leading and trailing slashes, the
 * root file name (index.php) and the query string
 * question mark and trailing slash.
 *
 * @param string $url
 * @param boolean $clean 
 * @return string
 */
function cleanExtraction($url, $clean) {
	if($clean) $url = str_replace("?/", "", $url);
	if ('/' == substr($url, 0, 1)) $url = substr_replace($url, '', 0, 1); 
	if ('/' == substr($url, strlen($url)-1)) $url = substr_replace($url, '', strlen($url)-1); 
	return str_replace("index.php", "", $url);
}
/**
 * the check cleanliness function returns a boolean
 * depending on the existance of the query string.
 *
 * @param string $url
 * @return booleans
 */
function checkCleanliness($url) {
	if(FORCE_DIRTY) {
		return true;
	} else {
		return substr_count($url, "?")>0 ? true : false;
	}
}
function URLlogic($url) {
	if(!is_array($url)) {
		throw new Exception("failed to decode url array", 500);
	}
	if(!isset($url[0])) {
		//---welcome screen
		render('template', array('body' => render('main', array('url' => DOMAIN.'new/'), true)));
	} else {
		if($url[0] === '') {
			//---welcome screen
			render('template', array('body' => render('main', array('url' => DOMAIN.'new/'), true)));
		} else if($url[0] == 'new') {
			//---create paste form
			$form = render('form', array('url' => DOMAIN.'save/'), true);
			render('template', array('body' => $form));			
		} else if($url[0] == 'save') {
			//---save paste
			$ttl = getRequest('ttl', FILTER_SANITIZE_NUMBER_INT);
			$data = getRequest('data', FILTER_SANITIZE_SPECIAL_CHARS);
			/**
			 * @todo encrypt the data
			 */
			switch (intval($ttl)) {
				case 1:
					$ttl = time()+365*24*60*60;
				break;
				case 2:
					$ttl = time()+30*24*60*60;
				break;
				case 3:
					$ttl = time()+24*60*60;
				break;
				case 4:
					$ttl = time()+60*60;
				break;
				case 5:
					$ttl = '0';
				break;
			}
			$key = sha1(generateKey());
			$name = writeFile($ttl, encrypt($key, $data));
			$pasteURL = DOMAIN.$name.'/'.$key;
			$form = render('paste', array('url' => DOMAIN.'new/', 'paste' => '<a href="'.$pasteURL.'">'.$pasteURL.'</a>'), true);
			render('template', array('body' => $form));
		} else {
			//---check file
			if(!isset($url[1])) {
				throw new Exception("Invalid URL", 404);
			} else {
				if(!file_exists(BLOBS.$url[0])) {
					throw new Exception("Invalid URL", 404);
				} else {
					$contents = readTheFile($url[0]);
					if($contents['ttl'] == intval(0)) {
						//---decrypt and destroy
						$cleartext = decrypt($url[1], $contents['data']);
						$form = render('paste', array('url' => DOMAIN.'new/', 'paste' => $cleartext), true);
						render('template', array('body' => $form));						
						unlink(BLOBS.$url[0]);
					} else if($contents['ttl'] < time()) {
						//---too old, destroy
						unlink(BLOBS.$url[0]);
						throw new Exception("Invalid URL", 404);
					} else {
						//---decrypt and die
						$cleartext = decrypt($url[1], $contents['data']);
						$form = render('paste', array('url' => DOMAIN.'new/', 'paste' => $cleartext), true);
						render('template', array('body' => $form));						
						die();
					}
				}
			}
		}
	}
}
//___________________________________________________________________________________________
//                                                                              save routines
/**
 * get request
 * function for securely dealing with post data by calling filter_var and htmlentities on all user requests.
 *
 * @param string $name the post variable name
 * @param string $filter the filter function to call
 */
function getRequest($name = '', $filter = FILTER_SANITIZE_SPECIAL_CHARS) {
	if(isset($_POST[$name])) {
		$var = filter_var($_POST[$name], $filter);
		return htmlentities($var);
	}
}
/**
 * write file
 * creates a json encoded blob of a given paste on the server.
 *
 * @param int $ttl time to live
 * @param string $data the file data to write
 * @return string the filename
 */
function writeFile($ttl, $data) {
	$contents = array('ttl' => $ttl, 'data' => $data);
	$json = json_encode($contents);
	$filename = sha1($json);
	while(file_exists(BLOBS.$filename)) {
		$contents['random'] = rand();
		$json = json_encode($contents);
		$filename = sha1($json);		
	}
	file_put_contents(BLOBS.$filename, $json);
	return $filename;
}
/**
 * read file
 * reads and json decodes the data from a paste file on the server to an array
 *
 * @param string $name the filename
 * @return array the file contents
 */
function readTheFile($name) {
	$contents = file_get_contents(BLOBS.$name);
	return json_decode($contents, true);
}
//___________________________________________________________________________________________
//                                                                               cryptography
/**
 * generate key
 * creates a randomized, random length key
 *
 * @return string randomly generated key
 */
function generateKey() {
	$charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$keylen = mt_rand(64, 128);
	$key = '';
	$i = 0;
	while (++$i < $keylen) {
		mt_srand((double) microtime()*rand());
		$key .=  $charset[mt_rand(0, strlen($charset)-1)];
	}
	return $key;
}
/**
 * encrypt function
 * securly encrypts a string
 *
 * @param string $key the encryption key
 * @param string $cleartext the string to be encrypted
 * @return string encrypted string
 */
function encrypt($key, $cleartext) {
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cleartext, MCRYPT_MODE_CBC, md5($key)));
}
/**
 * decrypt function
 * securly decrypts a string
 *
 * @param string $key the encryption key
 * @param string $encrypted the encrypted string
 * @return string clear text
 */
function decrypt($key, $encrypted) {
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5($key)), "\0");
}
//___________________________________________________________________________________________
//                                                                                  rendering
/**
 * render
 * simple html template function.
 *
 * @param string $view the filename of the template to load (without extension)
 * @param array $data the data to be inserted into the view
 * @param boolean $return if true the html will be returned, otherwise rendered to the screen
 */
function render($view, $data, $return = false) {
	if(sizeof($data) > 0)
        extract($data, EXTR_SKIP);

	$file = VIEWS.$view.".php";
	if(file_exists($file)) {
		if($return) {
	        ob_start();
	        include($file);
	        $content = ob_get_contents();
	        ob_end_clean();
	        return $content;
		} else {
    		include($file);
    	}
    } else {
    	throw new Exception("unable to load template: $file", 500);
    }
}

?>