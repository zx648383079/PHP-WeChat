<?php
declare(strict_types=1);
namespace Module\Book\Domain\Model;

use Domain\Model\Model;

class BookSourceSiteModel extends Model {
    public static function tableName(): string {
        return 'book_source_site';
    }
}