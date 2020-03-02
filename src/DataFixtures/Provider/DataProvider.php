<?php 

namespace App\DataFixtures\Provider;

class DataProvider {

    private $lorem = 'In de nec et rogati nos in turpes ut enim amicitia Etenim spatio excusatio Etenim.';

    private $ranks = ['astronaut'];

    private $roles = ['ROLE_USER', 'ROLE_ADMIN'];

    private $properties = ['star', 'planet'];

    private $email = ['william@gmail.com', 'shirin@gmail.com', 'arnaud@gmail.com', 'alex@gmail.com'];

    private $nickname = ['william', 'shirin', 'arnaud', 'alex'];

    private $celestialBodies = ['sun', 'earth', 'mars', 'venus', 'uranus'];

    /**
     * Get the value of lorem
     */ 
    public static function getLorem()
    {
        return $this->lorem;
    }

    /**
     * Get the value of ranks
     */ 
    public static function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Get the value of roles
     */ 
    public static function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get the value of properties
     */ 
    public static function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get the value of email
     */ 
    public static function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the value of nickname
     */ 
    public static function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get the value of celestialBodies
     */ 
    public static function getCelestialBodies()
    {
        return $this->celestialBodies;
    }
}