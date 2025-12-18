<?php

namespace App\Security\Voter;

use App\Entity\Property;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class PropertyVoter extends Voter
{
    public const VIEW = 'PROPERTY_VIEW';
    public const EDIT = 'PROPERTY_EDIT';
    public const DELETE = 'PROPERTY_DELETE';
    public const MANAGE_RESERVATIONS = 'PROPERTY_MANAGE_RESERVATIONS';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::MANAGE_RESERVATIONS])
            && $subject instanceof Property;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Les propriétés publiées sont visibles par tous
        if ($attribute === self::VIEW && $subject->isPublished()) {
            return true;
        }

        // L'utilisateur doit être connecté pour les autres actions
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Property $property */
        $property = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($property, $user),
            self::EDIT => $this->canEdit($property, $user),
            self::DELETE => $this->canDelete($property, $user),
            self::MANAGE_RESERVATIONS => $this->canManageReservations($property, $user),
            default => false,
        };
    }

    private function canView(Property $property, User $user): bool
    {
        // Le propriétaire peut voir ses propriétés (même non publiées)
        return $property->getOwner() === $user;
    }

    private function canEdit(Property $property, User $user): bool
    {
        // Seul le propriétaire peut modifier sa propriété
        return $property->getOwner() === $user;
    }

    private function canDelete(Property $property, User $user): bool
    {
        // Seul le propriétaire peut supprimer sa propriété
        return $property->getOwner() === $user;
    }

    private function canManageReservations(Property $property, User $user): bool
    {
        // Seul le propriétaire peut gérer les réservations de sa propriété
        return $property->getOwner() === $user;
    }
}
