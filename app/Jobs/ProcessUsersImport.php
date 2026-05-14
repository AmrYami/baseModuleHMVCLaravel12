<?php

namespace App\Jobs;

use App\Exports\ImportErrorsExport;
use App\Imports\UsersImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUsersImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;

    public $user = null;

    public string $emailTo;

    public function __construct(
        string $path,
        string $emailTo,
        $user
    ) {
        $this->path = $path;
        $this->emailTo = $emailTo;
        $this->user = $user;
    }

    public function handle()
    {
        // runs the import; adjust storage_path as needed
        $import = new UsersImport($this->user);

        // Resolve the import file under storage/app/private with a strict
        // path-traversal guard. realpath() collapses ../ segments so we
        // can verify the result is still under the expected base directory.
        $basePath = storage_path('app/private');
        $candidate = $basePath.DIRECTORY_SEPARATOR.$this->path;
        $fullPath = realpath($candidate);
        if ($fullPath === false || ! str_starts_with($fullPath, $basePath.DIRECTORY_SEPARATOR)) {
            throw new \RuntimeException('Invalid import path.');
        }
        $import->import($fullPath);

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
            $errorFile = 'import_errors_'.pathinfo($this->path, PATHINFO_FILENAME).'.xlsx';

            // Store under storage/app/private/ so the path matches $fullPath below.
            $relative = "private/{$errorFile}";
            Excel::store($export, $relative, 'local');

            $fullPath = storage_path("app/{$relative}");

            try {

                // 4) Email the user with that attachment
                Mail::raw(
                    'Your spreadsheet import has completed. '.
                    'Please see the attached file for any rows that failed validation or threw errors.',
                    function ($m) use ($fullPath) {
                        $m->to($this->emailTo)
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
                            ->subject('Import succeeded');
                    }
                );

            } catch (\Exception $exception) {
            }
        }
    }
}
