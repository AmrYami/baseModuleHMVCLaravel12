<?php

namespace App\Jobs;

use App\Mail\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings
};
use App\Exports\ParamedicsExport as ExportData;
use Users\Models\User;
use Users\Services\UserServiceShow;

class ParamedicsExport implements ShouldQueue
//class ParamedicsExport
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    protected $email;
    protected $filename;
    protected $serviceName;

    public function __construct($request, $email, $serviceName = 'listParamedics')
    {
        $this->email = $email;
        $this->serviceName = $serviceName;
        $this->request = $request;
        $this->filename = 'paramedics_' . now()->format('Ymd_His') . '.xlsx';
//        $users = $serviceShow->listParamedics($request, export: true);
//        $this->users = $users;
    }


    public function handle(UserServiceShow $svc)
    {
        // 1) Fetch your users via the service
        $fakeRequest = \Illuminate\Http\Request::create('/', 'GET', $this->request);
        $users       = $svc->{$this->serviceName}($fakeRequest, export: true);

        // 2) Export them—**synchronously**—to S3
        Excel::store(
            new ExportData($users),
            $this->filename,
            's3',
            \Maatwebsite\Excel\Excel::XLSX
        );

        // 3) Generate a temporary download URL
        $url = Storage::disk('s3')
            ->temporaryUrl($this->filename, now()->addDays(7));

        // 4) Send your mail
        try {
            User::where('email', $this->email)->update(['teamleader' => $url]);
            (new MailService(
                subject:  'Your Paramedics Report Is Ready',
                template: 'download_report',
                data:     ['url' => $url],
            ))->sendMail($this->email, false);

        } catch (\Throwable $e) {
            \Log::error("Paramedics export mail failed: ".$e->getMessage());
        }
    }
}
