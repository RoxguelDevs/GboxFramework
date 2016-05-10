<?php
namespace Gbox;
class Autoload
{
	private $files = [];
	private $exceptionFiles = [
		'Exception.php',
		'GboxException.php',
		'OrmException.php',
		'HttpException.php',
		'NotFoundHttpException.php',
		'ForbiddenHttpException.php',
		'MethodNotAllowedHttpException.php',
	];

	public function __construct ()
	{
	}

	public function loadBase ()
	{
		$this->addExceptionFiles();
		$this->addFile('gbox.php');
		$this->addFilesByDir(__DIR__ . '/base/');
		$this->addFilesByDir(__DIR__ . '/assets/');
		$this->addFilesByDir(__DIR__ . '/models/');
		$this->addFilesByDir(__DIR__ . '/widgets/');
		$this->addFilesByDir(__DIR__ . '/components/');
		$this->addFilesByDir(__DIR__ . '/base/helpers/');
		$this->addFilesByDir(__DIR__ . '/base/formatters/');
		$this->addFilesByDir(__DIR__ . '/base/collections/');
	}

	private function addExceptionFiles ()
	{
		foreach ($this->exceptionFiles as $file)
		{
			$this->loadFile(__DIR__ . '/base/exceptions/' . $file);
		}
	}

	public function addFile ($path)
	{
		array_push($this->files, $path);
	}

	public function addFilePHP ($path)
	{
		$this->addFile($path . '.php');
	}

	public function addFilesByDir ($dir)
	{
		if ($handle = opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
			{
				$extension = pathinfo($dir . $file, PATHINFO_EXTENSION);
				if ($file != '.' && $file != '..' && in_array($extension, ['php']))
				{
					$this->loadFile($dir . $file);
				}
			}
			closedir($handle);
		}
	}

	public function requireAll ()
	{
		foreach ($this->files as $file)
		{
			$this->loadFile($file);
		}
	}

	public function loadFile ($file)
	{
		require ($file);
	}

	public function loadFilePHP ($file)
	{
		$this->loadFile ($file . '.php');
	}
}
?>