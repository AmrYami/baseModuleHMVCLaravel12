<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Mail\MailService;
use Illuminate\Support\Facades\Mail;

class ImportUserMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fromId;
    protected $toId;
    protected $companyId;
    public $timeout = 6000;

    /**
     * Create a new job instance.
     */
    public function __construct($fromId = null, $toId = null, $companyId = null)
    {
        $this->fromId = $fromId;
        $this->toId = $toId;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $disk = 's3';
        $emails = [];

        $query = User::query();
        if (isset($this->companyId) && $this->companyId) {
            $query = $query->where('company_id', $this->companyId);
        }else{
            $query->whereDoesntHave('media');
        }
        if (isset($this->fromId) && isset($this->toId)) {
            $query->whereBetween('id', [$this->fromId, $this->toId]);
        } elseif (isset($this->fromId)) {
            $query->where('id', '>=', $this->fromId);
        } elseif (isset($this->toId)) {
            $query->where('id', '<=', $this->toId);
        }
        $query->chunkById(100, function ($users) use (&$emails, $disk) {

            foreach ($users as $user) {
                $test = 0;
                $avatar = 0;
                $multi = 0;
                $national = $user->national_id;

                // Avatar
                $avatarKey = collect(Storage::disk($disk)->files("media/{$national}/avatar"))->first();
                if ($avatarKey) {
                    $user
                        ->clearMediaCollection('avatar')
                        ->addMediaFromDisk($avatarKey, $disk)
                        ->usingName(pathinfo($avatarKey, PATHINFO_FILENAME))
                        ->usingFileName(basename($avatarKey))
                        ->toMediaCollection('avatar', $disk);

                    $test = 1;
                    $avatar = 1;
                }

                // Certificates
                $certKeys = Storage::disk($disk)->files("media/{$national}/certificates");
                foreach ($certKeys as $key) {
                    $user
                        ->addMediaFromDisk($key, $disk)
                        ->usingName(pathinfo($key, PATHINFO_FILENAME))
                        ->usingFileName(basename($key))
                        ->toMediaCollection('multi', $disk);

                    $test = 1;
                    $multi = 1;
                }

                // fix update syntax: ['upload' => $test]
                $user->update(['upload' => $test]);

                $emails[] = "{$user->email} avatar={$avatar} multi={$multi} national_id={$national}";
            }
        });

        // send summary email
        try {
            $mailService = new MailService(
                subject: 'Media import report',
                template: 'list_email',
                data: ['emails' => $emails],
            );
            $mailService->sendMail('amr.yami1@gmail.com', false);

        } catch (\Exception $exception) {
            \Log::error("ImportUserMediaJob mail error: " . $exception->getMessage());
        }
    }
}
