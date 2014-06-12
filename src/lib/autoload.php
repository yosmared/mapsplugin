<?php define('CLASS_DIR', __DIR__."/../");

set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);

function __autoload($className) {

	$className = str_replace("\\", '/', $className);

	// Use default autoload implementation
	require_once $className.".php";

}