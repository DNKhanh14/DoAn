<?php

/** @var \App\Core\Router $router */

$router->get('', 'HomeController@index');
$router->get('home', 'HomeController@index');
$router->any('appointment', 'AppointmentController@index');
$router->post('calendar', 'AppointmentController@calendar');
$router->post('contact', 'ContactController@send');
