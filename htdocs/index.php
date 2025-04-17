<?php
require_once '../vendor/autoload.php';
$f3 = \Base::instance();
$f3->config('../config/config.ini', true);
Multilang::instance();
$f3->run();
