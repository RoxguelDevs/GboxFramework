<?php
namespace app\models;
use Gbox\base\Orm;
use app\models\Companies;
class Contacts extends Orm
{
	public $full_name;
	public $company_name;
	public function getDb ()
	{
		return \Gbox::getConfig()->db;
	}
	public function tableName ()
	{
		return 'contacts';
	}
	public function key ()
	{
		return 'id_contact';
	}
    public function getCompany()
    {
        return $this->hasMany(Companies::className(), ['id_company' => 'id_company']);
    }
}