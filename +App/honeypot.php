<?php namespace app;

// This is a IDE honeypot. :)

// HowTo: minion honeypot -n "ibidem\\access"

class A12n extends \ibidem\access\A12n { /** @return \ibidem\access\A12n */ static function instance() { return parent::instance(); } }
class Access extends \ibidem\access\Access {}
class Controller_A12n extends \ibidem\access\Controller_A12n { /** @return \ibidem\access\Controller_A12n */ static function instance() { return parent::instance(); } }
class Layer_Access extends \ibidem\access\Layer_Access { /** @return \ibidem\access\Layer_Access */ static function instance() { return parent::instance(); } }
class Migration_User extends \ibidem\access\Migration_User { /** @return \ibidem\access\Migration_User */ static function instance() { return parent::instance(); } }
class Model_DB_User extends \ibidem\access\Model_DB_User { /** @return \ibidem\access\Model_DB_User */ static function instance() { return parent::instance(); } }
class Model_HTTP_User extends \ibidem\access\Model_HTTP_User { /** @return \ibidem\access\Model_HTTP_User */ static function instance($id = null) { return parent::instance($id); } }
class Model_User extends \ibidem\access\Model_User { /** @return \ibidem\access\Model_User */ static function instance() { return parent::instance(); } }
class Protocol extends \ibidem\access\Protocol { /** @return \ibidem\access\Protocol */ static function instance() { return parent::instance(); } }
