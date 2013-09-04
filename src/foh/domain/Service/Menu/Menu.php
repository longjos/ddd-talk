<?php
namespace Service\Menu;

class Menu {
    public function getMenuItem($menuItemId){
        return $this->menuCache[$menuItemId];
    }

    public function getMenuForStore(\Model\StoreLocation $store){
        return $this->menuCache;
    }

    private $menuCache = array(
        'f01' => array(
            'description' => "Sandwich",
            'salePrice' => 3.50,
            'category' => 'food'
        ),
        'f02' => array(
            'description' => 'Salad',
            'salePrice' => 4.00,
            'category' => 'food'
        ),
        'a01' => array(
            'description' => 'PBR',
            'salePrice' => 3.25,
            'category' => 'alcohol'
        )
    );
}