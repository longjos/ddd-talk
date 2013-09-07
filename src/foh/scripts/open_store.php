<?php
require("bootstrap.php");

$repo = new \Tracks\EventStore\Repository(
    new \Tracks\EventStore\EventStorage\Memory(),
    new \Tracks\EventHandler\DirectRouter(),
    new \Tracks\EventStore\SnapshotStorage\Memory()
);

$store = new \Model\StoreLocation();
$storeGuid  = $store->openStore(1234, '37206');
$repo->save($store);

$windowOrderRepo = new \Repository\WindowOrderRepo(
    $store,
    new \Tracks\EventStore\EventStorage\Memory(),
    new \Tracks\EventHandler\DirectRouter(),
    new \Tracks\EventStore\SnapshotStorage\Memory()
);

$orderFactory = new \Factory\WindowOrder();
$windowOrder = $orderFactory->makeWindowOrder($store);

$windowOrderGuid = $windowOrder->openOrder($store);

$firstItemGuid = $windowOrder->addItem('f01');
$windowOrder->orderItems->find($firstItemGuid)->addSpecialInstructions('No Meat');

$windowOrder->addItem('f02');
$windowOrder->addItem('a01');
$windowOrder->placeOrder();
$windowOrderRepo->save($windowOrder);

$loadedOrder = $windowOrderRepo->load($windowOrderGuid);

printTicket($loadedOrder);




