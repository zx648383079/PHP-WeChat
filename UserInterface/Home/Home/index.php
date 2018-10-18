<?php
defined('APP_DIR') or exit();
/** @var $this \Zodream\Template\View */

$this->extend('layouts/header');
?>

<div class="metro-grid">
    <div class="item hover-left">
        <a href="<?=$this->url('blog')?>">
            <div class="font-gird">
                <img src="/assets/images/blog.png" alt="">
                <p>博客</p>
            </div>
            <div class="back-grid">
                <h3>简介</h3>
                <div class="item-content">
                    本博客只为记录学习及工作。<br/>
                    具体功能正在开发中。。。
                </div>
            </div>
        </a>
    </div>
    <div class="item hover-right">
        <a href="<?=$this->url('tool')?>">
            <div class="font-gird">
                <img src="/assets/images/tool.png" alt="">
                <p>工具</p>
            </div>
            <div class="back-grid">
                <h3>简介</h3>
                <div class="item-content">
                    只为方便服务。。。
                </div>
            </div>
        </a>
    </div>
</div>

<?php $this->extend('layouts/footer')?>