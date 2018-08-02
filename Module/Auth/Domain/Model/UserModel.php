<?php
namespace Module\Auth\Domain\Model;

use Zodream\Domain\Access\Auth;
use Zodream\Database\Model\UserModel as BaseModel;
use Zodream\Helpers\Str;
use Zodream\Infrastructure\Cookie;
use Zodream\Infrastructure\Security\Hash;
use Zodream\Infrastructure\Http\Request;
use Zodream\Service\Factory;

/**
 * Class UserModel
 * @package Domain\Model\Auth
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $sex
 * @property string $avatar
 * @property string $token
 * @property integer $deleted_at
 * @property integer $created_at
 */
class UserModel extends BaseModel {

    const SEX_MALE = 1; // 性别男
    const SEX_FEMALE = 2; //性别女

    public $sex_list = [
        '未知',
        '男',
        '女'
    ];

	public static function tableName() {
        return 'user';
    }

    protected $primaryKey = array (
	  	'id',
		'name',
	  	'email',
	);
	
	public $rememberMe = false;
	
	public $code = false;
	
	public $agree = false;
	
	public $rePassword = false;
	
	public $roles = [];
	
	public $oldPassword = false;

	public function init() {
	    $this->on(static::AFTER_LOGIN, function() {
	        LoginLogModel::addLoginLog($this->name, true);
        });
    }

    protected function rules() {
		return array (
			'name' => 'required|string:0,30',
			'email' => 'string:0,100',
			'password' => 'string:0,64',
			'sex' => 'int',
			'avatar' => 'string:0,200',
			'token' => 'string:0,60',
			'deleted_at' => 'int',
			'created_at' => 'int',
		);
	}
	
	public function signInRules() {
		return [
			'email' => 'required|email',
			'password' => 'required|string:0,30',
			'code' => 'validateCode'
		];
	}
	
	public function signUpRules() {
		return [
			'name' => 'required|string:0,20',
			'email' => 'required|email',
			'password' => 'required|string:0,30',
			'rePassword' => 'validateRePassword',
			'agree' => ['validateAgree', 'message' => '必须同意相关协议！']
		];
	}

	public function resetRules() {
		return [
			'oldPassword'     => 'required|string:0,30',
			'password' => 'required|string:0,30',
			'rePassword' => 'validateRePassword',
		];
	}

	protected function labels() {
		return array (
		  'id' => 'Id',
		  'name' => 'Name',
		  'email' => 'Email',
		  'password' => 'Password',
		  'sex' => 'Sex',
		  'avatar' => 'Avatar',
		  'token' => 'Token',
		  'login_num' => 'Login Num',
		  'deleted_at' => 'Previous At',
		  'created_at' => 'Create At',
		);
	}

	public function getSexLabelAttribute() {
	    return $this->sex_list[$this->sex];
    }

	public function setPassword($password) {
		$this->password = Hash::make($password);
	}

	public function validatePassword($password) {
		return Hash::verify($password, $this->password);
	}

	public function validateAgree() {
	    return !empty($this->agree);
    }

    /**
     * @param $id
     * @return UserModel|boolean
     * @throws \Exception
     */
	public static function findIdentity($id) {
		return static::find($id);
	}

    /**
     *
     * @param $username
     * @param $password
     * @return bool|UserModel|void|\Zodream\Infrastructure\Interfaces\UserObject
     * @throws \Exception
     */
	public static function findByAccount($username, $password) {
        $user = self::findByEmail($username);
        if (empty($user)) {
            return false;
        }
        if (!$user->validatePassword($password)) {
            return false;
        }
        return $user;
    }

    /**
     * @param $name
     * @return UserModel|boolean
     * @throws \Exception
     */
	public static function findByName($name) {
		return static::find(['name' => $name]);
	}

    /**
     * @param $email
     * @return UserModel|boolean
     * @throws \Exception
     */
	public static function findByEmail($email) {
		return static::find(['email' => $email]);
	}

	public function validateCode() {
		if ($this->code === false) {
			return true;
		}
		$code = Factory::session()->get('code');
		if (empty($code) || $this->code != $code) {
			$this->setError('code', '验证码错误！');
			return false;
		}
		return true;
	}
	
	public function validateRePassword() {
		if ($this->rePassword === false) {
			return true;
		}
		if (empty($this->rePassword) || $this->rePassword != $this->password) {
			$this->setError('rePassword', '两次密码不一致！');
			return false;
		}
		return true;
	}
	
	public function signIn() {
		if (!$this->validate($this->signInRules())) {
			return false;
		}
		$user = $this->findByEmail($this->email);
		if (empty($user)) {
			$this->setError('email', '邮箱未注册！');
			return false;
		}
		if (!$user->validatePassword($this->password)) {
			$this->setError('password', '密码错误！');
			return false;
		}
		if ($user->deleted_at > 0) {
            $this->setError('deleted_at', '此用户已被禁止登录！');
            return false;
        }
		if (!empty($this->rememberMe)) {
			$token = Str::random(10);
			$user->token = $token;
			Cookie::set('token', $token, 3600 * 24 * 30);
		}
		if (!$user->save()) {
		    $this->setError($user->getError());
			return false;
		}
		return $user->login();
	}

    /**
     * @return UserModel|boolean
     */
	public function signInHeader() {
        list($this->email, $this->password) = $this->getBasicAuthCredentials();
        return $this->signIn();
    }

    protected function getBasicAuthCredentials() {
        $header = app('request')->header('Authorization');
        if (empty($header)) {
            return [null, null];
        }
        if (is_array($header)) {
            $header = current($header);
        }
        if (strpos($header, 'Basic ') !== 0) {
            return [null, null];
        }
        if (!($decoded = base64_decode(substr($header, 6)))) {
            return [null, null];
        }
        if (strpos($decoded, ':') === false) {
            return [null, null]; // HTTP Basic header without colon isn't valid
        }
        return explode(':', $decoded, 2);
    }
	
	public function signUp() {
		if (!$this->validate($this->signUpRules())) {
			return false;
		}
        $user = $this->findByEmail($this->email);
        if (!empty($user)) {
            $this->setError('email', '邮箱已注册！');
            return false;
        }
		$this->setPassword($this->password);
		$this->created_at = time();
		$this->avatar = '/assets/images/avatar/'.Str::randomInt(0, 48).'.png';
		$this->sex = self::SEX_FEMALE;
		if (!$this->save()) {
			return false;
		}
		return $this->login();
	}

	public function resetPassword() {
		if (!$this->validate($this->resetRules())) {
			return false;
		}
		/** @var $user static */
		$user = auth()->user();
		if (!$user->validatePassword($this->password)) {
			return false;
		}
		$user->setPassword($this->password);
		return $user->save();
	}
}