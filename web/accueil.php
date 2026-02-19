<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';

AuthController::requireLogin();

include 'views/accueil.php';
