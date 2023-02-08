<?php
namespace Module\Disk\Domain\Model;

use Module\Auth\Domain\Model\UserModel;
use Domain\Model\Model;
use Module\Auth\Domain\Model\UserSimpleModel;

/**
 *
 * @property integer $server_id
 * @property integer $file_id
 */
class ServerFileModel extends Model {

    public static function tableName() {
        return 'disk_server_file';
    }

    protected function rules() {
        return [
            'server_id' => 'required|int',
            'file_id' => 'required|int',
        ];
    }

    protected function labels() {
        return [
            'server_id' => 'Server Id',
            'file_id' => 'File Id',
        ];
    }
}