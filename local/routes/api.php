<?php

use Bitrix\Main\Routing\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {

    $routes->prefix('api/v1/appointment')->group(function (RoutingConfigurator $routes) {
        $routes->get('services', [\Norbit\Appointment\Controller\ServicesController::class, 'get']);
        $routes->get('branches', [\Norbit\Appointment\Controller\BranchesController::class, 'get']);
        $routes->get('specialists', [\Norbit\Appointment\Controller\SpecialistsController::class, 'get']);
        $routes->get('slots', [\Norbit\Appointment\Controller\SlotsController::class, 'get']);
    });
    $routes->post('/api/v1/appointment', [\Norbit\Appointment\Controller\AppointmentController::class, 'add']);

};