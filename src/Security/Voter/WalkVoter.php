<?php

namespace App\Security\Voter;

use App\Entity\Group;
use App\Entity\User;
use App\Entity\Walk;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class WalkVoter extends Voter
{
    public const EDIT = 'WALK_EDIT';
    public const VIEW = 'WALK_VIEW';
    public const CREATE = 'WALK_CREATE';
    public const DELETE = 'WALK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Autoriser WALK_CREATE même si $subject est null
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Walk;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // dd($user);
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Gérer le cas spécifique de WALK_CREATE
        if ($attribute === self::CREATE) {
            // Vérifiez si l'utilisateur est membre d'au moins un groupe
            foreach ($user->getGroupRoles() as $groupRole) {
                if ($groupRole->getRole() === 'MEMBER') {
                    return true;
                }
            }
            return false;
        }

        // Récupérer le groupe de la promenade
        $walkGroup = $subject->getWalkGroup();

        if (!$walkGroup) {
            return false;
        }

        // Vérifier si l'utilisateur est membre du groupe
        if (!$this->isUserInGroup($user, $walkGroup)) {
            return false;
        }

        // Handle specific permissions
        switch ($attribute) {
            case self::VIEW:
            case self::CREATE:
                // Members of the group can VIEW and CREATE
                return true;
            case self::EDIT:
            case self::DELETE:
                // Add additional logic for EDIT and DELETE if needed
                return false;
        }

        return false;
    }

    private function isUserInGroup(User $user, Group $group): bool
    {
        // dd($user, $group);
        // Logique pour vérifier si l'utilisateur est membre du groupe
        foreach ($user->getGroupRoles() as $groupRole) {
            if ($groupRole->getWalkGroup() === $group && $groupRole->getRole() === 'MEMBER') {
                return true;
            }
        }

        return false;
    }
}
