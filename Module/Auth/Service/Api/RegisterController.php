<?php
namespace Module\Auth\Service\Api;

use Module\Auth\Domain\Events\TokenCreated;
use Module\Auth\Domain\Model\UserModel;

use Zodream\Route\Controller\RestController;

class RegisterController extends RestController {

    protected function rules() {
        return [
            'index' => ['POST'],
        ];
    }

    public function indexAction() {
        $model = new UserModel();
        if (!$model->load()
            || !$model->signUp()) {
            return $this->renderFailure($model->getFirstError());
        }
        $user = auth()->user();
        $token = auth()->createToken($user);
        event(new TokenCreated($token, $user));
        return $this->render(array_merge($user->toArray(), [
            'token' => $token
        ]));
    }
}