<?php
namespace Module\WeChat\Service\Admin;

use Module\WeChat\Domain\Model\MenuModel;
use Module\WeChat\Domain\Model\WeChatModel;
use Zodream\ThirdParty\WeChat\Menu;

class MenuController extends Controller {

    /**
     * 菜单类型
     * 注: 有value属性的在提交菜单是该类型的值必须设置为此值, 没有的则不限制
     * @var array
     */
    public $menuTypes = [
        'click' => [
            'name' => '关键字',
            'meta' => 'key',
            'alert' => '用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；'
        ],
        'view' => [
            'name' => '链接',
            'meta' => 'url',
            'alert' => '用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。'
        ],
        'scancode_waitmsg' => [
            'name' => '启动扫码并接收服务器消息',
            'meta' => 'key',
            'value' => 'rselfmenu_0_0',
            'alert' => '用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。'
        ],
        'scancode_push' => [
            'name' => '启动扫码',
            'meta' => 'key',
            'value' => 'rselfmenu_0_1',
            'alert' => '用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。'
        ],
        'pic_sysphoto' => [
            'name' => '启动拍照',
            'meta' => 'key',
            'value' => 'rselfmenu_1_0',
            'alert' => '用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。'
        ],
        'pic_photo_or_album' => [
            'name' => '启动拍照或者相册',
            'meta' => 'key',
            'value' => 'rselfmenu_1_1',
            'alert' => '用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。'
        ],
        'pic_weixin' => [
            'name' => '启动微信相册',
            'meta' => 'key',
            'value' => 'rselfmenu_1_2',
            'alert' => '用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。'
        ],
        'location_select' => [
            'name' => '发送位置信息',
            'meta' => 'key',
            'value' => 'rselfmenu_2_0',
            'alert' => '用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。'
        ]
    ];

    protected function rules() {
        return [
            '*' => 'w'
        ];
    }

    public function indexAction() {
        $menu_list = MenuModel::where('parent_id', 0)->all();
        return $this->show(compact('menu_list'));
    }

    public function addAction($parent_id = 0) {
        return $this->runMethod('edit', ['id' => null, 'parent_id' => $parent_id]);
    }

    public function editAction($id, $parent_id = 0) {
        $model = MenuModel::findOrNew($id);
        if ($model->parent_id < $parent_id) {
            $model->parent_id = $parent_id;
        }
        $menu_list = MenuModel::where('parent_id', 0)->all();
        return $this->show(compact('model', 'menu_list'));
    }

    public function saveAction() {
        $model = new MenuModel();
        $model->wid = $this->weChatId();
        if ($model->load() && $model->setEditor()->autoIsNew()->save()) {
            return $this->jsonSuccess([
                'url' => $this->getUrl('menu')
            ]);
        }
        return $this->jsonFailure($model->getError());
    }

    public function deleteAction($id) {
        MenuModel::where('id', $id)->delete();
        return $this->jsonSuccess([
            'refresh' => true
        ]);
    }

    /**
     * 应用
     * @return \Zodream\Infrastructure\Http\Response
     */
    public function applyAction() {
        $menu_list = MenuModel::with('children')->where('wid', $this->weChatId())->all();
        try {
            WeChatModel::find($this->weChatId())
                ->sdk(Menu::class)
                ->create(array_map(function (MenuModel $menu) {
                    return $menu->toMenu();
                }, $menu_list));
        } catch (\Exception $ex) {
            return $this->jsonFailure($ex->getMessage());
        }
        return $this->jsonSuccess([
            'refresh' => true
        ]);
    }
}