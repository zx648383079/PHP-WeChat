<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->title = '首页';
?>

<div class="container">
    <div class="metro-grid">
        <a href="<?=$this->url('blog')?>">
            博客
        </a>
        <a href="<?=$this->url('doc')?>">
            文档
        </a>
        <a href="<?=$this->url('tool')?>">
            工具
        </a>
        <a class="unknow" href="<?=$this->url('')?>">
            <img src="<?=$this->asset('images/zd_seo.jpg')?>" alt="">
            待定
        </a>
        <a class="unknow" href="<?=$this->url('')?>">
            待定
        </a>
        <a class="unknow" href="<?=$this->url('')?>">
            待定
        </a>
    </div>

    <div class="panel">
        <div class="panel-header">
            最新博客
        </div>
        <div class="panel-body">
            <?=$this->node('blog-panel', ['limit' => 6])?>
        </div>
    </div>
</div>
