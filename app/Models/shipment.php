<?php
class Shipment {
    private $num;
    private $date;
    private $deliveryDate;
    private $client;
    private $incident;
    private $vehicle;
    private $path;
    private $insurance;

    // Constructor
    public function __construct($num, $date, $deliveryDate, $client, $incident, $vehicle, $path, $insurance) {
        $this->num = $num;
        $this->date = $date;
        $this->deliveryDate = $deliveryDate;
        $this->client = $client;
        $this->incident = $incident;
        $this->vehicle = $vehicle;
        $this->path = $path;
        $this->insurance = $insurance;
    }

    // Getters
    public function getNum() {
        return $this->num;
    }

    public function getDate() {
        return $this->date;
    }

    public function getDeliveryDate() {
        return $this->deliveryDate;
    }

    public function getClient() {
        return $this->client;
    }

    public function getIncident() {
        return $this->incident;
    }

    public function getVehicle() {
        return $this->vehicle;
    }

    public function getPath() {
        return $this->path;
    }

    public function getInsurance() {
        return $this->insurance;
    }

    // Setters
    public function setNum($num) {
        $this->num = $num;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setDeliveryDate($deliveryDate) {
        $this->deliveryDate = $deliveryDate;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setIncident($incident) {
        $this->incident = $incident;
    }

    public function setVehicle($vehicle) {
        $this->vehicle = $vehicle;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function setInsurance($insurance) {
        $this->insurance = $insurance;
    }
}

