<?php
namespace Module\Book\Domain\Model;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/10/15
 * Time: 21:00
 */
use Domain\Model\Model;
use Zodream\Helpers\Html;

/**
 * Class BookChapterModel
 * @package Domain\Model\Book
 * @property integer $id
 * @property string $content
 */
class BookChapterBodyModel extends Model {
    public static function tableName(): string {
        return 'book_chapter_body';
    }

    protected function rules(): array {
        return [
            'content' => '',
        ];
    }

    protected function labels(): array {
        return [
            'id' => 'Id',
            'content' => '内容',
        ];
    }

    public function getHtmlAttribute() {
        return Html::fromText($this->content);
    }


}