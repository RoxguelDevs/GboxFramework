<?php
namespace app\models;
use Gbox\base\Orm;
class Users extends Orm
{
	public $full_name;
	public function getDb ()
	{
		return \Gbox::getConfig()->db;
	}
	public function tableName ()
	{
		return 'users';
	}
	public function key ()
	{
		return 'id_user';
	}
	public function byMyBusiness ()
	{
		if (!\Gbox::$components->business->checkBusinessSelected())
		{
			throw new ForbiddenHttpException('No ha seleccionado una empresa');
		}
		return $this->where(['users', 'id_business'], \Gbox::$components->business->getData('id_business'));
	}
}