<?php

namespace App;

class ConsoleInterface {
    public function displayMenu() {
        echo "\n--- Charity Donation Tracker Menu ---\n";
        echo "1. View Charities\n";
        echo "2. Add Charity\n";
        echo "3. Edit Charity\n";
        echo "4. Delete Charity\n";
        echo "5. Add Donation\n";
        echo "6. View All Donations\n";  
        echo "7. View Donations by Charity\n"; 
        echo "8. Import Charities from CSV\n"; 
        echo "0. Exit\n";
        echo "Choose an option: ";
    }

    public function getUserInput() {
        return trim(fgets(STDIN));
    }

    public function displayMessage($message) {
        echo $message . "\n";
    }

    public function promptForCSVFileName() {
        $this->displayMessage("Please enter the CSV file name (e.g., charities.csv). Make sure the file is located in the 'data' folder: ");
        return $this->getUserInput();
    }
    
}
