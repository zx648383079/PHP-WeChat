<?php
defined('APP_DIR') or exit();
/** @var $this \Zodream\Domain\Response\View */
/** @var $page \Zodream\Domain\Html\Page */
$this->extend(array(
    'layout' => array(
        'head'
    ))
);
$page = $this->get('page');
?>
<a class="btn" href="<?php $this->url('post/add');?>">新增</a>
<a class="btn" href="<?php $this->url('post/term');?>">管理分类</a>
<form method="GET">
    搜索： <input type="text" name="search" value="" placeholder="标题" required>
    <button type="submit">搜索</button>
</form>
<table class="table table-hover">
    <thead>
    <tr>
        <th></th>
        <th>标题</th> 
        <th>作者</th> 
        <th>分类目录</th> 
        <th>标签</th> 
        <th>评论</th> 
        <th>日期</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($page->getPage() as $value) {?>
            <tr>
                <td>
                    <input type="checkbox" name="id[]" value="<?php echo $value['id'];?>">
                </td>
                <td><?php echo $value['title'];?></td>
                <td><?php echo $value['user'];?></td>
                <td><?php echo $value['term'];?></td>
                <td><?php //echo $value['tag'];?></td>
                <td><?php echo $value['comment_count'];?></td>
                <td><?php $this->time($value['create_at']);?></td>
                <td>
                    [<a href="<?php $this->url('post/add/id/'.$value['id']);?>">编辑</a>]
                    [<a href="<?php $this->url('post/delete/id/'.$value['id']);?>">删除</a>]
                </td>
            </tr>
        <?php }?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="8">
            <?php $page->pageLink();?>
        </th>
    </tr>
    </tfoot>
</table>
<?php
$this->extend(array(
    'layout' => array(
        'foot'
    ))
);
?>