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
$menuService = new \Service\Menu\Menu();

$windowOrderRepo = new \Repository\WindowOrderRepo(
    $store,
    new \Tracks\EventStore\EventStorage\Memory(),
    new \Tracks\EventHandler\DirectRouter(),
    new \Tracks\EventStore\SnapshotStorage\Memory()
);

$orderFactory = new \Factory\WindowOrder();
$windowOrder = $orderFactory->makeWindowOrder($store);
$windowOrderGuid = $windowOrder->openOrder($store);

$firstItemGuid = $windowOrder->addOrderItem($menuService->getMenuItem('f01'));
$windowOrder->orderItems->find($firstItemGuid)->addSpecialInstructions('No Meat');

$windowOrder->addOrderItem($menuService->getMenuItem('f02'));
$windowOrder->addOrderItem($menuService->getMenuItem('a01'));
$windowOrder->placeOrder();
$windowOrder->deliverOrder(5);
$windowOrderRepo->save($windowOrder);

$reconstitutedWindowOrder = $windowOrderRepo->load($windowOrderGuid);

printTicket($reconstitutedWindowOrder);




