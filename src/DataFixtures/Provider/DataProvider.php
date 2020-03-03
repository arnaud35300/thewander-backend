<?php 

namespace App\DataFixtures\Provider;

class DataProvider {
    const LOREM = 'In de nec et rogati nos in turpes ut enim amicitia Etenim spatio excusatio Etenim.';
    const RANKS = ['astronaut'];
    const ROLES = ['ROLE_USER', 'ROLE_ADMIN'];
    const PROPERTIES = ['star', 'planet'];
    const EMAIL = ['william@gmail.com', 'shirin@gmail.com', 'arnaud@gmail.com', 'alex@gmail.com'];
    const NICKNAME = ['william', 'shirin', 'arnaud', 'alex'];
    const CELESTIAL_BODIES = ['sun', 'earth', 'mars', 'venus', 'uranus'];

    /**
     *? Retrieves the value of lorem.
     */ 
    public static function getLorem()
    {
        return self::LOREM;
    }

    /**
     *? Retrieves the value of ranks.
     */ 
    public static function getRanks()
    {
        return self::RANKS;
    }

    /**
     *? Retrieves the value of roles.
     */ 
    public static function getRoles()
    {
        return self::ROLES;
    }

    /**
     *? Retrieves the value of properties.
     */ 
    public static function getProperties()
    {
        return self::PROPERTIES;
    }

    /**
     *? Retrieves the value of email.
     */ 
    public static function getEmail()
    {
        return self::EMAIL;
    }

    /**
     *? Retrieves the value of nickname.
     */ 
    public static function getNickname()
    {
        return self::NICKNAME;
    }

    /**
     *? Retrieves the value of celestialBodies.
     */ 
    public static function getCelestialBodies()
    {
        return self::CELESTIAL_BODIES;
    }
}