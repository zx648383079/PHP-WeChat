<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
use Zodream\Domain\Access\Auth;
/** @var $this View */
$this->registerCssFile([
        '@font-awesome.min.css',
        '@zodream.css',
        '@zodream-admin.css',
        '@dialog.css',
        '@blog_admin.css'
    ])->registerJsFile([
        '@jquery.min.js',
        '@jquery.dialog.min.js',
        '@jquery.upload.min.js',
        '@main.min.js',
        '@blog_admin.min.js'
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
            ZoDream 博客管理平台
        </div>
    </header>
    <div class="container page-box">
        <div class="left-catelog navbar">
            <span class="left-catelog-toggle"></span>
            <ul>
                <li><a href="<?=$this->url('./admin')?>"><i class="fa fa-home"></i><span>首页</span></a></li>
                <li class="expand"><a href="javascript:;">
                        <i class="fa fa-book"></i><span>文章管理</span></a>
                    <ul>
                        <li><a href="<?=$this->url('./admin/blog')?>">
                                <i class="fa fa-list"></i><span>文章列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/blog/create')?>">
                                <i class="fa fa-edit"></i><span>发表文章</span></a></li>
                    </ul>
                </li>
                <li class="expand"><a href="javascript:;">
                        <i class="fa fa-tags"></i><span>分类管理</span></a>
                    <ul>
                        <li><a href="<?=$this->url('./admin/term')?>"><i class="fa fa-list"></i><span>分类列表</span></a></li>
                        <li><a href="<?=$this->url('./admin/term/create')?>"><i class="fa fa-edit"></i><span>新增分类</span></a></li>
                    </ul>
                </li>
                <li><a href="<?=$this->url('./admin/comment')?>"><i class="fa fa-comment"></i><span>评论</span></a></li>
            </ul>
        </div>
        <div class="right-content">
            <?=$content?>
        </div>
    </div>
   <?=$this->footer()?>
   </body>
</html>