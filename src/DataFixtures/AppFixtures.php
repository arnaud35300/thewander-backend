<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\DataProvider;
use App\Entity\CelestialBody;
use App\Entity\Comment;
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
                ->setName($currentRank)
            ;

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
                ->setName($currentRole)
            ;

            $roles[$currentRole] = $role;
            $manager->persist($role);
        }

        //Property
        $properties = array();
        $propertiesData = DataProvider::getProperties();

        foreach ($propertiesData as $currentProperty) {
            $property = new Property();
            $property
                ->setName($currentProperty)
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
            ->setAvatar('https://avatarfiles')
            ->setFirstname('John Doe')
            ->setBio($lorem)
            ->setRank($ranks['astronaut'])
        ;

        $users[] = $admin;
        $manager->persist($admin);

        // random user
        $email = DataProvider::getEmail();
        $nickname = DataProvider::getNickname();

        foreach ($email as $key => $currentEmail) {
            $user = new User();
            $user
                ->setNickname($nickname[$key])
                ->setEmail($currentEmail)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setRole($roles['ROLE_CONTRIBUTOR'])
                ->setAvatar('https://avatarfi')
                ->setFirstname($nickname[$key])
                ->setBio($lorem)
                ->setRole($roles['ROLE_ADMIN'])
                ->setRank($ranks['astronaut'])
            ;

            $users[] = $user;
            $manager->persist($user);
        }

        // Celestial body 
        $celestialBodiesData = DataProvider::getCelestialBodies();
        $celestialBodies = [];
        foreach ($celestialBodiesData as $currentCelestialBody) {
            $celestialBody = new CelestialBody();
            $celestialBody
                ->setName($currentCelestialBody)
                ->setDescription($lorem)
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ->setXPosition(mt_rand(-500, 500))
                ->setYPosition(mt_rand(-500, 500))
                ->addProperty($properties[mt_rand(0, count($properties) - 1)])
            ;

            $celestialBodies[] = $celestialBody; 
            $manager->persist($celestialBody);
        }

        for ($i = 0; $i < 15; $i++) {
            $comment = new Comment();
            $comment
                ->setBody(DataProvider::getLorem())
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ->setCelestialBody($celestialBodies[mt_rand(0, count($celestialBodies) - 1)])
            ;

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
