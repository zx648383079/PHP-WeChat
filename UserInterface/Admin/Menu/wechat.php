<div>微信管理</div>
<ul>
    <li data="wechat">账号管理</li>
    <?php if (!empty(\Zodream\Infrastructure\Session::getValue('wechat'))) {?>
    <li>功能
        <ul>
            <li data="wechat/reply">自动回复</li>
            <li>群发功能</li>
            <li>自定义菜单</li>
            <li>投票管理</li>
        </ul>
    </li>
    <li>管理
        <ul>
            <li>消息管理</li>
            <li>用户管理</li>
            <li>素材管理
                <ul>
                    <li>图文消息</li>
                    <li>图片</li>
                    <li>语音</li>
                    <li>视频</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>微信商城</li>
    <?php }?>
</ul>