<?php
namespace Model;
class StoreLocation extends \Tracks\Model\AggregateRoot {
    public $zipCode;
    public $storeNumber;

    public function __construct(){
        $this->registerEvents();
    }
    public function openStore($storeNumber, $storeZipCode){
        $guid = \Tracks\Model\Guid::create();
        $this->applyEvent(new EventStoreOpened($guid, $storeNumber, $storeZipCode));
        return $guid;
    }

    protected function registerEvents(){
        $this->registerEvent('Model\EventStoreOpened', 'onEventStoreOpened');
    }

    protected function onEventStoreOpened(EventStoreOpened $event) {
        $this->guid = $event->guid;
        $this->storeNumber = $event->storeNumber;
        $this->zipCode = $event->storeZipCode;
    }
}


class EventStoreOpened extends \Tracks\Event\Base {
    public function __construct($storeGuid, $storeNumber, $storeZipCode){
        parent::__construct($storeGuid);
        $this->storeNumber = $storeNumber;
        $this->storeZipCode = $storeZipCode;
    }
    public $storeNumber;
    public $storeZipCode;
}