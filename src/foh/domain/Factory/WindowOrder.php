<?php
namespace Factory;

class WindowOrder {

    protected $taxRateService;

    public function __construct(){
        $this->taxRateService = new \Service\Tax\TaxRate();
    }

    public function makeWindowOrder(\Model\StoreLocation $store){
        return new \Model\WindowOrder(
            $this->taxRateService->getTaxStrategyFor($store)
        );
    }
}