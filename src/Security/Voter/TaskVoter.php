<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @extends Voter<'EDIT'|'DELETE',Task>
 */
class TaskVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    public function __construct(private Security $secuity)
    {
        
    }

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Task;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($this->secuity->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($user, $subject);
            case self::DELETE:
                return $this->canDelete($user, $subject);
        }
    }

    /**
     * Check if a can edit a task.
     * 
     * @param User $user
     * @param Task $task
     * 
     * @return bool
     */
    private function canEdit(User $user, Task $task): bool
    {
        if ($task->getAuthor() === null) {
            return false;
        }

        return $task->getAuthor()->getId() === $user->getId();
    }
    
    /**
     * Check if a can edit a task.
     * 
     * @param User $user
     * @param Task $task
     * 
     * @return bool
     */
    private function canDelete(User $user, Task $task): bool
    {
        return $this->canEdit($user, $task);
    }
}
