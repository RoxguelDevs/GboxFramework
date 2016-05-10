<?php
namespace app\debug\controllers;
use Gbox\base\Controller;
use Gbox\helpers\Json;
use Gbox\base\Session;
class SiteController extends Controller
{
	public function actionIndex ()
	{
		$files = [];
		$dir = \Gbox::$components->debug->getPath();
		if ($handle = opendir($dir))
		{
		    while (false !== ($entry = readdir($handle)))
		    {
		        if ($entry != '.' && $entry != '..' && strpos($entry, 'debug') === 0)
		        {
		        	$data = Json::decode(file_get_contents(\Gbox::$components->debug->getFile($entry)));
		        	if ($data[0]['session'] == session_id())
		        	{
						// $files[] = $entry;
						$files[filemtime($dir . $entry)] = $entry;
		        	}
		        }
		    }
		    closedir($handle);
		    krsort($files);
		}
	    Session::set('files-debug', $files);
	    if (!$current = \Gbox::getRequest()->get('id'))
	    {
		    reset($files);
		    $current = current($files);
		    Session::set('files-debug-current', $current);
	    }
	    Session::set('files-debug-current', $current);
		return $this->render('index', ['files' => $files]);
	}
}