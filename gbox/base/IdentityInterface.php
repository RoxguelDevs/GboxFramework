<?php
namespace Gbox\base;
use Gbox;
interface IdentityInterface
{
    public static function findIdentity ($id);
    public static function findIdentityByAccessToken ($token, $type = null);
    public static function keyId ();
    public function getId ();
    public function getAuthKey ();
    public function validateAuthKey ($authKey);
}