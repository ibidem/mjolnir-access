<?php namespace app;

// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has
// no effect on your application. :)

// HowTo: order honeypot -n 'mjolnir\access'

class Access extends \mjolnir\access\Access {}
class AccessChannel_Facebook extends \mjolnir\access\AccessChannel_Facebook { /** @return \mjolnir\access\AccessChannel_Facebook */ static function instance() { return parent::instance(); } }
class AccessChannel_Universal extends \mjolnir\access\AccessChannel_Universal { /** @return \mjolnir\access\AccessChannel_Universal */ static function instance() { return parent::instance(); } }
class Allow extends \mjolnir\access\Allow {}
class Auth extends \mjolnir\access\Auth { /** @return \mjolnir\access\Auth */ static function instance() { return parent::instance(); } }
class Backend_ProfileField extends \mjolnir\access\Backend_ProfileField { /** @return \mjolnir\access\Backend_ProfileField */ static function instance() { return parent::instance(); } }
class Backend_Role extends \mjolnir\access\Backend_Role { /** @return \mjolnir\access\Backend_Role */ static function instance() { return parent::instance(); } }
class Backend_Settings extends \mjolnir\access\Backend_Settings { /** @return \mjolnir\access\Backend_Settings */ static function instance() { return parent::instance(); } }
class Backend_User extends \mjolnir\access\Backend_User { /** @return \mjolnir\access\Backend_User */ static function instance() { return parent::instance(); } }
class Ban extends \mjolnir\access\Ban {}
class Context_Access extends \mjolnir\access\Context_Access { /** @return \mjolnir\access\Context_Access */ static function instance() { return parent::instance(); } }
class Controller_A12n extends \mjolnir\access\Controller_A12n { /** @return \mjolnir\access\Controller_A12n */ static function instance() { return parent::instance(); } }
class Controller_Access extends \mjolnir\access\Controller_Access { /** @return \mjolnir\access\Controller_Access */ static function instance() { return parent::instance(); } }
class Layer_Access extends \mjolnir\access\Layer_Access { /** @return \mjolnir\access\Layer_Access */ static function instance() { return parent::instance(); } }
class Model_ProfileField extends \mjolnir\access\Model_ProfileField {}
class Model_Role extends \mjolnir\access\Model_Role {}
class Model_SecondaryEmail extends \mjolnir\access\Model_SecondaryEmail {}
class Model_SecurityToken extends \mjolnir\access\Model_SecurityToken {}
class Model_User extends \mjolnir\access\Model_User {}
class Model_UserSigninToken extends \mjolnir\access\Model_UserSigninToken { /** @return \mjolnir\access\Model_UserSigninToken */ static function instance() { return parent::instance(); } }
class Protocol extends \mjolnir\access\Protocol { /** @return \mjolnir\access\Protocol */ static function instance() { return parent::instance(); } }
class ReCaptcha extends \mjolnir\access\ReCaptcha {}
class Schematic_Mjolnir_Access_Base extends \mjolnir\access\Schematic_Mjolnir_Access_Base { /** @return \mjolnir\access\Schematic_Mjolnir_Access_Base */ static function instance() { return parent::instance(); } }
class Schematic_Mjolnir_Access_Oauth extends \mjolnir\access\Schematic_Mjolnir_Access_Oauth { /** @return \mjolnir\access\Schematic_Mjolnir_Access_Oauth */ static function instance() { return parent::instance(); } }
class SecurityToken extends \mjolnir\access\SecurityToken {}
trait Trait_Controller_MjolnirEmails { use \mjolnir\access\Trait_Controller_MjolnirEmails; }
trait Trait_Controller_MjolnirPwdReset { use \mjolnir\access\Trait_Controller_MjolnirPwdReset; }
trait Trait_Controller_MjolnirSignin { use \mjolnir\access\Trait_Controller_MjolnirSignin; }
trait Trait_Controller_MjolnirSignup { use \mjolnir\access\Trait_Controller_MjolnirSignup; }
trait Trait_Model_SecurityToken { use \mjolnir\access\Trait_Model_SecurityToken; }
