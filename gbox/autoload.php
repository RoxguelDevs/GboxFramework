<?php
namespace Gbox;
class Autoload
{
	public function __construct ()
	{
		spl_autoload_register(function ($name) {
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			if (strpos($name, 'Gbox' . DIRECTORY_SEPARATOR) == 0) {
				$name = str_replace('Gbox' . DIRECTORY_SEPARATOR, 'base' . DIRECTORY_SEPARATOR, $name);
			}
			if (strpos($name, 'app' . DIRECTORY_SEPARATOR) == 0) {
				$name = str_replace('app' . DIRECTORY_SEPARATOR, '', $name);
			}
			$name = str_replace('base' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR, 'base' . DIRECTORY_SEPARATOR, $name);
			require ($name . '.php');
		});
	}
}
?>