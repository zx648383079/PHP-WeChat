<?php
namespace Module\Auth\Service\Api;

use Module\Auth\Domain\Events\TokenCreated;
use Module\Auth\Domain\Repositories\AuthRepository;
use Module\Auth\Domain\Repositories\UserRepository;
use Zodream\Infrastructure\Http\Request;
use Zodream\Route\Controller\RestController;

class RegisterController extends RestController {

    protected function methods() {
        return [
            'index' => ['POST'],
        ];
    }

    public function indexAction(Request $request) {
        try {
            if ($request->has('mobile')) {
                AuthRepository::registerMobile(
                    $request->get('name'),
                    $request->get('mobile'),
                    $request->get('code'),
                    $request->get('password'),
                    $request->get('rePassword') ?: $request->get('confirm_password'),
                    $request->has('agree')
                );
            } else {
                AuthRepository::register(
                    $request->get('name'),
                    $request->get('email'),
                    $request->get('password'),
                    $request->get('rePassword') ?: $request->get('confirm_password'),
                    $request->has('agree')
                );
            }
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
        $user = auth()->user();
        $token = auth()->createToken($user);
        event(new TokenCreated($token, $user));
        $data = UserRepository::getCurrentProfile();
        $data['token'] = $token;
        return $this->render($data);
    }
}