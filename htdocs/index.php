<?php
require_once '../vendor/autoload.php';
$f3 = \Base::instance();
$f3->config('../config/config.ini', true);
$f3->config("../config/{$f3->get('HOST')}.ini", true);
$f3->config('../config/routes.ini');
$f3->run();
