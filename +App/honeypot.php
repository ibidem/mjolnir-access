<?php namespace app;

// This is a IDE honeypot. :)

// HowTo: minion honeypot -n "ibidem\\access"

class A12n extends \ibidem\access\A12n { /** @return \ibidem\access\A12n */ static function instance() { return parent::instance(); } }
class Access extends \ibidem\access\Access {}
class Backend_Access extends \ibidem\access\Backend_Access { /** @return \ibidem\access\Backend_Access */ static function instance() { return parent::instance(); } }
class Backend_RoleManager extends \ibidem\access\Backend_RoleManager { /** @return \ibidem\access\Backend_RoleManager */ static function instance() { return parent::instance(); } }
class Backend_UserManager extends \ibidem\access\Backend_UserManager { /** @return \ibidem\access\Backend_UserManager */ static function instance() { return parent::instance(); } }
class Controller_A12n extends \ibidem\access\Controller_A12n { /** @return \ibidem\access\Controller_A12n */ static function instance() { return parent::instance(); } }
class Layer_Access extends \ibidem\access\Layer_Access { /** @return \ibidem\access\Layer_Access */ static function instance() { return parent::instance(); } }
class Migration_User extends \ibidem\access\Migration_User { /** @return \ibidem\access\Migration_User */ static function instance() { return parent::instance(); } }
class Model_DB_Role extends \ibidem\access\Model_DB_Role {}
class Model_DB_User extends \ibidem\access\Model_DB_User {}
class Model_User extends \ibidem\access\Model_User { /** @return \ibidem\access\Model_User */ static function instance($id = null) { return parent::instance($id); } }
class Protocol extends \ibidem\access\Protocol { /** @return \ibidem\access\Protocol */ static function instance() { return parent::instance(); } }
