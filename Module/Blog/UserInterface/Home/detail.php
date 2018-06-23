<?php
use Zodream\Template\View;
use Zodream\Helpers\Str;
/** @var $this View */
$this->title = $blog->title;
$url = (string)$this->url(['./comment', 'blog_id' => $blog->id]);
$recommendUrl = (string)$this->url(['./recommend', 'id' => $blog->id]);
$js = <<<JS
    uParse('#content',{rootPath:'/assets/ueditor'});
    SyntaxHighlighter.all();
    $.get('{$url}', function(html) {
      $(".book-footer").html(html);
    });
    $(".recommend-blog").click(function() {
      var that = $(this).find('b');
      $.getJSON('{$recommendUrl}', function(data) {
        if (data.code == 200) {
            that.text(data.data);
            return;
        }
        Dialog.tip(data.message);
      })
    });
    $(".book-navicon").click(function() {
        $('.book-skin').toggleClass("book-collapsed");
    });
    $("#content").sideNav({
        target: '.book-side-nav',
        contentLength: 20
    });
JS;

$this->registerCssFile('ueditor/third-party/SyntaxHighlighter/shCoreDefault.css');
$this->extend('layouts/header', [
        'keywords' => $blog->keywords,
        'description' => $blog->description,
    ])
    ->registerJsFile('@jquery.sideNav.min.js')
    ->registerJs($js, View::JQUERY_READY)
    ->registerJsFile('ueditor/ueditor.parse.min.js')
    ->registerJsFile('ueditor/third-party/SyntaxHighlighter/shCore.js');
?>
    <div class="book-title book-mobile-inline">
        <ul class="book-nav">
            <li class="book-navicon">
                <i class="fa fa-navicon"></i>
            </li>
            <li class="book-back"><a href="<?=$this->url('blog')?>">返回</a></li>
            <?php if ($blog->previous):?>
            <li><a href="<?=$blog->previous->url?>"><?=$blog->previous->title?></a></li>
            <?php endif;?>
            <li class="active"><?=$blog->title?></li>
            <?php if ($blog->next):?>
            <li><a href="<?=$blog->next->url?>"><?=$blog->next->title?></a></li>
            <?php endif;?>
        </ul>
    </div>

    <div class="book-sidebar">
        <div class="book-chapter">
            <ul>
                <?php foreach ($cat_list as $item): ?>
                    <li <?=$blog->term_id == $item->id ? 'class="active"' : '' ?>>
                        <i class="fa fa-bookmark"></i><a href="<?=$item->url?>"><?=$item->name?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="book-dynamic">
            <?php foreach ($log_list as $log): ?>
                <dl>
                    <dt><a><?=$log['name']?></a> <?=$log['action']?>了 《<a href="<?=$this->url('./detail/id/'.$log['blog_id'])?>"><?=$log['title']?></a>》</dt>
                    <dd>
                        <p><?=$log['content']?></p>
                        <span class="book-time"><?=$this->ago($log['create_at'])?></span>
                    </dd>
                </dl>
            <?php endforeach;?>
        </div>

        <div class="book-side-nav">
        </div>
    </div>


    <div class="book-body">
        <a class="book-fork" href="https://github.com/zx648383079/PHP-ZoDream">
            <img src="/assets/images/forkme.png" alt="Fork Me On Github">
        </a>
        <div class="info">
            <span class="author"><i class="fa fa-edit"></i><b><?=$blog->user->name?></b></span>
            <span class="category"><i class="fa fa-bookmark"></i><b><?=$blog->term->name?></b></span>
            <span class="time"><i class="fa fa-calendar-check-o"></i><b><?=$blog->created_at?></b></span>
            <?php if($blog->type == 1):?>
            <span class="type">
                <a href="<?=$blog->source_url?>">
                    <i class="fa fa-link"></i><b>转载</b>
                </a>
            </span>
            <?php endif;?>
        </div>
        <div id="content" class="content">
            <?=$blog->content?>
        </div>
        <div class="tools">
            <span class="comment"><i class="fa fa-comments"></i><b><?=$blog->comment_count?></b></span>
            <span class="click"><i class="fa fa-eye"></i><b><?=$blog->click_count?></b></span>
            <span class="agree recommend-blog"><i class="fa fa-thumbs-o-up"></i><b><?=$blog->recommend?></b></span>
        </div>
    </div>
    <div id="comments" class="book-footer comment">
        
    </div>

    <?php $this->extend('layouts/footer');?>