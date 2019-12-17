<?php
namespace Module\Counter\Domain\Model;

use Domain\Model\Model;
use Zodream\Infrastructure\Http\Request;

/**
 * Class PageLogModel
 * @package Module\Counter\Domain\Model
 * @property integer $id
 * @property string $url
 * @property integer $visit_count
 */
class PageLogModel extends Model {

    public $timestamps = false;

    public static function tableName() {
        return 'ctr_page_log';
    }

    protected function rules() {
        return [
            'url' => 'required|string:0,255',
            'visit_count' => 'int',
        ];
    }

    protected function labels() {
        return [
            'id' => 'Id',
            'url' => 'Url',
            'visit_count' => 'Visit Count',
        ];
    }

    public static function log(Request $request) {
        if ($request->has('loaded') || $request->has('leave')) {
            return;
        }
        $url = (string)$request->uri();
        $model = static::where('url', $url)->first();
        if ($model) {
            $model->visit_count ++;
            $model->save();
            return;
        }
        static::create([
            'url' => $url,
            'visit_count' => 1
        ]);
    }
}
