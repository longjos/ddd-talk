<?php
/**
 * Created by IntelliJ IDEA.
 * User: jlong
 * Date: 9/1/13
 * Time: 2:24 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Model;


use Repository\WindowOrderRepo;

class WindowOrder extends \Tracks\Model\AggregateRoot {

    function __construct(\Strategy\ITaxCalculation $taxStrategy){
        $this->orderItems = new \Tracks\Model\EntityList();
        $this->orderTotal = 0.0;
        $this->preferredCustomer = NULL;
        $this->taxStrategy = $taxStrategy;
        $this->registerEvents();
    }

    function openOrder(\Model\StoreLocation $store){
        $orderGuid = \Tracks\Model\Guid::create();
        $this->applyEvent(
            new EventOrderOpened(
                $orderGuid,
                $store->getGuid(),
                new \DateTime()
            )
        );
        return $orderGuid;
    }

    function addOrderItem(\Service\Menu\MenuItem $menuItem){
        $orderItemGuid = \Tracks\Model\Guid::create();
        $this->applyEvent(new EventItemAdded(
            $this->getGuid(),
            $orderItemGuid,
            $menuItem->itemSKU,
            $menuItem->description,
            $menuItem->category,
            $menuItem->salePrice
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

    function deliverOrder($customerSatisfactionScore){
        $this->applyEvent(
            new EventOrderDelivered(
                $this->getGuid(),
                $customerSatisfactionScore
            )
        );
    }

    private function registerEvents(){
        $this->registerEvent('Model\EventOrderOpened', 'onOrderOpened');
        $this->registerEvent('Model\EventItemAdded', 'onItemAdded');
        $this->registerEvent('Model\EventOrderPlaced', 'onOrderPlaced');
    }

    protected function onOrderOpened(EventOrderOpened $event){
        $this->guid = $event->guid;
        $this->storeGuid = $event->storeGuid;
        $this->orderStatus = WindowOrderStatus::OPEN;
    }

    protected function onOrderPlaced(EventOrderPlaced $event){
        $this->taxSubTotals = $event->taxSubTotals;
        $this->saleSubTotal = $event->subTotal;
        $this->saleTotal = $event->saleTotal;
        $this->orderStatus = WindowOrderStatus::IN_PROGRESS;

    }

    protected function onOrderDelivered(EventOrderDelivered $event){
        $this->orderStatus = WindowOrderStatus::DELIVERED;
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
    public $storeGuid;
    protected $orderTotal;
    protected $taxStrategy;


}

class EventOrderOpened extends \Tracks\Event\Base {
    public function __construct(
        \Tracks\Model\Guid $guid,
        \Tracks\Model\Guid $storeGuid,
        \DateTime $dateTimeOpened
    ) {
        parent::__construct($guid);
        $this->storeGuid = $storeGuid;
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

class EventOrderDelivered extends \Tracks\Event\Base {
    public function __construct(
        \Tracks\Model\Guid $guid,
        $customerSatisfactionScore
    ) {
        parent::__construct($guid);
        $this->customerSatisfactionScore = $customerSatisfactionScore;
    }

    public $customerSatisfactionScore;
}


class WindowOrderStatus {
    const OPEN = 'open';
    const IN_PROGRESS = 'in_progress';
    const DELIVERED = 'delivered';
}