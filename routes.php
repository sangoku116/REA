<?php
use router;

return function (router $router) {
    $router->add('GET', '/', fn() => 'kekw');
    $router->errorHandler(404, fn() => 'Lolz');
    $router->add('GET', '/reports/view/{reportID}', function () use($router){
        $parameters = $router->current()->parameters();
        return "report is {$parameters['reportID']}";
    });
};