<?php

namespace App\Security\Voter;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class ReservationVoter extends Voter
{
    public const VIEW = 'RESERVATION_VIEW';
    public const EDIT = 'RESERVATION_EDIT';
    public const DELETE = 'RESERVATION_DELETE';
    public const CANCEL = 'RESERVATION_CANCEL';
    public const CONFIRM = 'RESERVATION_CONFIRM';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::CANCEL, self::CONFIRM])
            && $subject instanceof Reservation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // L'utilisateur doit être connecté
        if (!$user instanceof User) {
            return false;
        }

        // Les admins ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Reservation $reservation */
        $reservation = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($reservation, $user),
            self::EDIT => $this->canEdit($reservation, $user),
            self::DELETE => $this->canDelete($reservation, $user),
            self::CANCEL => $this->canCancel($reservation, $user),
            self::CONFIRM => $this->canConfirm($reservation, $user),
            default => false,
        };
    }

    private function canView(Reservation $reservation, User $user): bool
    {
        // Le propriétaire de la réservation peut la voir
        if ($reservation->getUser() === $user) {
            return true;
        }

        // Le propriétaire du logement peut voir les réservations
        if ($reservation->getProperty()->getOwner() === $user) {
            return true;
        }

        return false;
    }

    private function canEdit(Reservation $reservation, User $user): bool
    {
        // Seul le propriétaire peut modifier sa réservation en attente
        return $reservation->getUser() === $user && $reservation->isPending();
    }

    private function canDelete(Reservation $reservation, User $user): bool
    {
        // Seul le propriétaire peut supprimer sa réservation annulée ou rejetée
        if ($reservation->getUser() !== $user) {
            return false;
        }

        return in_array($reservation->getStatus(), [
            Reservation::STATUS_CANCELLED,
            Reservation::STATUS_REJECTED
        ]);
    }

    private function canCancel(Reservation $reservation, User $user): bool
    {
        // Le propriétaire peut annuler sa réservation si elle est annulable
        if ($reservation->getUser() === $user && $reservation->canBeCancelled()) {
            return true;
        }

        // Le propriétaire du logement peut aussi annuler
        if ($reservation->getProperty()->getOwner() === $user && $reservation->canBeCancelled()) {
            return true;
        }

        return false;
    }

    private function canConfirm(Reservation $reservation, User $user): bool
    {
        // Seul le propriétaire du logement peut confirmer une réservation en attente
        return $reservation->getProperty()->getOwner() === $user && $reservation->isPending();
    }
}
