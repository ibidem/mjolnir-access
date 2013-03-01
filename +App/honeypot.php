<?php namespace app;

// This is an IDE honeypot. It tells IDEs the class hirarchy, but otherwise has
// no effect on your application. :)

// HowTo: order honeypot -n 'mjolnir\access'


class Access extends \mjolnir\access\Access
{
}

class AccessChannel_Facebook extends \mjolnir\access\AccessChannel_Facebook
{
	/** @return \app\AccessChannel_Facebook */
	static function instance() { return parent::instance(); }
}

class AccessChannel_Universal extends \mjolnir\access\AccessChannel_Universal
{
	/** @return \app\AccessChannel_Universal */
	static function instance() { return parent::instance(); }
}

class Allow extends \mjolnir\access\Allow
{
}

class Auth extends \mjolnir\access\Auth
{
	/** @return \app\Auth */
	static function instance() { return parent::instance(); }
}

class Backend_ProfileField extends \mjolnir\access\Backend_ProfileField
{
	/** @return \app\Backend_ProfileField */
	static function instance() { return parent::instance(); }
}

class Backend_Role extends \mjolnir\access\Backend_Role
{
	/** @return \app\Backend_Role */
	static function instance() { return parent::instance(); }
}

class Backend_Settings extends \mjolnir\access\Backend_Settings
{
	/** @return \app\Backend_Settings */
	static function instance() { return parent::instance(); }
}

class Backend_User extends \mjolnir\access\Backend_User
{
	/** @return \app\Backend_User */
	static function instance() { return parent::instance(); }
}

class Ban extends \mjolnir\access\Ban
{
}

class Context_Access extends \mjolnir\access\Context_Access
{
	/** @return \app\Context_Access */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Renderable action_index()
 * @method \app\ThemeView setup_view($errors, $webtitle, $target, $errortarget)
 * @method \app\Renderable signin_view($errors = null)
 * @method \app\Renderable signup_view($errors = null)
 * @method \app\Renderable pwdreset_view($errors = null)
 * @method \app\Renderable emails_view($errors = null)
 * @method \app\Controller_Access channel_is($channel)
 * @method \app\Channel channel()
 * @method \app\Controller_Access add_preprocessor($name, $processor)
 * @method \app\Controller_Access add_postprocessor($name, $processor)
 * @method \app\Controller_Access trait_preprocess()
 * @method \app\Controller_Access postprocess()
 * @method \app\Renderable action_signin()
 * @method \app\Renderable public_signin()
 * @method \app\Renderable public_signout()
 * @method \app\Renderable public_pwdreset()
 */
class Controller_Access extends \mjolnir\access\Controller_Access
{
	/** @return \app\Controller_Access */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Layer_Access channel_is($channel)
 * @method \app\Channel channel()
 */
class Layer_Access extends \mjolnir\access\Layer_Access
{
	/** @return \app\Layer_Access */
	static function instance() { return parent::instance(); }
}

class Model_ProfileField extends \mjolnir\access\Model_ProfileField
{
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

class Model_Role extends \mjolnir\access\Model_Role
{
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

class Model_SecondaryEmail extends \mjolnir\access\Model_SecondaryEmail
{
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

class Model_SecurityToken extends \mjolnir\access\Model_SecurityToken
{
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

class Model_User extends \mjolnir\access\Model_User
{
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

class Model_UserSigninToken extends \mjolnir\access\Model_UserSigninToken
{
	/** @return \app\Model_UserSigninToken */
	static function instance() { return parent::instance(); }
	/** @return \app\SQLStatement */
	static function statement($identifier, $sql, $lang = null) { return parent::statement($identifier, $sql, $lang); }
}

/**
 * @method \app\Protocol relays(array $relays)
 * @method \app\Protocol attributes(array $attributes)
 * @method \app\Protocol only_others()
 * @method \app\Protocol only_owner()
 * @method \app\Protocol everybody()
 * @method \app\Protocol param($name, array $values)
 * @method \app\Protocol all_parameters()
 */
class Protocol extends \mjolnir\access\Protocol
{
	/** @return \app\Protocol */
	static function instance() { return parent::instance(); }
}

class ReCaptcha extends \mjolnir\access\ReCaptcha
{
}

class Schematic_Mjolnir_Access_Base extends \mjolnir\access\Schematic_Mjolnir_Access_Base
{
	/** @return \app\Schematic_Mjolnir_Access_Base */
	static function instance() { return parent::instance(); }
}

class SecurityToken extends \mjolnir\access\SecurityToken
{
}

/**
 * @method \app\Task_Make_User set($name, $value)
 * @method \app\Task_Make_User add($name, $value)
 * @method \app\Task_Make_User metadata_is(array $metadata = null)
 * @method \app\Task_Make_User writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_Make_User extends \mjolnir\access\Task_Make_User
{
	/** @return \app\Task_Make_User */
	static function instance() { return parent::instance(); }
}

/**
 * @method \app\Task_User_Password set($name, $value)
 * @method \app\Task_User_Password add($name, $value)
 * @method \app\Task_User_Password metadata_is(array $metadata = null)
 * @method \app\Task_User_Password writer_is($writer)
 * @method \app\Writer writer()
 */
class Task_User_Password extends \mjolnir\access\Task_User_Password
{
	/** @return \app\Task_User_Password */
	static function instance() { return parent::instance(); }
}
trait Trait_Controller_MjolnirEmails { use \mjolnir\access\Trait_Controller_MjolnirEmails; }
trait Trait_Controller_MjolnirPwdReset { use \mjolnir\access\Trait_Controller_MjolnirPwdReset; }
trait Trait_Controller_MjolnirSignin { use \mjolnir\access\Trait_Controller_MjolnirSignin; }
trait Trait_Controller_MjolnirSignup { use \mjolnir\access\Trait_Controller_MjolnirSignup; }
trait Trait_Model_SecurityToken { use \mjolnir\access\Trait_Model_SecurityToken; }
