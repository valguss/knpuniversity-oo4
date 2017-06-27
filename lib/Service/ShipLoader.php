<?php
namespace Service;

use Model\BountyHunterShip;
use Model\ShipCollection;

class ShipLoader
{
    private $shipStorage;

    public function __construct(ShipStorageInterface $shipStorage)
    {
        $this->shipStorage = $shipStorage;
    }

    /**
     * @return ShipCollection
     */
    public function getShips()
    {
        $ships = array();

        $shipsData = $this->queryForShips();

        foreach ($shipsData as $shipData) {
            $ships[] = $this->createShipFromData($shipData);
        }
	    $ships[] = new BountyHunterShip('Slave I');

	    return new ShipCollection($ships);
    }

    /**
     * @param $id
     * @return \Model\AbstractShip
     */
    public function findOneById($id)
    {
        $shipArray = $this->shipStorage->fetchSingleShipData($id);

        return $this->createShipFromData($shipArray);
    }

	/**
	 * @param array $shipData
	 *
	 * @return \Model\AbstractShip
	 */
    private function createShipFromData(array $shipData)
    {
        if ($shipData['team'] == 'rebel') {
            $ship = new \Model\RebelShip($shipData['name']);
        } else {
            $ship = new \Model\Ship($shipData['name']);
            $ship->setJediFactor($shipData['jedi_factor']);
        }

        $ship->setId($shipData['id']);
        $ship->setWeaponPower($shipData['weapon_power']);
        $ship->setStrength($shipData['strength']);

        return $ship;
    }

    private function queryForShips()
    {
	    try {
		    return $this->shipStorage->fetchAllShipsData();
	    } catch (\PDOException $e) {
		    trigger_error('Database Exception! '.$e->getMessage());
		    return [];
        }
    }
}

