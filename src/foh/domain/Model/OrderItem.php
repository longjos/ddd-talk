<?php
namespace Model;
class OrderItem extends \Tracks\Model\Entity {
    public $itemName;
    public $itemSku;
    public $salePrice;
    public $specialInstructions = array();
    public $itemCategory;

    function __construct(
        $orderItemGuid,
        $itemSku,
        $itemName,
        $salePrice,
        $itemCategory = NULL
    ) {
        $this->guid = $orderItemGuid;
        $this->itemSku = $itemSku;
        $this->name = $itemName;
        $this->salePrice = $salePrice;
        $this->itemCategory = $itemCategory;
        $this->registerEvents();
    }

    function addSpecialInstructions($instruction){
        $this->applyEvent(
            new EventSpecialInstructionsAdded(
                $this->getGuid(),
                $instruction
            )
        );
    }

    protected function onSpecialInstructionsAdded(EventSpecialInstructionsAdded $event){
        array_push($this->specialInstructions, $event->instruction);
    }

    private function registerEvents()
    {
        $this->registerEvent('Model\EventSpecialInstructionsAdded', 'onSpecialInstructionsAdded');
    }
}

class EventSpecialInstructionsAdded extends \Tracks\Event\Base {
    public function __construct($guid, $instruction){
        parent::__construct($guid);
        $this->instruction = $instruction;
    }

    public $instruction;
}