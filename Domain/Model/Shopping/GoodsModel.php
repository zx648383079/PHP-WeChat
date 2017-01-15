<?php
namespace Domain\Model\Shopping;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/15
 * Time: 19:07
 */
use Zodream\Domain\Html\Page;

/**
 * Class GoodsModel
 * @package Domain\Model\Shopping
 * @property string $image
 * @property string $description
 * @property string $content
 * @property integer $create_at
 * @property integer $update_at
 */
class GoodsModel extends BaseGoodsModel {
    public static function tableName() {
        return 'goods';
    }

    /**
     * @return Page
     */
    public function getComments() {
        return new Page();
    }

    /**
     * @return array
     */
    public function getProperties() {
        return $this->hasMany(GoodsPropertyModel::class, 'goods_id', 'id');
    }

    /**
     * @return GoodsImageModel[]
     */
    public function getImages() {
        return $this->hasMany(GoodsImageModel::class, 'goods_id', 'id');
    }

    /**
     * @return array
     */
    public function getTags() {
        return [];
    }
}