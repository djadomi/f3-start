<?php
require_once '/www/composer/vendor/autoload.php';
$f3 = \Base::instance();
$f3->config('f3/config/config.ini', true);
$f3->config("f3/config/{$f3->get('HOST')}.ini", true);
$f3->config('f3/config/routes.ini');
$f3->run();
