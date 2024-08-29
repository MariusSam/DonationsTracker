<?php 

namespace App\Model;

class Donation {
    private $id;
    private $donorName;
    private $amount;
    private $charityId;
    private $donated_at;

    public function __construct($donorName, $amount, $charityId, $donated_at = null, $id = null) {
        $this->id = $id;
        $this->donorName = $donorName;
        $this->amount = $amount;
        $this->charityId = $charityId;
        $this->donated_at = $donated_at;
    }

    public function getId() {
        return $this->id;
    }

    public function getDonorName() {
        return $this->donorName;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCharityId() {
        return $this->charityId;
    }

    public function getDonatedAt() {
        return $this->donated_at;
    }
}