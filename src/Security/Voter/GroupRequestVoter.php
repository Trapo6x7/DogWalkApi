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
    public const DELETE = 'GROUPREQUEST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::READ, self::DELETE])
            && $subject instanceof GroupRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var GroupRequest $subject */
        $group = $subject->getWalkGroup();

        switch ($attribute) {
            case self::READ:
                return $this->isUserGroupCreator($user, $group);
            case self::DELETE:
                // Même logique que WalkVoter : seuls les membres du groupe (MEMBER ou CREATOR) peuvent supprimer
                return $this->isUserInGroup($user, $group);
        }
        return false;
    }

    private function isUserGroupCreator($user, $group): bool
    {
        foreach ($group->getGroupRoles() as $groupRole) {
            if ($groupRole->getUser()->getId() == $user->getId() && $groupRole->getRole() == 'CREATOR') {
                return true;
            }
        }
        return false;
    }

    private function isUserInGroup($user, $group): bool
    {
        foreach ($group->getGroupRoles() as $groupRole) {
            if (
                $groupRole->getUser()->getId() == $user->getId() &&
                in_array($groupRole->getRole(), ['MEMBER', 'CREATOR'])
            ) {
                return true;
            }
        }
        return false;
    }
}
