<?php
declare(strict_types=1);
namespace Module\WeChat\Service\Api\Admin;

use Module\WeChat\Domain\Repositories\QrcodeRepository;
use Zodream\Infrastructure\Contracts\Http\Input;

class QrcodeController extends Controller {


    public function indexAction(string $keywords = '') {
        return $this->renderPage(QrcodeRepository::getList(
            $this->weChatId(),
            $keywords));
    }

    public function detailAction(int $id) {
        try {
            return $this->render(
                QrcodeRepository::get($id)
            );
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
    }

    public function saveAction(Input $input) {
        try {
            $data = $input->validate([
                'id' => 'int',
                'name' => 'required|string:0,255',
                'type' => 'int:0,127',
                'scene_str' => 'string:0,255',
                'expire_time' => 'int',
            ]);
            $data['wid'] = $this->weChatId();
            return $this->render(
                QrcodeRepository::save($this->weChatId(), $data)
            );
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
    }

    public function deleteAction(int $id) {
        try {
            QrcodeRepository::remove($id);
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
        return $this->renderData(true);
    }

}