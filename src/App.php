<?php

namespace App;

use App\Service\CharityService;
use App\Service\DonationService;
use App\Repository\CharityRepository;
use App\Repository\DonationRepository;
use App\Database;
use App\ConsoleInterface;

class App {
    private $charityService;
    private $donationService;
    private $cli;

    public function __construct() {
        $db = new Database('data/database.db');
        $this->charityService = new CharityService(new CharityRepository($db));
        $this->donationService = new DonationService(new DonationRepository($db), $this->charityService);
        $this->cli = new ConsoleInterface();
    }

    public function run() {
        while (true) {
            $this->cli->displayMenu();
            $choice = $this->cli->getUserInput();

            switch ($choice) {
                case '1':
                    $this->viewCharities();
                    break;
                case '2':
                    $this->addCharity();
                    break;
                case '3':
                    $this->editCharity();
                    break;
                case '4':
                    $this->deleteCharity();
                    break;
                case '5':
                    $this->addDonation();
                    break;
                case '6':
                    $this->getAllDonations();
                    break;
                case '7':
                    $this->viewDonationsByCharity();
                    break;
                case '8':
                    $this->importCharitiesFromCSV();
                    break;
                case '0':
                    $this->cli->displayMessage("Exiting application...");
                    exit;
                default:
                    $this->cli->displayMessage("Invalid choice. Please try again.");
                    break;
            }
        }
    }


    private function viewCharities() {
        try {
            $charities = $this->charityService->getAllCharities();

            if ($charities) {
                foreach ($charities as $charity) {
                    $this->cli->displayMessage("ID: {$charity->getId()} | Name: {$charity->getName()} | Email: {$charity->getEmail()}");
                }
            } else {
                $this->cli->displayMessage("No charities found.");
            }
        } catch (\PDOException $e) {
            $this->cli->displayMessage("Error: Database not initialized or missing table(s). Please ensure the database is properly set up.");
        }
       
    }

    private function addCharity() {
        $this->cli->displayMessage("Enter charity name: ");
        $name = $this->cli->getUserInput();
        $this->cli->displayMessage("Enter email: ");
        $email = $this->cli->getUserInput();

        // Add charity and handle potential errors
        $error = $this->charityService->addCharity($name, $email);
        if ($error) {
            $this->cli->displayMessage("Error: $error");
        } else {
            $this->cli->displayMessage("Charity added successfully.");
        }
    }

    private function editCharity() {
        $this->cli->displayMessage("Enter charity ID to edit: ");
        $id = $this->cli->getUserInput();

        // Validate if charity exists and is active before proceeding
        if (!$this->charityService->isCharityActive($id)) {
            $this->cli->displayMessage("Charity with ID $id does not exist or has been deleted.");
            return; // Exit the method if validation fails
        }

        $this->cli->displayMessage("Enter new charity name: ");
        $name = $this->cli->getUserInput();
        $this->cli->displayMessage("Enter new email: ");
        $email = $this->cli->getUserInput();

        // Update charity and handle potential errors
        $error = $this->charityService->updateCharity($id, $name, $email);
        if ($error) {
            $this->cli->displayMessage("Error: $error");
        } else {
            $this->cli->displayMessage("Charity updated successfully.");
        }
    }

    private function deleteCharity() {
        $this->cli->displayMessage("Enter charity ID to delete: ");
        $id = $this->cli->getUserInput();

        // Delete charity and handle potential errors
        $error = $this->charityService->deleteCharity($id);
        if ($error) {
            $this->cli->displayMessage("Error: $error");
        } else {
            $this->cli->displayMessage("Charity deleted successfully.");
        }
    }

    private function addDonation() {
        $this->cli->displayMessage("Enter donor name: ");
        $donorName = $this->cli->getUserInput();
        $this->cli->displayMessage("Enter donation amount: ");
        $amount = $this->cli->getUserInput();
        $this->cli->displayMessage("Enter charity ID: ");
        $charityId = $this->cli->getUserInput();

        // Add donation and handle potential errors
        $error = $this->donationService->addDonation($donorName, $amount, $charityId);
        if ($error) {
            $this->cli->displayMessage("Error: $error");
        } else {
            $this->cli->displayMessage("Donation added successfully.");
        }
    }

    private function getAllDonations() {
        $donations = $this->donationService->getAllDonations();

        if ($donations) {
            foreach ($donations as $donation) {
                $this->cli->displayMessage("ID: {$donation['id']} | Donor: {$donation['donor_name']} | Amount: {$donation['amount']} | Charity ID: {$donation['charity_id']} | Date: {$donation['donated_at']}");
            }
        } else {
            $this->cli->displayMessage("No donations found.");
        }
    }

    private function viewDonationsByCharity() {
        $this->cli->displayMessage("Enter charity ID: ");
        $charityId = $this->cli->getUserInput();

        $donations = $this->donationService->viewDonationsByCharity($charityId);
        
        if ($donations) {
            foreach ($donations as $donation) {
                $this->cli->displayMessage("ID: {$donation['id']} | Donor: {$donation['donor_name']} | Amount: {$donation['amount']} | Date: {$donation['donated_at']}");
            }
        } else {
            $this->cli->displayMessage("No donations found for charity ID $charityId.");
        
        }
    }

    private function importCharitiesFromCSV() {
        $this->cli->displayMessage("Enter the CSV file name (should be in the 'data' folder): ");
        $fileName = $this->cli->getUserInput();
        $filePath = "data/$fileName";
    
        try {
            // Call the import method in CharityService
            $result = $this->charityService->importCharitiesFromCSV($filePath);
    
            // Display the results 
            $this->cli->displayMessage("{$result['imported']} imported, {$result['skipped']} skipped.");
        } catch (\Exception $e) {
            // Display any errors that occurred during the import process
            $this->cli->displayMessage("Error during import: " . $e->getMessage());
        }
    }
}


