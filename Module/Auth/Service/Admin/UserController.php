<?php
namespace Module\Auth\Service\Admin;


use Module\Auth\Domain\Model\OAuthModel;
use Module\Auth\Domain\Model\UserModel;
use Zodream\Domain\Access\Auth;
use Zodream\Infrastructure\Http\Request;
use Zodream\Validate\Validator;

class UserController extends Controller {

    public function indexAction($keywords = null) {
        $user_list = UserModel::where('id', '!=', Auth::id())
            ->when(!empty($keywords), function ($query) {
            OAuthModel::search($query, 'name');
        })->page();
        return $this->show(compact('user_list'));
    }

    public function createAction() {
        return $this->runMethod('edit', ['id' => null]);
    }

    public function editAction($id) {
        $model = UserModel::findOrNew($id);
        return $this->show(compact('model'));
    }

    public function saveAction() {
        $id = intval(Request::request('id'));
        $rule = $id > 0 ? [
            'name' => 'required|string',
            'email' => 'required|email',
            'sex' => 'int',
            'avatar' => 'string',
            'password' => 'string',
        ] : [
            'name' => 'required|string',
            'email' => 'required|email',
            'sex' => 'int',
            'avatar' => 'string',
            'password' => 'required|string',
        ];
        $data = Request::post('name,email,sex,avatar,password,confirm_password');
        if ($id < 1 && $data['password'] != $data['confirm_password']) {
            return $this->jsonFailure('两次密码不一致！');
        }
        $model = UserModel::findOrNew($id);
        if (!$model->load($data) || !$model->validate($rule)) {
            return $this->jsonFailure($model->getFirstError());
        }
        if (!empty($data['password'])) {
            $model->setPassword($data['password']);
        }
        if (!$model->save()) {
            return $this->jsonFailure($model->getFirstError());
        }
        return $this->jsonSuccess([
            'url' => $this->getUrl('user')
        ]);
    }

    public function deleteAction($id) {
        if ($id == Auth::id()) {
            return $this->jsonFailure('不能删除自己！');
        }
        UserModel::where('id', $id)->delete();
        return $this->jsonSuccess([
            'url' => $this->getUrl('user')
        ]);
    }
}