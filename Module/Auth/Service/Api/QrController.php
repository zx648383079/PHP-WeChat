<?php
namespace Module\Auth\Service\Api;

use Module\Auth\Domain\Model\LoginQrModel;
use Module\Auth\Domain\Model\UserModel;
use Zodream\Http\Uri;
use Zodream\Infrastructure\Http\Output\RestResponse;
use Zodream\Infrastructure\Http\Response;
use Zodream\Route\Controller\RestController;

class QrController extends RestController {

    protected function rules() {
        return [
            '*' => '@',
        ];
    }

    /**
     * 验证token
     * @param $token
     * @return Response
     * @throws \Exception
     */
    public function indexAction($token) {
        $model = LoginQrModel::findByToken($token);
        if (empty($model) || $model->isExpired() ||
            !in_array($model->status, [LoginQrModel::STATUS_UN_SCAN, LoginQrModel::STATUS_UN_CONFIRM])) {
            return $this->renderFailure('二维码已失效');
        }
        return $this->render($model->toArray());
    }


    /**
     * @param $token
     * @param bool $confirm
     * @param bool $reject
     * @return RestResponse
     * @throws \Exception
     */
    public function authorizeAction($token, $confirm = false, $reject = false) {
        if (strpos($token, 'token=') > 0) {
            $url = new Uri($token);
            $token = $url->getData('token');
        }
        $model = LoginQrModel::findByToken($token);
        if (empty($model) || $model->isExpired() ||
            !in_array($model->status, [LoginQrModel::STATUS_UN_SCAN, LoginQrModel::STATUS_UN_CONFIRM])) {
            return $this->renderFailure('二维码已失效');
        }
        $model->user_id = auth()->id();
        if (!empty($confirm) && $model->status == LoginQrModel::STATUS_UN_CONFIRM) {
            $model->status = LoginQrModel::STATUS_SUCCESS;
            $model->save();
            return $this->render($model->toArray());
        }
        if (!empty($reject)) {
            $model->status = LoginQrModel::STATUS_REJECT;
            $model->save();
            return $this->render($model->toArray());
        }
        return $this->render($model->toArray());
    }
}