<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Slugger;
use App\Repository\RoleRepository;
use App\Repository\RankRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;
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

    public function prePersist(User $user, LifecycleEventArgs $event)
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

        $user->setStatus(1);

        $birthday = '1990-00-00';
        $birthday = \DateTime::createFromFormat('Y-m-d', $birthday);
        $user->setBirthday($birthday);
    }

    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
        $user->setPassword(
            $this->encoder->encodePassword(
                $user,
                $user->getPassword()
            )
        );

        $user->setSlug(
            $this->slugger->slugify($user->getNickname())
        );

        $user->setUpdatedAt(new \DateTime());
    }    
}
