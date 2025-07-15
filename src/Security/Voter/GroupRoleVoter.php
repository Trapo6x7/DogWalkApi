<?php

namespace App\Security\Voter;

use App\Entity\GroupRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class GroupRoleVoter extends Voter{
    public const EDIT = 'GROUPROLE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT])
            && $subject instanceof \App\Entity\GroupRole;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var GroupRole $subject */
        $group = $subject->getWalkGroup();

        switch ($attribute) {
            case self::EDIT:
                foreach($group->getGroupRoles() as $groupRole){
                    if ($groupRole->getUser() === $user && $groupRole->getRole() === 'CREATOR'){
                        return true;
                    }
                }
                break;
        }
        return false;
    }
}
