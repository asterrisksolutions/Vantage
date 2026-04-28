<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password Reset Notification
 *
 * Sent when a user requests a password reset.
 * Contains a secure, time-limited reset link.
 */
class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    protected string $token;

    /**
     * The number of minutes the token is valid for.
     */
    protected int $expiresIn;

    /**
     * Create a new notification instance.
     *
     * @param string $token The password reset token
     * @param int $expiresIn Number of minutes until token expires
     */
    public function __construct(string $token, int $expiresIn = 15)
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable The user receiving the notification
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $email = $notifiable->email;
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ], false));

        return (new MailMessage)
            ->subject('Password Reset Request - VANTAGE')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset your password.')
            ->line('Click the button below to reset your password:')
            ->action('Reset Password', $resetUrl)
            ->line('This link will expire in ' . $this->expiresIn . ' minutes.')
            ->line('If you did not request a password reset, no action is required.')
            ->line('If you are having trouble clicking the button, copy and paste the URL below into your browser:')
            ->line($resetUrl)
            ->salutation('Regards, VANTAGE Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'expires_in' => $this->expiresIn,
        ];
    }
}