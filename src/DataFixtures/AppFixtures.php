<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\DataProvider;
use App\Entity\CelestialBody;
use App\Entity\Property;
use App\Entity\Rank;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $lorem = DataProvider::getLorem();
        
        // Rank 
        $ranks = array();
        $ranksData = DataProvider::getRanks();
        
        foreach ($ranksData as $currentRank) {
            $rank = new Rank();
            $rank
                ->setName($currentRank);

            // store ranks
            $ranks[$currentRank] = $rank;
            $manager->persist($rank);
        }

        // Role 
        $roles = array();
        $rolesData = DataProvider::getRoles();
        
        foreach ($rolesData as $currentRole) {
            $role = new Role();
            $role
                ->setName($currentRole);;

            $roles[$currentRole] = $role;
            $manager->persist($role);
        }

        // Property
        $properties = array();
        $propertiesData = DataProvider::getProperties();
        
        foreach ($propertiesData as $currentProperty) {
            $property = new Property();
            $property
                ->setName($currentProperty)
                ->setUnit(12000)
                ->setValue('kg')
            ;

            $properties[] = $property;
            $manager->persist($property);
        }

        // User 
        $users = array();

        // admin
        $admin = new User();
        $admin
            ->setNickname('admin')
            ->setEmail('admin@admin.fr')
            ->setPassword($this->encoder->encodePassword($admin, 'password'))
            ->setRole($roles['ROLE_ADMIN'])
            ->setAvatar('https://avatarfiles.alphacoders.com/124/thumb-124726.jpg')
            ->setFirstname('John Doe')
            ->setBio($lorem)
            ->setRank($ranks['astronaut'])
        ;

        $users[] = $admin;
        
        // random user
        $email = DataProvider::getEmail();
        $nickname = DataProvider::getNickname();
        
        foreach ($email as $key => $currentEmail) {
            $user = new User();
            $user
                ->setNickname($nickname[$key])
                ->setEmail($currentEmail)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setRole($roles['ROLE_USER'])
                ->setAvatar('https://avatarfiles.alphacoders.com/124/thumb-124726.jpg')
                ->setFirstname($nickname[$key])
                ->setBio($lorem)
                ->setRank($ranks['astronaut'])
            ;

            $users[] = $user;
            $manager->persist($user);
        }

        // Celestial body 
        $celestialBodiesData = DataProvider::getCelestialBodies();

        foreach($celestialBodiesData as $currentCelestialBody) {
            $celestialBody = new CelestialBody();
            $celestialBody
                        ->setName($currentCelestialBody)
                        ->setDescription($lorem)
                        ->setUser($users[mt_rand(0, count($users) - 1)])
                        ->setXPosition(mt_rand(-500,500))
                        ->setYPosition(mt_rand(-500,500))
                        ->addProperty($properties[mt_rand(0, count($properties) - 1)])
            ;
        }   

        $manager->flush();
    }
}
