<?php

namespace App\EventListener;

use App\Entity\Preference;
use App\Entity\User;
use App\Service\Slugger;
use App\Repository\RoleRepository;
use App\Repository\RankRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserEvent
{
    private $rankRepository;
    private $roleRepository;
    private $slugger;
    private $encoder;

    public function __construct(
        RankRepository $rankRepository,
        RoleRepository $roleRepository,
        Slugger $slugger,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->rankRepository = $rankRepository;
        $this->roleRepository = $roleRepository;
        $this->slugger = $slugger; 
        $this->encoder = $encoder;
    }

    /**
     *? Sets some of the entity's properties automatically once a new user is instantiated.
     * 
     * @param User $user The User entity.
     * 
     * @return void
     */
    public function prePersist(User $user)
    {
        $rank = $this->rankRepository->findOneByRankNumber(1);

        $user->setRank($rank);

        $role = $this->roleRepository->findOneByName('ROLE_CONTRIBUTOR');

        $user->setRole($role);

        $user->setPassword(
            $this->encoder->encodePassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setSlug(
            $this->slugger->slugify($user->getNickname())
        );

        $user
            ->setStatus(1)
            ->setExperience(0)
            ->setAvatar(
                '0' . mt_rand(0, 4) . '_avatar.png'
            )
        ;

        $preference = new Preference();

        $preference
            ->setVolume(50)
            ->setSoundscape('https://ajna-design.fr/wp-content/uploads/2020/03/The-Wander-Loop-Kinomood_-_Bring_Me_Over.mp3')
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;

        $user->setPreference($preference);
    }

    /**
     *? Sets some of the entity's properties automatically once a new user is updated.
     * 
     * @param User $user The User entity.
     * 
     * @return void
     */
    public function preUpdate(User $user)
    {
        $user->setUpdatedAt(new \DateTime());

        $experience = $user->getExperience();

        $rank = [
            1 => 10,
            2 => 25,
            3 => 50,
            4 => 75,
            5 => 100
        ];

        foreach($rank as $key => $value) {
            if ($experience > $value) {
                $rank = $this->rankRepository->findOneByRankNumber($key);
                
                $user->setRank($rank);
            }
        }
    }    
}
