<?php
bootstrap();


$form = render('form', array('url' => '?/save/'), true);
//$viewit = render('paste', array('paste' => 'here is some sample data.'), true);
render('template', array('body' => $form));


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