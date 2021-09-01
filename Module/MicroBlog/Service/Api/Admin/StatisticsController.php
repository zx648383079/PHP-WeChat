<?php
declare(strict_types=1);
namespace Module\MicroBlog\Service\Api\Admin;

use Module\MicroBlog\Domain\Repositories\StatisticsRepository;

final class StatisticsController extends Controller {

    public function indexAction() {
        return $this->render(StatisticsRepository::subtotal());
    }
}