<?php

namespace App\Model;

class Charity {
    private $id;
    private $name;
    private $email;

    public function __construct($name, $email, $id = null) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }
}