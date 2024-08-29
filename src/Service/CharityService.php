<?php

namespace App\Service;

use App\Repository\CharityRepository;
use App\Model\Charity;

class CharityService {
    private $charityRepository;

    public function __construct(CharityRepository $charityRepository) {
        $this->charityRepository = $charityRepository;
    }

    public function getAllCharities() {
        return $this->charityRepository->getAllCharities();
    }

    public function addCharity($name, $email) {
        if (!$this->isValidEmail($email)) {
            return "Invalid email format.";
        }

        $charity = new Charity($name, $email);
        $this->charityRepository->addCharity($charity);

        return null; // No error, operation successful
    }

    public function updateCharity($id, $name, $email) {
        if (!$this->isValidEmail($email)) {
            return "Invalid email format.";
        }

        if (!$this->isCharityActive($id)) {
            return "Charity with ID $id does not exist or has been deleted.";
        }

        $charity = new Charity($name, $email, $id);
        $this->charityRepository->updateCharity($charity);
        return null;
    }

    public function deleteCharity($id) {
        if (!$this->isCharityActive($id)) {
            return "Charity with ID $id does not exist or has been deleted.";
        }

        $this->charityRepository->deleteCharity($id);
        return null;
    }
    
    public function isCharityActive($id) {
        return $this->charityRepository->isCharityActive($id);
    }

    private function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function importCharitiesFromCSV($filePath) {
        // Check if the file exists before trying to open it
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }
    
        // Open the CSV file for reading
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            // Skip the header row if there is one (optional)
            fgetcsv($handle, 1000, ',');
    
            $importedCount = 0;
            $skippedCount = 0;
    
            // Process each row of the CSV file
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Check if the CSV row has the expected number of columns (e.g., 2 for name and email)
                if (count($data) < 2) {
                    continue; // Skip if the row doesn't have enough data
                }
    
                $name = trim($data[0]);
                $email = trim($data[1]);
    
                // Skip empty names or emails
                if (empty($name) || empty($email)) {
                    continue;
                }
    
                // Check if the charity already exists in the database
                if ($this->isCharityExists($name, $email)) {
                    $skippedCount++;
                    continue; // Skip this record if it already exists
                }
    
                // Add charity to the database
                $this->addCharity($name, $email);
                $importedCount++;
            }
    
            fclose($handle);
    
            // Return a summary of the import process
            return [
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];
        } else {
            throw new \Exception("Could not open the CSV file.");
        }
    }

    private function isCharityExists($name, $email) {
        $stmt = $this->charityRepository->getPDO()->prepare("SELECT COUNT(*) FROM charities WHERE name = :name AND email = :email AND deleted_at IS NULL");
        $stmt->execute([':name' => $name, ':email' => $email]);
    
        return $stmt->fetchColumn() > 0;
    }
}