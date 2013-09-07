<?php
function printTicket(\Model\WindowOrder $order){
    foreach ($order->orderItems as $item) {
        echo $item->itemSku . " | " . $item->name;
        if (count($item->specialInstructions) > 0) {
            array_walk($item->specialInstructions, function($specialInstruction){
                echo PHP_EOL . "      " . $specialInstruction;
            });
        }
        echo "                  $" . number_format($item->salePrice, 2) . PHP_EOL;
    }
    echo PHP_EOL.PHP_EOL."        SubTotal: $" . number_format($order->saleSubTotal, 2) . PHP_EOL;
    echo "           Taxes:";
    foreach ($order->taxSubTotals  as $taxCategory => $taxSub){
        echo PHP_EOL . "                 " . $taxCategory . " - $" . number_format($taxSub, 2);
    }
    echo PHP_EOL . PHP_EOL . "           Total: $" . number_format($order->saleTotal, 2);
}