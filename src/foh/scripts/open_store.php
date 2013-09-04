<?php
require("../../vendor/autoload.php");
date_default_timezone_set('America/Chicago');
function fohAutoLoader($className)
{
    $file = dirname(__DIR__)
        .DIRECTORY_SEPARATOR
        .'domain'
        .DIRECTORY_SEPARATOR
        .str_replace('\\', DIRECTORY_SEPARATOR, $className)
        .'.php';
    if (file_exists($file)) {
        include $file;
    }
}

spl_autoload_register('fohAutoLoader');

/* $repo = new \Tracks\EventStore\Repository(
    new \Tracks\EventStore\EventStorage\ZendDb2(
        new \Zend\Db\Adapter\Adapter(
            array(
                'driver' => 'Pdo_Mysql',
                'database' => 'ddd',
                'username' => 'ddd',
                'password' => 'ddd',
                'hostname' => '192.168.33.14'
            )
        )
    ),
    new \Tracks\EventHandler\DirectRouter(),
    new \Tracks\EventStore\SnapshotStorage\Memory()
);

*/
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
$windowOrder->openOrder();

$firstItemGuid = $windowOrder->addItem('f01');
$windowOrder->orderItems->find($firstItemGuid)->addSpecialInstructions('No Meat');

$windowOrder->addItem('f02');
$windowOrder->addItem('a01');
$windowOrder->placeOrder();
$windowOrderRepo->save($windowOrder);
var_dump($windowOrder);
var_dump($windowOrder->taxSubTotals);





