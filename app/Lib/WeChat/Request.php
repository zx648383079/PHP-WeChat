<?php
namespace App\Lib\WeChat;
/**
 * 处理请求
 */

class Request {
    /**
     * @param 
     * @return array|string
     */
    public static function switchType() {
        $data = array();
        switch (Wechat::getInstance()->get('msgtype')) {
            //事件
            case 'event':
                switch (strtolower(Wechat::getInstance()->get('event'))) {
                    //关注
                    case 'subscribe':
                        //二维码关注
                        if (!empty(Wechat::getInstance()->get('eventkey')) && !empty(Wechat::getInstance()->get('ticket'))){
                            $data = self::eventQrsceneSubscribe();
                        //普通关注
                        }else{
                            $data = self::eventSubscribe();
                        }
                        break;
                    //扫描二维码
                    case 'scan':
                        $data = self::eventScan();
                        break;
                    //地理位置
                    case 'location':
                        $data = self::eventLocation();
                        break;
                    //自定义菜单 - 点击菜单拉取消息时的事件推送
                    case 'click':
                        $data = self::eventClick();
                        break;
                    //自定义菜单 - 点击菜单跳转链接时的事件推送
                    case 'view':
                        $data = self::eventView();
                        break;
                    //自定义菜单 - 扫码推事件的事件推送
                    case 'scancode_push':
                        $data = self::eventScancodePush();
                        break;
                    //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
                    case 'scancode_waitmsg':
                        $data = self::eventScancodeWaitMsg();
                        break;
                    //自定义菜单 - 弹出系统拍照发图的事件推送
                    case 'pic_sysphoto':
                        $data = self::eventPicSysPhoto();
                        break;
                    //自定义菜单 - 弹出拍照或者相册发图的事件推送
                    case 'pic_photo_or_album':
                        $data = self::eventPicPhotoOrAlbum();
                        break;
                    //自定义菜单 - 弹出微信相册发图器的事件推送
                    case 'pic_weixin':
                        $data = self::eventPicWeixin();
                        break;
                    //自定义菜单 - 弹出地理位置选择器的事件推送
                    case 'location_select':
                        $data = self::eventLocationSelect();
                        break;
                    //取消关注
                    case 'unsubscribe':
                        $data = self::eventUnsubscribe();
                        break;
                    //群发接口完成后推送的结果
                    case 'masssendjobfinish':
                        $data = self::eventMassSendJobFinish();
                        break;
                    //模板消息完成后推送的结果
                    case 'templatesendjobfinish':
                        $data = self::eventTemplateSendJobFinish();
                        break;
                    default:
                        return Msg::returnErrMsg(MsgConstant::ERROR_UNKNOW_TYPE, '收到了未知类型的消息');
                        break;
                }
                break;
            //文本
            case 'text':
                $data = self::text();
                break;
            //图像
            case 'image':
                $data = self::image();
                break;
            //语音
            case 'voice':
                $data = self::voice();
                break;
            //视频
            case 'video':
                $data = self::video();
                break;
            //小视频
            case 'shortvideo':
                $data = self::shortvideo();
                break;
            //位置
            case 'location':
                $data = self::location();
                break;
            //链接
            case 'link':
                $data = self::link();
                break;
            default:
                return ResponsePassive::text('收到未知的消息，我不知道怎么处理');
                break;
        }
        return $data;
    }


    /**
     * @descrpition 文本
     * @param 
     * @return array
     */
    public static function text() {
        $content = '收到文本消息';
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 图像
     * @param 
     * @return array
     */
    public static function image() {
        $content = '收到图片';
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 语音
     * @param 
     * @return array
     */
    public static function voice() {
        if (empty(Wechat::getInstance()->get('recognition'))) {
            $content = '收到语音';
        } else {
            $content = '收到语音识别消息，语音识别结果为：'.Wechat::getInstance()->get('recognition');
        }
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 视频
     * @param 
     * @return array
     */
    public static function video() {
        $content = '收到视频';
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 视频
     * @param 
     * @return array
     */
    public static function shortvideo() {
        $content = '收到小视频';
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 地理
     * @param 
     * @return array
     */
    public static function location() {
        $content = '收到上报的地理位置';
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 链接
     * @param 
     * @return array
     */
    public static function link() {
        $content = '收到连接';
        return ResponsePassive::text( $content);
    }

    /**
     * @descrpition 关注
     * @param 
     * @return array
     */
    public static function eventSubscribe() {
        $content = '欢迎您关注我们的微信，将为您竭诚服务';
        return ResponsePassive::text($content);
    }

    /**
     * 取消关注
     * @return array
     */
    public static function eventUnsubscribe() {
        $content = '为什么不理我了？';
        return ResponsePassive::text($content);
    }

    /**
     * 扫描二维码关注（未关注时）
     * @return array
     */
    public static function eventQrsceneSubscribe() {
        $content = '欢迎您关注我们的微信，将为您竭诚服务';
        return ResponsePassive::text($content);
    }

    /**
     * 扫描二维码（已关注时）
     * @param 
     * @return array
     */
    public static function eventScan() {
        $content = '您已经关注了哦～';
        return ResponsePassive::text($content);
    }

    /**
     * 上报地理位置
     * @param 
     * @return array
     */
    public static function eventLocation() {
        $content = '收到上报的地理位置';
        return ResponsePassive::text($content);
    }

    /**
     * 自定义菜单 - 点击菜单拉取消息时的事件推送
     * @param 
     * @return array
     */
    public static function eventClick() {
		//获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content = '收到点击菜单事件，您设置的key是' . $eventKey;
        return ResponsePassive::text($content);
    }

    /**
     * 自定义菜单 - 点击菜单跳转链接时的事件推送
     * @param 
     * @return array
     */
    public static function eventView() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content = '收到跳转链接事件，您设置的key是' . $eventKey;
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 扫码推事件的事件推送
     * @param 
     * @return array
     */
    public static function eventScancodePush() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content = '收到扫码推事件的事件，您设置的key是' . $eventKey;
        $content .= '。扫描信息：'.Wechat::getInstance()->get('scancodeinfo');
        $content .= '。扫描类型(一般是qrcode)：'.Wechat::getInstance()->get('scantype');
        $content .= '。扫描结果(二维码对应的字符串信息)：'.Wechat::getInstance()->get('scanresult');
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
     * @param 
     * @return array
     */
    public static function eventScancodeWaitMsg() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content = '收到扫码推事件且弹出“消息接收中”提示框的事件，您设置的key是' . $eventKey;
        $content .= '。扫描信息：'.Wechat::getInstance()->get('scancodeinfo');
        $content .= '。扫描类型(一般是qrcode)：'.Wechat::getInstance()->get('scantype');
        $content .= '。扫描结果(二维码对应的字符串信息)：'.Wechat::getInstance()->get('scanresult');
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出系统拍照发图的事件推送
     * @param 
     * @return array
     */
    public static function eventPicSysPhoto() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content = '收到弹出系统拍照发图的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.Wechat::getInstance()->get('sendpicsinfo');
        $content .= '。发送的图片数量：'.Wechat::getInstance()->get('count');
        $content .= '。图片列表：'.Wechat::getInstance()->get('piclist');
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.Wechat::getInstance()->get('picmd5sum');
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出拍照或者相册发图的事件推送
     * @param 
     * @return array
     */
    public static function eventPicPhotoOrAlbum() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content  = '收到弹出拍照或者相册发图的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.Wechat::getInstance()->get('sendpicsinfo');
        $content .= '。发送的图片数量：'.Wechat::getInstance()->get('count');
        $content .= '。图片列表：'.Wechat::getInstance()->get('piclist');
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.Wechat::getInstance()->get('picmd5sum');
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出微信相册发图器的事件推送
     * @param 
     * @return array
     */
    public static function eventPicWeixin() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content  = '收到弹出微信相册发图器的事件，您设置的key是' . $eventKey;
        $content .= '。发送的图片信息：'.Wechat::getInstance()->get('sendpicsinfo');
        $content .= '。发送的图片数量：'.Wechat::getInstance()->get('count');
        $content .= '。图片列表：'.Wechat::getInstance()->get('piclist');
        $content .= '。图片的MD5值，开发者若需要，可用于验证接收到图片：'.Wechat::getInstance()->get('picmd5sum');
        return ResponsePassive::text($content);
    }

    /**
     * @descrpition 自定义菜单 - 弹出地理位置选择器的事件推送
     * @param 
     * @return array
     */
    public static function eventLocationSelect() {
        //获取该分类的信息
        $eventKey = Wechat::getInstance()->get('eventkey');
        $content  = '收到点击跳转事件，您设置的key是' . $eventKey;
        $content .= '。发送的位置信息：'.Wechat::getInstance()->get('sendlocationinfo');
        $content .= '。X坐标信息：'.Wechat::getInstance()->get('location_x');
        $content .= '。Y坐标信息：'.Wechat::getInstance()->get('location_y');
        $content .= '。精度(可理解为精度或者比例尺、越精细的话 scale越高)：'.Wechat::getInstance()->get('scale');
        $content .= '。地理位置的字符串信息：'.Wechat::getInstance()->get('label');
        $content .= '。朋友圈POI的名字，可能为空：'.Wechat::getInstance()->get('poiname');
        return ResponsePassive::text($content);
    }

    /**
     * 群发接口完成后推送的结果
     *
     * 本消息有公众号群发助手的微信号“mphelper”推送的消息
     * @param 
     */
    public static function eventMassSendJobFinish() {
        //发送状态，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
        $status      = Wechat::getInstance()->get('status');
        //计划发送的总粉丝数。group_id下粉丝数；或者openid_list中的粉丝数
        $totalCount  = Wechat::getInstance()->get('totalcount');
        //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
        $filterCount = Wechat::getInstance()->get('filtercount');
        //发送成功的粉丝数
        $sentCount   = Wechat::getInstance()->get('sentcount');
        //发送失败的粉丝数
        $errorCount  = Wechat::getInstance()->get('errorcount');
        $content     = '发送完成，状态是'.$status.'。计划发送总粉丝数为'.$totalCount.'。发送成功'.$sentCount.'人，发送失败'.$errorCount.'人。';
        return ResponsePassive::text($content);
    }

    /**
     * 群发接口完成后推送的结果
     *
     * 本消息有公众号群发助手的微信号“mphelper”推送的消息
     * @param 
     */
    public static function eventTemplateSendJobFinish() {
        //发送状态，成功success，用户拒收failed:user block，其他原因发送失败failed: system failed
        $status = Wechat::getInstance()->get('status');
        if ($status == 'success'){
            //发送成功
        } else if ($status == 'failed:user block'){
            //因为用户拒收而发送失败
        } else if ($status == 'failed: system failed'){
            //其他原因发送失败
        }
        return true;
    }
}
