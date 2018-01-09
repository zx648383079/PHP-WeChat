<?php
namespace Module\Template\Domain\Model;

use Domain\Model\Model;
use Module\Template\Service\BaseWeight;
use phpDocumentor\Reflection\Types\Self_;
use Zodream\Disk\Directory;
use Zodream\Disk\File;
use Zodream\Disk\FileException;
use Zodream\Disk\FileObject;
use Zodream\Helpers\Str;
use Zodream\Service\Factory;

/**
 * 安装的部件列表
 * @package Module\Template
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $thumb
 * @property integer $type
 * @property string $path
 * @property integer $editable
 */
class WeightModel extends Model {

    const TYPE_BASIC = 0;
    const TYPE_ADVANCE = 1;

    const ADAPT_ALL = 0; // 自适应
    const ADAPT_PC = 1;  // 适应PC
    const ADAPT_MOBILE = 2; // 适应手机

    public static function tableName() {
        return 'weight';
    }


    protected function rules() {
        return [
            'name' => 'required|string:3-30',
            'description' => 'string:3-200',
            'thumb' => 'string:3-100',
            'type' => 'int:0-3',
            'editable' => 'int:0-1',
            'path' => 'string:3-200',
        ];
    }

    protected function labels() {
        return [
            'name' => 'Name',
            'description' => 'Description',
            'thumb' => 'Thumb',
            'type' => 'Type',
            'editable' => 'Editable',
            'path' => 'Path',
        ];
    }

    public function getPostConfigs() {

    }

    /**
     * @return BaseWeight
     * @throws FileException
     */
    public function getWeightInstance() {
        $path = $this->path;
        if (empty($path)) {
            throw new FileException($path);
        }
        if (is_dir($path)) {
            $path = rtrim($path, '/\\'). '/weight.php';
        }
        if (!is_file($path)) {
            return new $path;
        }
        include_once $path;
        $name = Str::studly($this->name).'Weight';
        return new $name;
    }


    public static function findWeights() {
        $directory = new Directory(dirname(dirname(__DIR__)) . '/UserInterface/weights');
        return static::getWeights($directory);
    }

    protected static function getWeights(Directory $dir) {
        if ($dir->hasFile('weight.json')) {
            return [static::getWeightInfo($dir)];
        }
        $weights = [];
        $dir->map(function (FileObject $file) use (&$weights) {
            if (!($file instanceof Directory)) {
                return;
            }
            $weights = array_merge($weights, static::getWeights($file));
        });
        return $weights;
    }

    protected static function getWeightInfo(Directory $directory) {
        $data = json_decode($directory->childFile('weight.json')->read(), true);
        $data['path'] = $directory;
        return $data;
    }

    /**
     * 是否已安装
     * @param $name
     * @return bool
     */
    public static function isInstalled($name) {
        return static::where('name', $name)->count() > 0;
    }

    /**
     * 创建
     * @param $name
     * @param $path
     * @param $thumb
     * @param null $description
     * @param bool $editable  是否允许编辑
     * @param int $type
     * @return static
     */
    public static function install($name,
                                   $path = null,
                                   $thumb = null,
                                   $description = null,
                                   $editable = true,
                                   $type = self::TYPE_BASIC) {
        if (is_array($name)) {
            extract($name);
        }
        $path = (string)$path;
        return static::create(compact('name', 'path',
            'description', 'type', 'thumb', 'editable'));
    }
}