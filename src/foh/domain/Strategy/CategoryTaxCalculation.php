<?php
namespace Strategy;

class CategoryTaxCalculation implements ITaxCalculation{

    function __construct(array $rates){
        $this->taxRates = $rates;
    }

    function calculateTax(\Model\WindowOrder $order) {
        $subTotals = array();
        foreach($order->orderItems as $item) {
            $subTotals['base'] += $subTotals['base'] + $item->salePrice;
            if(!is_null($item->itemCategory)){
                $subTotals[$item->itemCategory] += $subTotals[$item->itemCategory] + $item->salePrice;
            }
        }
        $taxAmounts = array();

        foreach($subTotals as $category => $subTotal){
            $taxAmounts[$category] = $subTotal * $this->taxRates[$category];
        }
        return $taxAmounts;
    }
}