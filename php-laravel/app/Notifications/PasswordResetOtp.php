<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password Reset OTP Notification
 *
 * Sent when a user requests a password reset using OTP method.
 * Contains a 6-digit OTP code for verification.
 */
class PasswordResetOtp extends Notification
{
    use Queueable;

    /**
     * The OTP code.
     */
    protected string $otp;

    /**
     * The number of minutes the OTP is valid for.
     */
    protected int $expiresIn;

    /**
     * Create a new notification instance.
     *
     * @param string $otp The 6-digit OTP code
     * @param int $expiresIn Number of minutes until OTP expires
     */
    public function __construct(string $otp, int $expiresIn = 15)
    {
        $this->otp = $otp;
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
        return (new MailMessage)
            ->subject('Your Password Reset OTP - VANTAGE')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset your password.')
            ->line('Your One-Time Password (OTP) is:')
            ->line('<strong style="font-size: 24px; letter-spacing: 5px;">' . $this->otp . '</strong>')
            ->line('This OTP will expire in ' . $this->expiresIn . ' minutes.')
            ->line('Enter this OTP on the password reset page to create a new password.')
            ->line('If you did not request a password reset, please ignore this email or contact support if you have concerns.')
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
            'otp' => $this->otp,
            'expires_in' => $this->expiresIn,
        ];
    }
}