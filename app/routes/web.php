<?php

/** @var \App\Core\Router $router */

$router->get('', 'HomeController@index');
$router->get('home', 'HomeController@index');
$router->any('appointment', 'AppointmentController@index');
$router->post('calendar', 'AppointmentController@calendar');
$router->post('random-staff', 'AppointmentController@randomStaff');
$router->post('lookup-client', 'AppointmentController@lookupClient');
$router->post('contact', 'ContactController@send');
