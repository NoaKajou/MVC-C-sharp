<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/statsController.php';

AuthController::requireLogin();

include 'views/categorie.php';
