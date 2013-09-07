<?php
namespace Strategy;

class CategoryTaxCalculation implements ITaxCalculation{

    function __construct(array $rates){
        $this->taxRates = $rates;
    }

    function calculateTax(\Model\WindowOrder $order) {
        $subTotals = array('base' => 0.0);
        foreach($order->orderItems as $item) {
            $subTotals['base'] += $subTotals['base'] + $item->salePrice;
            if(!is_null($item->itemCategory)){
                if (!array_key_exists($item->itemCategory, $subTotals)){
                    $subTotals[$item->itemCategory] = 0.0;
                }
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