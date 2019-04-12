<?php
namespace Module\Shop\Domain\Models\Advertisement;

use Domain\Model\Model;
use Zodream\Infrastructure\Support\Html;

/**
 * Class AdModel
 * @package Domain\Model\Advertisement
 * @property integer $id
 * @property integer $position_id
 * @property string $name
 * @property integer $type
 * @property string $url
 * @property string $content
 * @property integer $start_at
 * @property integer $end_at
 * @property integer $update_at
 * @property integer $create_at
 */
class AdModel extends Model {
    const TEXT = 0;
    const IMG = 1;
    const HTML = 2;
    const VIDEO = 3;

    public $type_list = [
        self::TEXT => '文本',
        self::IMG => '图片',
        self::HTML => '代码',
        self::VIDEO => '视频',
    ];

    public static function tableName() {
        return 'shop_ad';
    }

    protected function rules() {
        return [
            'name' => 'required|string:0,30',
            'position_id' => 'required|int',
            'type' => 'int:0,9',
            'url' => 'required|string:0,255',
            'content' => 'required|string:0,255',
            'start_at' => 'int',
            'end_at' => 'int',
            'created_at' => 'int',
            'updated_at' => 'int',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'name' => '广告名',
            'position_id' => '广告位',
            'type' => '类型',
            'url' => '链接',
            'content' => '内容',
            'start_at' => '开始时间',
            'end_at' => '结束时间',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function position() {
        return $this->hasOne(AdPositionModel::class, 'id', 'position_id');
    }

    public function getHtml() {
        switch ($this->type) {
            case self::TEXT:
                return Html::a(htmlspecialchars($this->content), $this->url);
            case self::IMG:
                return Html::a(Html::img($this->content), $this->url);
            case self::VIDEO:
                return Html::a(Html::tag('embed', '', ['src' => $this->content]), $this->url);
            case self::HTML:
            default:
                return htmlspecialchars_decode($this->content);
        }
    }

    /**
     * @param integer $id
     * @return AdModel
     * @throws \Exception
     */
    public static function getAd($id) {
        return static::find($id);
    }

    /**
     * @param integer $id
     * @return AdModel[]
     */
    public static function getAds($id) {
        return static::where('position_id', $id)->all();
    }

    public function __toString() {
        return $this->getHtml();
    }

    public static function banners() {
        return [
            [
                'content' => 'https://yanxuan.nosdn.127.net/08c22f34ed0445208c8bbf80cb769d06.jpg?imageView&quality=75&thumbnail=750x0'
            ],
            [
                'content' => 'https://yanxuan.nosdn.127.net/8271dce9c32d58eb8598c1408acf6132.jpg?imageView&quality=75&thumbnail=750x0'
            ],
            [
                'content' => 'https://yanxuan.nosdn.127.net/3693d1b5948a2072fdf3524668e11993.jpg?imageView&quality=75&thumbnail=750x0'
            ],
        ];
    }
}