<?php
namespace Module\Disk\Service\Api;

use Exception;
use Module\Disk\Domain\Repositories\DiskRepository;
use Zodream\Http\Uri;
use Zodream\Infrastructure\Contracts\Http\Output;

class HomeController extends Controller {

    public function indexAction($id, $path = '') {
        return $this->renderPage(DiskRepository::driver()->catalog($id, $path));
    }

    /**
     * 进行网址访问许可
     * @param $url
     * @return Output
     * @throws Exception
     */
    public function allowAction($url) {
        if (empty($url)) {
            return $this->renderFailure('网址必传');
        }
        $token = md5(sprintf('%s-%s-%s', is_array($url) ? implode('-', $url) : $url, time(), auth()->id()));
        cache()->store('disk')->set($token, auth()->id(), 3600);
        $cb = function ($url) use ($token) {
            if (empty($url)) {
                return '';
            }
            $uri = new Uri($url);
            $uri->addData('token', $token);
            return (string)$uri;
        };
        if (!is_array($url)) {
            return $this->renderData($cb($url));
        }
        return $this->renderData(array_map($cb, $url));
    }
}