<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService extends Mailable
{
    use Queueable, SerializesModels;

    protected ?string $template;

    protected mixed $data;

    protected bool $isMarkDown;

    /**
     * Create a new message instance.
     *
     * Use Laravel's `$this->from(address, name)` so the framework actually
     * sets the From: header (assigning to dynamic $this->fromAddress would
     * not). Pull defaults from config(), not env(), to stay correct after
     * `php artisan config:cache`.
     */
    public function __construct(
        $from = null,
        $subject = null,
        $template = null,
        $data = null,
        bool|int $isMarkDown = false
    ) {
        $fromAddress = $from ?? config('mail.from.address', 'no-reply@example.com');
        $fromName = config('mail.from.name', config('app.name', 'No Reply'));
        $this->from($fromAddress, $fromName);
        $this->subject = $subject;
        $this->template = $template;
        $this->data = $data;
        $this->isMarkDown = (bool) $isMarkDown;
    }

    /**
     * Build the email.
     */
    public function build()
    {
        if ($this->isMarkDown) {
            return $this->subject($this->subject)
                ->markdown('emails.'.$this->template)
                ->with(['data' => $this->data]);
        }

        return $this->subject($this->subject)
            ->view('emails.'.$this->template)
            ->with(['data' => $this->data]);
    }

    /**
     * Send the email.
     */
    public function sendMail($to, $queue = false): bool
    {
        try {
            if ($queue) {
                Mail::to($to)->queue($this);
            } else {
                Mail::to([$to])->send($this);
            }

            Log::info('Email sent successfully', ['to' => $to, 'subject' => $this->subject]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Email sending failed', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
