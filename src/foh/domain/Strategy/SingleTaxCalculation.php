<?php
namespace Strategy;

class SingleTaxCalculation implements ITaxCalculation{
    function __construct($simpleTaxRate){
        $this->taxRate = $simpleTaxRate;
    }
    function calculateTax(\Model\WindowOrder $order) {
        $subTotal = 0.0;
        foreach($order->orderItems as $item) {
            $subTotal = $subTotal + $item->salePrice;
        }
        return array(
            'base' => $subTotal * $this->taxRate
        );
    }
}