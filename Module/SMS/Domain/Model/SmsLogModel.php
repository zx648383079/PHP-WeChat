<?php
namespace Module\SMS\Doamin\Model;

use Domain\Model\Model;

class SmsLogModel extends Model {
    public static function tableName() {
        return 'sms_log';
    }
}