<?php

namespace App\Jobs;

use ZipArchive;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ZipDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $name;
    public $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $name, $filePath)
    {
        $this->name = $name;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $zip = new ZipArchive;
        $zipResponse = $zip->open(public_path('assets/' . $this->name . '.zip'), ZipArchive::CREATE);

        if (!!$zipResponse) {
            $zip->addFile(public_path('assets/' . $this->filePath), $this->filePath);
            $zip->close();
        }
    }
}
