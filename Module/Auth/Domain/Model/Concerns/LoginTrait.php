<?php
namespace Module\Auth\Domain\Model\Concerns;

use Zodream\Helpers\Str;
use Zodream\Infrastructure\Cookie;

trait LoginTrait {

    public function signInRules() {
        return [
            'email' => 'required|email',
            'password' => 'required|string:0,30',
            'code' => 'validateCode'
        ];
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
}