<?php

namespace App\Repository;

use App\Model\Donation;
use App\Database;
use PDO;

class DonationRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function addDonation(Donation $donation) {
        $stmt = $this->db->prepare("INSERT INTO donations (donor_name, amount, charity_id, donated_at) VALUES (:donorName, :amount, :charityId, :donated_at)");
        $stmt->execute([
            ':donorName' => $donation->getDonorName(),
            ':amount' => $donation->getAmount(),
            ':charityId' => $donation->getCharityId(),
            ':donated_at'  => date('Y-m-d H:i:s') 
        ]);
    }

    public function getAllDonations() {
        $stmt = $this->db->query("SELECT * FROM donations");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDonationsByCharityId($charityId) {
        $stmt = $this->db->prepare("SELECT * FROM donations WHERE charity_id = :charity_id");
        $stmt->execute([':charity_id' => $charityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}