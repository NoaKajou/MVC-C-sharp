<?php
require_once __DIR__ . '/../models/statsModel.php';


class StatsController {

    private $model;

    public function __construct($db) {
        $this->model = new StatsModel($db);
    }

    public function categories() {
        $stats = $this->model->getStatsCategories();
        require 'views/stats/categories.php';
    }
}
