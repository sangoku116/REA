<?php
use router;

return function (router $router) {
    $router->add('GET', '/', fn() => 'kekw');
};