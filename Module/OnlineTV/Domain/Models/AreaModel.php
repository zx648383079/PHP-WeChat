<?php
declare(strict_types=1);
namespace Module\OnlineTV\Domain\Models;

use Domain\Model\Model;

class AreaModel extends Model {

	public static function tableName() {
        return 'tv_area';
    }

}