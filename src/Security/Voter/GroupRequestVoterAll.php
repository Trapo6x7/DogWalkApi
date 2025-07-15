<?php

namespace App\Security\Voter;

use App\Entity\GroupRequest;
use App\Entity\User;
use App\Repository\GroupRoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class GroupRequestVoterAll extends Voter
{
    public const READ = 'GROUPREQUESTALL_READ';

    public function __construct(
        private readonly GroupRoleRepository $groupRoleRepository
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Accepter un seul GroupRequest ou un tableau d'entre eux
        return in_array($attribute, [self::READ]) 
            && ($subject instanceof GroupRequest || is_array($subject));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
      
        if (!$user instanceof User) {
            return false;
        }

        if (is_array($subject)) {
            foreach ($subject as $groupRequest) {
                if ($groupRequest instanceof GroupRequest && $this->canRead($groupRequest, $user)) {
                    return true;
                }
            }
            return false; 
        }

        return $this->canRead($subject, $user);
    }

    private function canRead(GroupRequest $groupRequest, User $user): bool
    {
        $group = $groupRequest->getWalkGroup();
   
        if (!$group) {
            return false;
        }

        // Vérifier si l'utilisateur est CREATOR dans ce groupe
        foreach ($group->getGroupRoles() as $groupRole) {
           
            if ($groupRole->getUser() === $user && $groupRole->getRole() === 'CREATOR') {
                return true;
            }
        }

        return false;
    }
}