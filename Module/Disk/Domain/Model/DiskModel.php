<?php
namespace Module\Disk\Domain\Model;

use Zodream\Database\Command;
use Zodream\Database\Model\Model;
use Zodream\Domain\Access\Auth;
use Zodream\Helpers\Time;
use Zodream\Database\Model\Query;

/**
 * Class DiskModel 网盘目录数据
 * @package Domain\Model\Disk
 * @property integer $id
 * @property integer $user_id
 * @property integer $file_id 默认为文件名
 * @property string $name 文件名
 * @property integer $left_id 左值
 * @property integer $right_id 右值
 * @property integer $parent_id 上级
 * @property integer $deleted_at
 * @property integer $updated_at
 * @property integer $created_at
 * @property FileModel $file
 * @method Query auth();
 *
 */
class DiskModel extends Model {




    public static function tableName() {
        return 'disk';
    }

    protected function rules() {
        return [
            'name' => 'required|string:0,100',
            'file_id' => 'int',
            'user_id' => 'int',
            'left_id' => 'int',
            'right_id' => 'int',
            'parent_id' => 'int',
            'deleted_at' => 'int',
            'created_at' => 'int',
            'updated_at' => 'int',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'file_id' => 'File Id',
            'user_id' => 'User Id',
            'left_id' => 'Left Id',
            'right_id' => 'Right Id',
            'parent_id' => 'Parent Id',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function file() {
        return $this->hasOne(FileModel::class, 'id', 'file_id');
    }

    public function scopeAuth($query) {
        return $query->where('user_id', auth()->id());
    }

    public function moveTo(DiskModel $disk) {
        if ($this->parent_id = $disk->id) {
            return false;
        }
        if ($disk->left_id > $this->left_id
            && $disk->right_id < $this->right_id) {
            // 目标为子节点无法移动
            return false;
        }
        // 更改目标上级的左右值
        // 移动并更改左右值
        $difLeft = $disk->right_id - 1 - $this->left_id;
        $this->record()->where('user_id', $this->user_id)
            ->where('left_id', '>', $this->left_id)
            ->where('right_id', '<', $this->right_id)
            ->updateOne([
            'left_id',
            'right_id',
        ], $difLeft);
        $this->left_id += $difLeft;
        $this->right_id += $difLeft;
        $this->parent_id = $disk->id;
        $this->save();

        // 更改原上级的左右值
        $this->record()->where('user_id', $this->user_id)
            ->where('left_id', '<', $this->left_id)
            ->where('right_id', '>', $this->right_id)
            ->updateOne('right_id', $this->left_id - $this->right_id - 1);
        return true;
    }

    public function copyTo(DiskModel $disk) {
        if ($this->parent_id = $disk->id) {
            return false;
        }
        if ($disk->left_id > $this->left_id
            && $disk->right_id < $this->right_id) {
            // 目标为子节点无法移动
            return false;
        }
        // 先空出位置
        $diff = $this->right_id - $this->left_id + 1;
        self::record()->where('user_id', $disk->user_id)
            ->where('left_id', '>=', $disk->right_id)
            ->updateOne('left_id', $diff);
        self::record()->where('user_id', $disk->user_id)
            ->where('right_id', '>=', $disk->right_id)
            ->updateOne('right_id', $diff);
        // 复制开始
        $real_diff = $disk->right_id - $this->left_id;
        $sql = sprintf('INSET INTO %s (name, file_id, user_id, left_id, right_id, parent_id, updated_at, created_at) SELECT name, file_id, %s, left_id + %s, right_id + %s, if(parent_id=%s,%s,parent_id) as pid, updated_at, created_at FROM %s WHERE user_id = %s AND left_id >= %s AND right_id <= %s AND deleted_at = 0',
            self::tableName(), $disk->user_id, $real_diff, $real_diff, $this->parent_id, $disk->id, self::tableName(), $this->user_id, $this->left_id, $this->right_id);
        return Command::getInstance()->execute($sql);
    }

    public function deleteThis() {
        $this->record()->where('user_id', $this->user_id)
            ->where('left_id', '>=', $this->left_id)
            ->where('right_id', '<=', $this->right_id)
            ->delete();
        $num = $this->left_id - $this->right_id - 1;
        $this->record()->where('user_id', $this->user_id)
            ->where('right_id', '>', $this->right_id)
            ->updateOne('right_id', $num);
        $this->record()->where('user_id', $this->user_id)
            ->where('left_id', '>', $this->right_id)
            ->updateOne('left_id', $num);
    }

    /**
     * 软删除
     * @return bool|mixed
     */
    public function softDeleteThis() {
        self::where('user_id', $this->user_id)
            ->where('left_id', '>', $this->left_id)
            ->where('right_id', '<', $this->right_id)
            ->update([
                'deleted_at' => 1
            ]);
        $this->deleted_at = time();
        return $this->save();
    }

    /**
     * 还原
     * @return mixed
     */
    public function resetThis() {
        return self::where('user_id', $this->user_id)
            ->where('left_id', '>=', $this->left_id)
            ->where('right_id', '<=', $this->right_id)
            ->update([
                'deleted_at' => 0
            ]);
    }

    /**
     * 获取子代
     * @return static[]
     */
    public function getChildren() {
        return static::where('parent_id', $this->id)->all() ;
    }

    /**
     * 获取所有后代
     * @return mixed
     */
    public function getAllChildren() {
        return static::where('left_id', '>', $this->left_id)->where('right_id', '<', $this->right_id)->all();
    }

    /**
     * 添加到下一个
     * @param integer $left 上一个的左值
     * @return bool|mixed
     * @throws \Exception
     */
    public function addByLeft($left) {
        $this->left_id = $left + 1;
        $this->right_id = $left + 2;
        self::record()->where('user_id', $this->user_id)
            ->where('left_id', '>', $left)
            ->updateOne('left_id', 2);
        self::record()->where('user_id', $this->user_id)
            ->where('right_id', '>', $left)
            ->updateOne('right_id', 2);
        return $this->save();
    }

    /**
     * 添加到第一个节点
     */
    public function addAsFirst() {
        $left = 0;
        if ($this->parent_id > 0) {
            $left = self::where('id', $this->parent_id)->value('left_id');
        }
        return $this->addByLeft($left);
    }

    /**
     * 添加到节点后面
     * @param DiskModel $model
     * @return bool|mixed
     * @throws \Exception
     */
    public function addAfter(DiskModel $model) {
        return $this->addByLeft($model->right_id);
    }

    /**
     * 添加到节点前面
     * @param DiskModel $model
     * @return bool|mixed
     * @throws \Exception
     */
    public function addBefore(DiskModel $model) {
        $result = $this->addByLeft($this->left_id - 1);
        if ($result) {
            $model->left_id += 2;
            $model->right_id += 2;
        }
        return $result;
    }

    /**
     * 添加到最后一个节点
     */
    public function addAsLast() {
        // 需要判断当前父节点有没有子节点
        if ($this->parent_id > 0) {
            $parent = self::find($this->parent_id);
            if ($parent->left_id == $parent->right_id - 1) {
                // 没有子代
                return $this->addByLeft($parent->left_id);
            }
            return $this->addByLeft($this->right_id - 1);
        }
        $right = intval(self::where('parent_id', $this->parent_id)->max('right_id'));
        return $this->addByLeft($right);
    }

    /**
     * 添加第一个子节点
     * @param DiskModel $model
     * @return bool|mixed
     * @throws \Exception
     */
    public function addFirstChild(DiskModel $model) {
        $result = $model->addByLeft($this->left_id);
        if ($result) {
            $this->right_id += 2;
        }
        return $result;
    }

    /**
     * 添加一个子节点到最后
     * @param DiskModel $model
     * @return bool|mixed
     * @throws \Exception
     */
    public function addLastChild(DiskModel $model) {
        $result = $model->addByLeft($this->right_id - 1);
        if ($result) {
            $this->right_id += 2;
        }
        return $result;
    }

    /**
     * 后面追加
     * @param DiskModel $model
     * @return bool|int
     */
    public function append(DiskModel $model) {
        return $this->addLastChild($model);
    }

    public function prepend(DiskModel $model) {
        return $this->addFirstChild($model);
    }


    public function getCount() {
        return ($this->right_id - $this->left_id - 1) / 2;
    }

    public function getDeletedAtAttribute() {
        $val = $this->getAttributeSource('deleted_at');
        return empty($val) ? '' : Time::format($val);
    }
}