<?php

namespace App\Jobs;

use App\Exports\ImportErrorsExport;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Mail;


class ProcessUsersImport implements ShouldQueue
//class ProcessUsersImport
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;
    public $user = null;
    public string $emailTo;

    public function __construct(
        string $path,
        string $emailTo,
        $user
    )
    {
        $this->path = $path;
        $this->emailTo = $emailTo;
        $this->user = $user;
    }

    public function handle()
    {
        // runs the import; adjust storage_path as needed
        $import = new UsersImport($this->user);
        $import->import(storage_path("app/private/{$this->path}"));
//        $fullPath = storage_path("app/private/{$this->path}");

        // sanity check
//        if (! file_exists($fullPath)) {
//            throw new \RuntimeException("File not found at {$fullPath}");
//        }

//        Excel::import(
//            $import,  // passing  User
//            $fullPath,
//        );
//      Excel::import($import, $fullPath);

        // 2) Grab all errors
        $failures = $import->errors;    // validation failures
        $exceptions = $import->exceptions;  // caught exceptions
// 3) If there are any, build an Excel with raw rows + JSON errors
        if (count($failures) || count($exceptions)) {
            $export = new ImportErrorsExport($failures, $exceptions);
            $errorFile = 'import_errors_' . pathinfo($this->path, PATHINFO_FILENAME) . '.xlsx';

            // store into storage/app/import_errors_*.xlsx
            Excel::store($export, $errorFile, 'local');

            $fullPath = storage_path("app/private/{$errorFile}");

            try {

                // 4) Email the user with that attachment
                Mail::raw(
                    'Your spreadsheet import has completed. ' .
                    'Please see the attached file for any rows that failed validation or threw errors.',
                    function ($m) use ($fullPath) {
                    $m->to($this->emailTo)
//                        $m->to('amr.yami1@gmail.com')
                            ->subject('Your import completed with errors')
                            ->attach($fullPath);
                    }
                );

            } catch (\Exception $exception) {
            }
        } else {
            // no errors: optional “success” email, or skip entirely
            try {

                Mail::raw(
                    'Your spreadsheet import completed successfully with no errors!',
                    function ($m) {
                    $m->to($this->emailTo)
//                        $m->to('amr.yami1@gmail.com')
                            ->subject('Import succeeded');
                    }
                );

            } catch (\Exception $exception) {
            }
        }
    }
}
