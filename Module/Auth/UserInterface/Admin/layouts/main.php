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
        '@auth.css'
    ])->registerJsFile([
        '@jquery.min.js',
        '@jquery.dialog.min.js',
        '@jquery.upload.min.js',
        '@main.min.js',
        '@auth.min.js'
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
            ZoDream Account Admin
        </div>
    </header>
    <div class="container page-box">
        <div class="left-catelog navbar">
            <span class="left-catelog-toggle"></span>
            <ul>
                <li><a href="<?=$this->url('./admin')?>">
                        <i class="fa fa-home"></i><span>首页</span></a></li>
                <li class="expand"><a href="javascript:;">
                        <i class="fa fa-users"></i><span>用户管理</span></a>
                    <ul>
                            <li><a href="<?=$this->url('./admin/user')?>">
                                <i class="fa fa-list"></i><span>用户列表</span></a></li>
                            <li><a href="<?=$this->url('./admin/user/create')?>">
                                <i class="fa fa-plus"></i><span>新增用户</span></a></li>
                    </ul>
                </li>
                <li class="expand"><a href="javascript:;">
                        <i class="fa fa-user"></i><span><?=Auth::user()->name?></span></a>
                    <ul>
                            <li><a href="<?=$this->url('./admin/account')?>">
                                <i class="fa fa-info-circle"></i><span>个人资料</span></a></li>
                            <li><a href="<?=$this->url('./admin/account/password')?>">
                                <i class="fa fa-key"></i><span>更改密码</span></a></li>
                            <li><a href="<?=$this->url('./logout')?>">
                                <i class="fa fa-sign-out"></i><span>退出登陆</span></a></li>
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