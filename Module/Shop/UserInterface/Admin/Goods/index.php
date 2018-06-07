<?php
defined('APP_DIR') or exit();
use Zodream\Template\View;
/** @var $this View */
$this->title = 'ZoDream';
?>
   <div class="search">
        <form class="form-horizontal" role="form">
            <div class="input-group">
                <label class="sr-only" for="keywords">标题</label>
                <input type="text" class="form-control" name="keywords" id="keywords" placeholder="标题">
            </div>
            <button type="submit" class="btn btn-default">搜索</button>
        </form>
        <a class="btn btn-success pull-right" href="<?=$this->url('./admin/goods/create')?>">新增商品</a>
    </div>

    <table class="table  table-bordered well">
        <thead>
        <tr>
            <th>ID</th>
            <th>商品名</th>
            <th>分类</th>
            <th>品牌</th>
            <th>价格</th>
            <th>推荐</th>
            <th>销量</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($model_list as $item):?>
            <tr>
                <td><?=$item->id?></td>
                <td><?=$item->name?></td>
                <td>
                    <?php if ($item->category):?>
                        <a href="<?=$this->url('./admin/goods', ['cat_id' => $item->cat_id])?>">
                            <?=$item->category->name?>
                        </a>
                    <?php else:?>
                    [未分类]
                    <?php endif;?>
                </td>
                <td>
                    <?php if ($item->brand):?>
                        <a href="<?=$this->url('./admin/goods', ['brand_id' => $item->brand_id])?>">
                            <?=$item->brand->name?>
                        </a>
                    <?php else:?>
                    [无]
                    <?php endif;?>
                </td>
                <td>
                    <?=$this->price?>
                </td>
                <td>
                    
                </td>
                <td>
                    <?=$this->sales?>
                </td>
                <td>
                    <div class="btn-group  btn-group-xs">
                        <a class="btn btn-default btn-xs" href="<?=$this->url('./admin/book/chapter', ['book' => $item->id])?>">章节</a>
                        <a class="btn btn-default btn-xs" href="<?=$this->url('./admin/book/edit', ['id' => $item->id])?>">编辑</a>
                        <a class="btn btn-danger" data-type="del" href="<?=$this->url('./admin/book/delete', ['id' => $item->id])?>">删除</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div align="center">
        <?=$model_list->getLink()?>
    </div>