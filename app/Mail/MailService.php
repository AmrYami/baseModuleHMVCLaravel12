<?php

namespace App\Mail;

use AllowDynamicProperties;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

#[AllowDynamicProperties] class MailService extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $from = null,
        $subject = null,
        $template = null,
        $data = null,
        bool|int $isMarkDown = false
    )
    {
        $this->fromAddress = $from ?? env('MAIL_FROM_ADDRESS', 'no-reply@example.com');
        $this->fromName = env('MAIL_FROM_NAME', 'No Reply');
        $this->subject = $subject;
        $this->template = $template;
        $this->data = $data;
        $this->isMarkDown = $isMarkDown;
    }

    /**
     * Build the email.
     */
    public function build()
    {
        if($this->isMarkDown){
            return $this->subject($this->subject)
                ->markdown('emails.' . $this->template)
                ->with(['data' => $this->data]);
        }
        return $this->subject($this->subject)
            ->view('emails.' . $this->template)
            ->with(['data' => $this->data]);
    }

    /**
     * Send the email.
     */
    public function sendMail($to, $queue = false)
    {
        try {

            if ($queue) {
                Mail::to($to)->queue($this->build());
            } else {
//                Mail::send($this->build());
                Mail::to([$to])->send($this);
            }

            Log::info('Email sent successfully', ['to' => $this->to, 'subject' => $this->subject]);

            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Throwable $e) {
            Log::error('Email sending failed', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }
}
