<?php
/**
 * Created by IntelliJ IDEA.
 * User: jlong
 * Date: 9/1/13
 * Time: 2:24 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Model;


class WindowOrder extends \Tracks\Model\AggregateRoot {

    function __construct(\Strategy\ITaxCalculation $taxStrategy, \Service\Menu\Menu $menuService){
        $this->menu = $menuService;
        $this->orderItems = new \Tracks\Model\EntityList();
        $this->orderTotal = 0.0;
        $this->preferredCustomer = NULL;
        $this->taxStrategy = $taxStrategy;
        $this->registerEvents();
    }

    function openOrder(){
        $orderGuid = \Tracks\Model\Guid::create();
        $this->applyEvent(
            new EventOrderOpened(
                $orderGuid,
                new \DateTime()
            )
        );
        return $orderGuid;
    }

    function addItem($itemId){
        $menuItemDTO = $this->menu->getMenuItem($itemId);
        $orderItemGuid = \Tracks\Model\Guid::create();
        $this->applyEvent(new EventItemAdded(
            $this->getGuid(),
            $orderItemGuid,
            $itemId,
            $menuItemDTO['description'],
            $menuItemDTO['category'],
            $menuItemDTO['salePrice']
        ));
        return $orderItemGuid;
    }

    function placeOrder(){
        $subTotal = 0.0;
        foreach ($this->orderItems as $orderItem){
            $subTotal += $orderItem->salePrice;
        }

        $taxSubTotals = $this->taxStrategy->calculateTax($this);
        $saleTotal = $subTotal + array_reduce(
                $taxSubTotals,
                function ($sum, $tax) {
                    return $sum + $tax;
                },
                0.0
            );
        $this->applyEvent(
            new EventOrderPlaced(
                $this->getGuid(),
                $subTotal,
                $taxSubTotals,
                $saleTotal
            )
        );
    }

    function cancelOrder(){

    }
    function rejectOrder(){

    }
    function deliverOrder(){

    }

    private function registerEvents(){
        $this->registerEvent('Model\EventOrderOpened', 'onOrderOpened');
        $this->registerEvent('Model\EventItemAdded', 'onItemAdded');
        $this->registerEvent('Model\EventOrderPlaced', 'onOrderPlaced');
    }

    protected function onOrderOpened(EventOrderOpened $event){
        $this->guid = $event->guid;
    }

    protected function onOrderPlaced(EventOrderPlaced $event){
        $this->taxSubTotals = $event->taxSubTotals;
        $this->saleSubTotal = $event->subTotal;
        $this->saleTotal = $event->saleTotal;
    }

    protected function onItemAdded(EventItemAdded $event){
        $this->orderItems->add(
            new \Model\OrderItem(
                $event->orderItemGuid,
                $event->itemSku,
                $event->itemName,
                $event->salePrice,
                $event->itemCategory
            )
        );
    }


    public $orderItems;
    public $orderStatus;
    public $preferredCustomer;
    public $taxSubTotals;
    public $saleSubTotal;
    public $saleTotal;
    protected $orderTotal;
    protected $taxStrategy;

}

class EventOrderOpened extends \Tracks\Event\Base {
    public function __construct(
        \Tracks\Model\Guid $guid,
        \DateTime $dateTimeOpened
    ) {
        parent::__construct($guid);
        $this->dateTimeOpened = $dateTimeOpened;
    }

    public $dateTimeOpened;
}


class EventItemAdded extends \Tracks\Event\Base {
    public function __construct(
        \Tracks\Model\Guid $guid,
        \Tracks\Model\Guid $orderItemGuid,
        $itemSku,
        $itemName,
        $itemCategory,
        $salePrice
    ) {
        parent::__construct($guid);
        $this->orderItemGuid = $orderItemGuid;
        $this->itemSku = $itemSku;
        $this->itemName = $itemName;
        $this->itemCategory = $itemCategory;
        $this->salePrice = $salePrice;
    }
    public $orderItemGuid;
    public $itemSku;
    public $itemName;
    public $itemCategory;
    public $salePrice;
}


class EventOrderPlaced extends \Tracks\Event\Base {
    public function __construct(
        \Tracks\Model\Guid $guid,
        $subTotal,
        $taxSubTotals,
        $saleTotal
    ) {
        parent::__construct($guid);
        $this->subTotal = $subTotal;
        $this->taxSubTotals = $taxSubTotals;
        $this->saleTotal = $saleTotal;
    }

    public $taxSubTotals;
    public $saleTotal;
    public $subTotal;

}