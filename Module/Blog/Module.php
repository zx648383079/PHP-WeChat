<?php
namespace Module\Blog;

use Module\Blog\Domain\Migrations\CreateBlogTables;
use Module\Blog\Domain\Model\BlogModel;
use Module\SEO\Domain\SiteMap;
use Zodream\Route\Controller\Module as BaseModule;

class Module extends BaseModule {

    public function getMigration() {
        return new CreateBlogTables();
    }

    public function openLinks(SiteMap $map) {
        $map->add(url('./'), time());
        $map->add(url('./tag'), time());
        $map->add(url('./category'), time());
        $map->add(url('./archives'), time());
        $items = BlogModel::where('open_type', '<>', BlogModel::OPEN_DRAFT)
            ->orderBy('id', 'desc')
            ->get('id', 'updated_at');
        foreach ($items as $item) {
            $map->add($item->url,
                $item->updated_at, SiteMap::CHANGE_FREQUENCY_WEEKLY, .8);
        }
    }
}