<?php
declare(strict_types=1);
namespace Module\Exam\Service\Api;

use Module\Exam\Domain\Repositories\CourseRepository;

class CourseController extends Controller {

    public function indexAction(int $id) {
        try {
            return $this->render(
                CourseRepository::get($id)
            );
        } catch (\Exception $ex) {
            return $this->renderFailure($ex->getMessage());
        }
    }

    public function childrenAction(int $id) {
        return $this->renderData(
            CourseRepository::children($id)
        );
    }

    public function treeAction() {
        return $this->render(
            CourseRepository::all(true)
        );
    }
}