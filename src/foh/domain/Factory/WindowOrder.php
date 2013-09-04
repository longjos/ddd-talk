<?php
namespace Factory;

class WindowOrder {

    protected $accountingService;

    public function __construct(){
        $this->accountingService = new \Service\Tax\TaxRate();
    }

    public function makeWindowOrder(\Model\StoreLocation $store){
        return new \Model\WindowOrder(
            $this->accountingService->getTaxStrategyFor($store),
            new \Service\Menu\Menu($store)
        );
    }
}