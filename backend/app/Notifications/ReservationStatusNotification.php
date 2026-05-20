<?php

namespace App\Notifications;

use App\Models\HotelReservation;
use App\Models\RestaurantReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReservationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $event,
        private readonly string $reservationType,
        private readonly string $reservationName,
    ) {
    }

    public static function sendSafely(?object $user, string $event, Model $reservation): void
    {
        if (!$user || !method_exists($user, 'notify')) {
            return;
        }

        try {
            $user->notify(new self(
                $event,
                $reservation instanceof HotelReservation ? 'hotel' : 'restaurant',
                self::reservationName($reservation),
            ));
        } catch (Throwable $exception) {
            Log::warning('Reservation notification failed.', [
                'event' => $event,
                'reservation_type' => $reservation::class,
                'reservation_id' => $reservation->getKey(),
                'user_id' => $user->id ?? null,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $lines] = $this->content();

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Bonjour,');

        foreach ($lines as $line) {
            $message->line($line);
        }

        return $message->line('MaghrebPass');
    }

    private function content(): array
    {
        return match ($this->event) {
            'created' => [
                'Votre demande de réservation a été reçue',
                [
                    "Votre demande de réservation {$this->reservationLabel()} a été reçue et elle est en attente de validation par l’administration.",
                    'Aucun paiement n’est demandé avant l’approbation de votre demande.',
                ],
            ],
            'approved' => [
                'Votre demande de réservation a été approuvée',
                [
                    "Votre demande {$this->reservationLabel()} a été approuvée.",
                    'Veuillez compléter le paiement simulé pour confirmer définitivement votre réservation.',
                ],
            ],
            'rejected' => [
                'Votre demande de réservation a été refusée',
                [
                    "Votre demande de réservation {$this->reservationLabel()} a été refusée.",
                    'Aucun paiement n’a été effectué.',
                ],
            ],
            'paid' => [
                'Paiement confirmé',
                [
                    'Votre paiement simulé a été confirmé.',
                    'Votre réservation est maintenant confirmée.',
                ],
            ],
            default => [
                'Mise à jour de votre réservation',
                ["Votre réservation {$this->reservationLabel()} a été mise à jour."],
            ],
        };
    }

    private function reservationLabel(): string
    {
        return trim("{$this->reservationType} {$this->reservationName}");
    }

    private static function reservationName(Model $reservation): string
    {
        if ($reservation instanceof HotelReservation) {
            return $reservation->hotel?->name ? "({$reservation->hotel->name})" : '';
        }

        if ($reservation instanceof RestaurantReservation) {
            return $reservation->restaurant?->name ? "({$reservation->restaurant->name})" : '';
        }

        return '';
    }
}
