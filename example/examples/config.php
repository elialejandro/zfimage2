<?php

$path = realpath(__DIR__ . '/../../library' );

// Set new include path
set_include_path(implode(PATH_SEPARATOR, array(
		$path,
		get_include_path(),
)));

include_once "Zend/Loader/Autoloader.php";

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace("ZFImage_");

