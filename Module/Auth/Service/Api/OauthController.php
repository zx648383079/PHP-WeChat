<?php
namespace Module\Auth\Service\Api;

use Module\Auth\Domain\Model\OAuthModel;
use Module\Auth\Domain\Model\UserModel;
use Module\Auth\Domain\Repositories\AuthRepository;
use Zodream\Route\Controller\RestController;
use Zodream\ThirdParty\WeChat\MiniProgram\OAuth as MiniOAuth;
use Module\Auth\Service\OauthController as BaseController;

class OauthController extends RestController {

    public function indexAction($type = 'qq', $redirect_uri = '') {
        url()->setModulePath(config()->getModulePath(self::class));
        session(['platform' => app('platform')]);
        return (new BaseController())->indexAction($type, $redirect_uri);
    }

    public function miniAction($code, $nickname, $avatar = '', $gender = 2) {
        $mini = new MiniOAuth();
        try {
            $data = $mini->login($code);
            $user = AuthRepository::oauth(
                OAuthModel::TYPE_WX_MINI,
                $data['openid'],
                function () use ($nickname, $gender, $avatar) {
                    return [
                        $nickname,
                        null,
                        $gender == 1 ? UserModel::SEX_MALE : UserModel::SEX_FEMALE,
                        $avatar
                    ];
                }, isset($data['unionid']) ? $data['unionid'] : null);
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
        return $this->render(array_merge($user->toArray(), [
            'token' => auth()->createToken($user)
        ]));
    }
}