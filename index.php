<?php
config();

$x = 42/0;
throw new Exception("Error Processing Request", 1);


//___________________________________________________________________________________________
//                                                                          app configuration
/**
 * config
 * sets the default constant variables and sets up error/exception handling.
 */
function config() {
	define("VERSION",			"0.0");
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
    $exc_msg = DEBUG ? "Exception# $code<br/>$msg" : "Sorry, an exception has occured.";
    die("<h1>Error!</h1><p>$exc_msg</p>");
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
	$err_msg = DEBUG ? "Error# $num<br/>$str<br/>file: $file on line# $line<br>in context:<pre>".print_r($ctx, true)."</pre>" : "Sorry, an error has occured.";
    die("<h1>Error!</h1><p>$err_msg</p>");
}


?>