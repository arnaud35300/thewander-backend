<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\RankRepository;
use App\Repository\RoleRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserEvent
{
    private $rankRepository;
    private $roleRepository;

    public function __construct(RankRepository $rankRepository, RoleRepository $roleRepository)
    {
        $this->rankRepository = $rankRepository;
        $this->roleRepository = $roleRepository;
    }

    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $rank = $this->rankRepository->findOneByName('neophyte');
        $user->setRank($rank);

        $role = $this->roleRepository->findOneByName('ROLE_CONTRIBUTOR');
        $user->setRole($role);
    }
}
