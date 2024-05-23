<?php

namespace App\Notifications;

use App\Models\Term;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDigest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $user): MailMessage
    {
        // TODO: add limit
        $terms = $user
            ->terms()
            ->getQuery()
            ->withWhereHas("definitions", function ($query) {
                $query->whereBetween("created_at", [now()->addWeeks(-1), now()]);
            })
            ->orderBy("name", "asc")
            ->get();

        return (new MailMessage())
            ->linesIf(
                $terms->count() > 0,
                $terms->map(function (Term $term) {
                    return "{$term->name}: {$term->definitions->map(
                        fn($def) => $def->text
                    )->join(", ")}";
                })
            )
            ->lineIf($terms->count() === 0, "Nothing")
            ->line("The introduction to the notification.")
            ->action("Notification Action", url("/"))
            ->line($terms->toJson())
            ->line("Thank you for using our application!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
                //
            ];
    }
}
