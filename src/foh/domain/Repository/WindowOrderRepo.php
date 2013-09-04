<?php
namespace Repository;

class WindowOrderRepo extends \Tracks\EventStore\Repository {

    protected $store;

    public function __construct(
        \Model\StoreLocation $store,
        \Tracks\EventStore\IEventStore $eventStore,
        \Tracks\EventHandler\IEventRouter $eventRouter,
        \Tracks\EventStore\ISnapshotStore $snapshotStore
    ) {
        parent::__construct(
            $eventStore,
            $eventRouter,
            $snapshotStore
        );
        $this->store = $store;
    }
    /**
     * Load an entity from it's event history
     *
     * @param Guid   $guid   An Entity's GUID
     * @param Entity $entity That Entity
     *
     * @return \Tracks\Model\Entity
     */
    private function _loadFromHistory(\Tracks\Model\Guid $guid, \Tracks\Model\Entity $entity = null)
    {
        $events = $this->eventStore->getAllEvents($guid);

        if (is_null($entity)) {
            if (is_null($modelClass = $this->eventStore->getType($guid))) {
                return null;
            }
            $windowOrderFactory = new \Factory\WindowOrder();
            $entity = $windowOrderFactory->makeWindowOrder($this->store);
        }

        $entity->loadHistory($events);

        return $entity;
    }
}