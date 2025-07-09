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

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::READ])
            && $subject instanceof GroupRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var GroupRequest  $subject */
        $group = $subject->getWalkGroup();

        switch ($attribute) {
            case self::READ:
                foreach ($group->getGroupRoles() as $groupRole) {
               
                    if ($groupRole->getUser() == $user && $groupRole->getRole() == 'CREATOR') {
                        return true;
                    }
                }
                break;
        }
        return false;
    }
}
