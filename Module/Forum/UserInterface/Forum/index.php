<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->title = $forum->name;
$this->registerCssFile([
        '@dialog.css',
        '@forum.css',
    ])
    ->registerJsFile([
        '@main.min.js',
        '@jquery.dialog.min.js',
        '@forum.min.js'
    ]);
?>

<div class="container">
    <ul class="path">
        <li>
            <a href="<?=$this->url('/')?>" class="fa fa-home"></a>
        </li><li>
            <a href="<?=$this->url('./')?>">圈子首页</a>
        </li>
        <?php foreach($path as $item):?>
        <li>
            <a href="<?=$this->url('./forum', ['id' => $item->id])?>"><?=$item->name?></a>
        </li>
        <?php endforeach;?>
        <li class="active">
            <a href="<?=$this->url('./forum', ['id' => $forum->id])?>"><?=$forum->name?></a>
        </li>
    </ul>
</div>

<div class="container">
    <div class="forum-group">
        <div class="group-header">
            <?=$forum->name?>
        </div>
        <div class="group-body">
            <?php foreach($forum_list as $item):?>
            <div class="forum-item">
                <div class="thumb">
                    <img src="<?=$this->asset('images/zd.jpg')?>" alt="">
                </div>
                <div class="info">
                    <div class="name">
                        <a href="<?=$this->url('./forum', ['id' => $item->id])?>"><?=$item['name']?></a>
                        <span>(1)</span>
                    </div>
                    <div class="count">主题：，帖数：</div>
                    <div class="last-thread">
                        <a href=""></a>
                        5分钟 admin
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>

    <div class="thread-list">
        <?php $this->extend('./page');?>
    </div>

    <?php if(!auth()->guest() && $forum->parent_id > 0):?>
    <div class="thread-new-box">
        <div class="header">快速发帖</div>
        <form data-type="ajax" action="<?=$this->url('./thread/create')?>" method="post">
            <div class="title">
                <select name="classify_id">
                    <option value="0">选择主题分类</option>
                    <?php foreach($classify_list as $item):?>
                    <option value="<?=$item->id?>"><?=$item->name?></option>
                    <?php endforeach;?>
                </select>
                <input type="text" name="title" required>
            </div>
            <textarea name="content" required></textarea>
            <div class="footer">
                <button class="btn">发表帖子</button>
                <input type="hidden" name="forum_id" value="<?=$forum->id?>">
            </div>
        </form>
    </div>
    <?php endif;?>
</div>
