<?php
define('TESTING', true);
define('CANCEL_START_APP', true);

if (!function_exists('tdump')) {
	function tdump()
	{
		echo call_user_func_array('Tracy\Dumper::toText', func_get_args());
	}
}
if (!function_exists('bardump')) {
	function bardump()
	{
		call_user_func_array('Tracy\Debugger::barDump', func_get_args());
	}
}


require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/jasir/testbase/lib/Loader.php';
require __DIR__ . '/BaseTestCase.php';
