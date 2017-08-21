<?php
namespace Module\Disk\Service;

use Module\Auth\Domain\Model\UserModel;
use Module\Disk\Domain\Model\DiskModel;
use Module\Disk\Domain\Model\ShareFileModel;
use Module\Disk\Domain\Model\ShareModel;
use Module\Disk\Domain\Model\ShareUserModel;
use Zodream\Disk\FileSystem;
use Zodream\Domain\Access\Auth;
use Zodream\Helpers\Str;
use Zodream\Service\Factory;
use Zodream\Service\Routing\Url;

class DiskController extends Controller {
    
    public function indexAction() {
        return $this->render('index');
    }


    public function listAction($id = 0, $category = null, $offset = 0, $length = 20) {
        if (intval($length) < 1) {
            return $this->jsonFailure('长度不对！');
        }
        $user = Auth::id();
        $query = DiskModel::where(['user_id' => $user,
            'parent_id' => $id, 'delete_at' => null])->ofType(intval($category));
        return $this->jsonSuccess($query->limit($length)->offset($offset)->asArray()->all());
    }

    public function actionDelete() {
        $data = Request::post('id');
        if (empty($data)) {
            return $this->jsonFailure('不能为空！');
        }
        $user = Auth::id();
        $row = DiskModel::where([
                'id' => ['in', (array)$data],
                'user_id' => $user
            ])->update([
                'deleted_at' => time()
            ]);
        if (empty($row)) {
            return $this->jsonFailure('服务器错误！');
        }
        return $this->jsonSuccess();
    }

    public function actionShare() {
        $data = Request::post('id,user,mode public,end_at,role 0');
        if (empty($data['id'])) {
            return $this->jsonFailure('不能为空！');
        }
        if (!empty($data['user'])) {
            $data['mode'] = 'private';
        }
        $disk = DiskModel::find(current($data['id']));
        $user = Auth::id();
        if ($disk->user_id != $user) {
            return $this->jsonFailure('请不要进行危险操作！');
        }
        $model = new ShareModel();
        $model->title = Str::substr($disk->name, 0, 36).(count($data['id']) > 1 ? '等'.count($data['id']).'个文件' : null);
        $model->mode = $data['mode'];
        $model->user_id = $user;
        if ($data['mode'] == 'protected') {
            $data['password'] = $model->password = Str::random(6);
        }
        $model->created_at = time();
        if (!empty($data['end_at'])) {
            $model->end_at = strtotime($data['end_at']);
        }
        if (!$model->save()) {
            return $this->jsonFailure('分享失败');
        }
        $data['url'] = Url::to(['/share', 'id' => $model->id], true);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $disks = [];
            foreach ((array)$data['id'] as $item) {
                $disks[] = [$item, $model->id];
            }
            ShareFileModel::record()
                ->batchInsert('zd_share_disk', ['disk_id', 'share_id'], $disks);
            if ($model->mode == 'private') {
                $users = [];
                foreach ((array)$data['user'] as $item) {
                    $users[] = [$item, $model->id];
                }
                ShareUserModel::record()
                    ->batchInsert('zd_share_user', ['user_id', 'share_id'], $users);
//                Bulletin::message($data['user'],
//                    Yii::$app->user->identity->usrname.'给你分享了文件！',
//                    Html::a('查看', $data['url']));
            }
//            if ($model->mode == 'internal') {
//                Yii::$app->db->createCommand()
//                    ->batchInsert('zd_share_role', ['role_id', 'share_id'], [[$data['role'], $model->id]])
//                    ->execute();
//            }
            $transaction->commit();
        } catch (Exception $ex) {
            $model->delete();
            $transaction->rollBack();
            return $this->jsonFailure('分享失败！');
        }
        return $this->jsonSuccess($data);
    }

    /**
     * 增加文件夹
     */
    public function actionCreate() {
        $model = new DiskModel();
        $model->name = Request::post('name');
        $model->parent_id = Request::post('parent_id', 0);
        $model->created_at = $model->updated_at = time();
        $model->user_id = Auth::id();
        $model->is_dir = 1;
        if (!$model->save()) {
            return $this->jsonFailure($model->getFirstErrors());
        }
        return $this->jsonSuccess($model->toArray());
    }

    function actionRename() {
        $data = Request::post('id,name');
        $model = DiskModel::find($data['id']);
        if (empty($model)) {
            return $this->jsonFailure('选择错误的文件！');
        }
        $model->name = $data['name'];
        $model->updated_at = time();
        if (!$model->save()) {
            return $this->jsonFailure('修改失败！');
        }
        return $this->jsonSuccess($model);
    }

    function actionCheck() {
        $data = Request::post('md5,name,parent_id');
        if (empty($data['md5']) || empty($data['name'])) {
            return $this->jsonFailure('不能为空！');
        }
        $model = DiskModel::find(['md5' => $data['md5']]);
        if (empty($model)) {
            return $this->jsonFailure('MD5 Error', 2);
        }
        $data['extension'] = FileSystem::getExtension($data['name']);
        $data['size'] = $model->size;
        $data['location'] = $model->location;
        $disk = new DiskModel();
        $disk->user_id = Auth::id();
        $data['created_at'] = $data['updated_at'] = time();
        if (!$disk->load($data, '') || !$disk->save()) {
            return $this->jsonFailure('添加失败', 3);
        }
        $data['id'] = $disk->id;
        unset($data['localhost']);
        return $this->jsonSuccess($data);
    }

    public function actionAdd() {
        $data = Request::post('name,md5,size,parent_id 0,type:extension,temp');
        $file = Factory::root()->file($this->configs['cache'].$data['temp']);
        if (!$file->exist() || $file->size() != $data['size']) {
            return $this->jsonFailure('FILE ERROR!');
        }
        $data['location'] = md5($data['name'].time()).FileSystem::getExtension($data['name'], true);
        if (!$file->move(Factory::root()->file($this->config['disk'].$data['location']))) {
            return $this->jsonFailure('MOVE FILE ERROR!');
        }
        $model = new DiskModel();
        $model->user_id = Auth::id();
        $data['created_at'] = $data['updated_at'] = time();
        if (!$model->load($data, '') || !$model->save()) {
            return $this->jsonFailure($model->getErrors());
        }
        $data['id'] = $model->id;
        unset($data['location']);
        return $this->jsonSuccess($data);
    }

    public function actionFolder($id) {
        $user = Auth::id();
        $data = DiskModel::where([
            'is_dir' => 1,
            'user_id' => $user,
            'parent_id' => $id
        ])->select('id,name,parent_id')->asArray()->all();
        return $this->jsonSuccess($data);
    }

    public function actionMove() {
        $data = Request::post('id,parent 0,mode 0');
        if (empty($data['id'])) {
            return $this->jsonFailure('没有移动对象');
        }
        $user = Auth::id();
        if ($data['mode'] != 1) {
            $result = DiskModel::where([
                    'user_id' => $user,
                    'id' => ['in', (array)$data['id']]
                ])->update([
                    'parent_id' => intval($data['parent']),
                    'updated_at' => time()
                ]);
            if (empty($result)) {
                return $this->jsonFailure('服务器错误!');
            }
            return $this->jsonSuccess('成功');
        }
        $models = DiskModel::where(['in', 'id', $data['id']])
            ->andWhere(['user_id' => $user])
            ->select([
                'name',
                'extension',
                'icon',
                'size',
                'md5',
                'location',
                'is_dir',
                'parent_id',
                'user_id',
                'update_at',
                'create_at',
            ])
            ->asArray()->all();
        $time = time();
        $args = [];
        foreach ($models as $item) {
            $args[] = [$item['name'], $item['extension'], $item['icon'], $item['size'], $item['md5'], $item['location'], $item['is_dir'],  $data['parent'], $user, $time, $time];
        }
        if (DiskModel::record()->batchInsert([
            'name',
            'extension',
            'icon',
            'size',
            'md5',
            'location',
            'is_dir',
            'parent_id',
            'user_id',
            'update_at',
            'create_at',
        ], $args)) {
            return $this->jsonSuccess('成功');
        }
        return $this->jsonFailure('服务器错误！');
    }
}