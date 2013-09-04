<?php
$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/hello/', function () use ($app) {
    $g =  \Tracks\Model\Guid::create();
    $order = new \Model\Order();
    echo $g;
});

$app->run();
