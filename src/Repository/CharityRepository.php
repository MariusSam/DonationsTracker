<?php

namespace App\Repository;

use App\Model\Charity;
use App\Database;
use PDO;

class CharityRepository {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getAllCharities() {
        $stmt = $this->db->query("SELECT * FROM charities WHERE deleted_at IS NULL");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new Charity($row['name'], $row['email'], $row['id']), $result);
    }

    public function addCharity(Charity $charity) {
        $stmt = $this->db->prepare("INSERT INTO charities (name, email) VALUES (:name, :email)");
        $stmt->execute([':name' => $charity->getName(), ':email' => $charity->getEmail()]);
    }

    public function updateCharity(Charity $charity) {
        $stmt = $this->db->prepare("UPDATE charities SET name = :name, email = :email WHERE id = :id");
        $stmt->execute([':id' => $charity->getId(), ':name' => $charity->getName(), ':email' => $charity->getEmail()]);
    }

    public function deleteCharity($id) {
        $stmt = $this->db->prepare("UPDATE charities SET deleted_at = ? WHERE id = ?");
        $stmt->execute([date('Y-m-d H:i:s'), $id]);
    }

    public function getPdo() {
        return $this->db;
    }

    public function isCharityActive($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM charities WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
    
}
