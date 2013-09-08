<?php
namespace Service\Menu;

class Menu {

    public function getMenuItem($menuItemId){
        $menuItem = $this->menuCache[$menuItemId];
        return new MenuItem(
            $menuItemId,
            $menuItem['description'],
            $menuItem['salePrice'],
            $menuItem['category']
        );
    }

    public function getMenuForStore(\Model\StoreLocation $store){
        return $this->menuCache;
    }

    private $menuCache = array(
        'f01' => array(
            'description' => "Sandwich",
            'salePrice' => 4.50,
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

class MenuItem {
    public $itemSKU;
    public $description;
    public $salePrice;
    public $category;

    public function __construct(
        $itemSKU,
        $description,
        $salePrice,
        $category
    ) {
        $this->itemSKU = $itemSKU;
        $this->description = $description;
        $this->salePrice = $salePrice;
        $this->category = $category;
    }
}