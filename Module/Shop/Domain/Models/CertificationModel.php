<?php
namespace Module\Shop\Domain\Models;

use Domain\Model\Model;

class CertificationModel extends Model {

    public static function tableName() {
        return 'shop_certification';
    }
}