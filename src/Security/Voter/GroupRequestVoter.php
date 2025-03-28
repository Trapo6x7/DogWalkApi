<?php

namespace App\Security\Voter;

use App\Entity\GroupRequest;
use App\Repository\GroupRoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class GroupRequestVoter extends Voter
{
    public const READ = 'GROUPREQUEST_READ';
    // private readonly GroupRoleRepository $groupRoleRepository;

    // public function __construct(GroupRoleRepository $groupRoleRepository)
    // {
    //     $this->groupRoleRepository = $groupRoleRepository;
    // }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::READ])
            && $subject instanceof GroupRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var GroupRequest  $subject */
        $group = $subject->getWalkGroup();

        // $walkGroupRole = $this->groupRoleRepository->findGroupRoleByGroupAndUser($group, $user)->getWalkGroup();

        // if ($group !== $walkGroupRole){
        //     return false;
        // }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::READ:
                foreach ($group->getGroupRoles() as $groupRole) {
                    // dd($groupRole);
                    if ($groupRole->getUser() == $user && $groupRole->getRole() == 'CREATOR') {
                        return true;
                    }
                }
                break;
        }
        return false;
    }
}
