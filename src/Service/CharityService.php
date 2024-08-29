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
            return "Invalid email format."; // Return error if email is invalid
        }

        $charity = new Charity($name, $email);
        $this->charityRepository->addCharity($charity);

        return null; // Return null if no error
    }

    public function updateCharity($id, $name, $email) {
        if (!$this->isValidEmail($email)) {
            return "Invalid email format."; // Return error if email is invalid
        }

        if (!$this->isCharityActive($id)) {
            return "Charity with ID $id does not exist or has been deleted."; // Return error if charity does not exist
        }

        $charity = new Charity($name, $email, $id);
        $this->charityRepository->updateCharity($charity);
        return null; // Return null if no error
    }

    public function deleteCharity($id) {
        if (!$this->isCharityActive($id)) {
            return "Charity with ID $id does not exist or has been deleted."; // Return error if charity does not exist
        }

        $this->charityRepository->deleteCharity($id);
        return null; // Return null if no error
    }
    
    // Check if a charity is active by its ID
    public function isCharityActive($id) {
        return $this->charityRepository->isCharityActive($id);
    }

    // Validate if the provided email format is correct
    private function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Check if a charity already exists in the database
    public function isCharityExists($name, $email) {
        return $this->charityRepository->isCharityExists($name, $email);
    }

    public function importCharitiesFromCSV($filePath) {
        // Check if the file exists before trying to open it
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }
    
        $handle = null;
        try {
            // Open the CSV file for reading
            if (($handle = fopen($filePath, 'r')) === FALSE) {
                throw new \Exception("Could not open the CSV file.");
            }
    
            // Skip the header row if there is one
            fgetcsv($handle, 1000, ',');
    
            $importedCount = 0;
            $skippedCount = 0;
    
            // Process each row of the CSV file
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Check if the CSV row has the expected number of columns
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
    
            // Return a summary of the import process
            return [
                'imported' => $importedCount,
                'skipped' => $skippedCount,
            ];
    
        } catch (\Exception $e) {
            // Handle exceptions 
            throw new \Exception("Error processing CSV file: " . $e->getMessage());
        } finally {
            // Ensure the file handle is closed even if an error occurs
            if ($handle !== null) {
                fclose($handle);
            }
        }
    }

    
}