<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TASK_EDIT'])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $userInt = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$userInt instanceof UserInterface) {
            return false;
        }
        /** @var User $user */
        $user = $userInt;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TASK_EDIT':
                return $this->canEdit($user, $subject);
        }
        return false;
    }

    private function canEdit(User $user, Task $task)
    {
        if ($user === $task->getAuthor() || $task->getAuthor()->getUserIdentifier() === 'Anonyme' && $this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }
}
