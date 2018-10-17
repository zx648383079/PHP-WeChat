<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->registerCssFile([
    '@font-awesome.min.css',
    '@zodream.css',
    '@dialog.css',
    '@template.css'
])->registerJsFile([
    '@jquery.min.js',
    '@jquery.dialog.min.js',
    '@jquery.datetimer.min.js',
    '@main.min.js',
    '@template.min.js'
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
   <?=$content?>
   <?=$this->footer()?>
   </body>
</html>