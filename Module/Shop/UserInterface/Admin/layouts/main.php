<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;

/** @var $this View */
$this->registerCssFile([
        '@font-awesome.min.css',
        '@zodream.css',
        '@zodream-admin.css',
        '@dialog.css',
        '@shop_admin.css'
    ])->registerJsFile([
        '@jquery.min.js',
        '@jquery.dialog.min.js',
        '@jquery.upload.min.js',
        '@main.min.js',
        '@shop_admin.min.js'
    ]);
?>
<!DOCTYPE html>
<html lang="<?=$this->get('language', 'zh-CN')?>">
   <head>
       <meta name="viewport" content="width=device-width, initial-scale=1"/>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <meta name="Description" content="<?=$this->description?>" />
       <meta name="keywords" content="<?=$this->keywords?>" />
       <title><?=$this->title?></title>
       <?=$this->header();?>
   </head>
   <body>
   <header>
        <div class="container">
            ZoDream Shop Admin
        </div>
    </header>
    <div class="container page-box">
        <div class="left-catelog navbar">
            <span class="left-catelog-toggle"></span>
            <ul>
                <li><a href="<?=$this->url('./admin')?>"><i class="fa fa-home"></i><span>首页</span></a></li>
                <li class="expand"><a href="javascript:;"><i class="fa fa-briefcase"></i><span>商品管理</span></a>
                    <ul>
                        <li><a href="<?=$this->url('./admin/goods')?>"><i class="fa fa-list"></i><span>商品列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/goods/create')?>"><i class="fa fa-list"></i><span>新建商品</span></a></li>
                        <li><a href="<?=$this->url('./admin/category')?>"><i class="fa fa-list"></i><span>分类列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/category/create')?>"><i class="fa fa-list"></i><span>新建分类</span></a></li>
                        <li><a href="<?=$this->url('./admin/brand')?>"><i class="fa fa-list"></i><span>品牌列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/brand/create')?>"><i class="fa fa-list"></i><span>新建品牌</span></a></li>
                    </ul>
                </li>
                <li class="expand">
                    <a href="javascript:;"><i class="fa fa-briefcase"></i><span>订单管理</span></a>
                    <ul>
                    <li><a href="<?=$this->url('./admin/order')?>"><i class="fa fa-list"></i><span>订单列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/order/create')?>"><i class="fa fa-list"></i><span>新建订单</span></a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="right-content">
            <?=$content?>
        </div>
    </div>
   <?=$this->footer()?>
   </body>
</html>