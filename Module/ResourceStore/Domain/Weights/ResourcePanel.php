<?php
declare(strict_types=1);
namespace Module\ResourceStore\Domain\Weights;

use Module\ResourceStore\Domain\Models\ResourceModel;
use Module\ResourceStore\Domain\Repositories\ResourceRepository;
use Module\Template\Domain\Weights\Node;
use Zodream\Helpers\Html;
use Zodream\Helpers\Time;

class ResourcePanel extends Node {

    const KEY = 'home_resource_store';

    public function render($type = null) {
        $limit = intval($this->attr('limit'));
        $category = intval($this->attr('category'));
        $keywords = (string)$this->attr('keywords');
        $sort = (string)$this->attr('sort') ?: 'new';
        $cb = function () use ($category, $keywords, $sort, $limit) {
            $data = ResourceRepository::getLimitList(10, $keywords, $category, 0, '', $sort);
            $html = implode('', array_map(function (ResourceModel $item) {
                $url = url('/res', ['id' => $item->id]);
                $title = Html::text($item->title);
                $meta = Html::text($item->description);
                $ago = Time::isTimeAgo($item->getAttributeSource('created_at'), 2678400);
                return <<<HTML
<div class="col-md-3">
    <div class="card-list-item">
        <a class="item-thumb" href="{$url}">
            <img src="{$item->thumb}" alt="{$title}"/>
        </a>
        <div class="item-body">
            <div class="item-title"><a class="name" href="{$url}">{$title}</a></div>
            <div class="item-meta">{$meta}</div>
            <div class="item-time">{$ago}</div>
        </div>
    </div>
</div>
HTML;
            }, $data));
            return <<<HTML
<div class="row">
    {$html}
</div>
HTML;
        };
        if (app()->isDebug()) {
            return $cb();
        }
        return $this->cache()->getOrSet(sprintf('%s-%s-%s-%s-%s',
            self::KEY, $category, $keywords, $sort, $limit),
            $cb, 3600
        );
    }
}