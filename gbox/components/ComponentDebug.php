<?php
namespace app\components;
use Gbox\helpers\Url;
use Gbox\helpers\Json;
use Gbox\base\Request;
use Gbox\base\Session;
use Gbox\base\Component;
class ComponentDebug extends Component
{
	private $types = [
		'router',
		'sql',
	];
	private $path;
	private $file;
	public function init ()
	{
		$this->path = Url::to($this->params['path']);
		if (!file_exists($this->path)) {
			mkdir($this->path, 0777, true);
		}
		$this->file = tempnam($this->path, 'debug');
	}
	public function getPath ()
	{
		return $this->path;
	}
	public function getFile ($file = null)
	{
		if ($file)
		{
			return $this->getPath() . $file;
		}
		return $this->file;
	}
	public function getFileName ()
	{
		return $this->file;
	}
	public function getData ($file = null)
	{
		if ($file)
		{
			return Json::decode(file_get_contents($this->getFile($file)));
		}
		else
		{
			return Json::decode(file_get_contents($this->file));
		}
	}
	public function saveReportRouter ($data = [])
	{
		$data['time'] = time();
		$data['session'] = session_id();
		$data['type'] = 'router';
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->saveReportInFile($data);
	}
	public function saveReportOrm ($data = [])
	{
		$data['time'] = time();
		$data['session'] = session_id();
		$data['type'] = 'orm';
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->saveReportInFile($data);
	}
	public function saveReportModel ($data = [])
	{
		$data['time'] = time();
		$data['session'] = session_id();
		$data['type'] = 'model';
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->saveReportInFile($data);
	}
	public function saveReportRequest ($data = [])
	{
		$data['time'] = time();
		$data['session'] = session_id();
		$data['type'] = 'request';
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->saveReportInFile($data);
	}
	private function saveReportInFile ($data)
	{
		if (\Gbox::getRequest()->getModule() == 'debug')
		{
			return;
		}
		$file = file_get_contents($this->file);
		$datafile = json_decode($file);
		unset($file);
		$datafile[] = $data;
		file_put_contents($this->file, json_encode($datafile));
		unset($datafile);
	}
}