<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DocumentDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete documents from table and server in 2AM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $documents = Document::where('archive', '=', 1)->get();
        foreach($documents as $value) {
            if (file_exists(public_path('assets/' . $value['filePath']))) {
                unlink(public_path('assets/' . $value['filePath']));
            }
            if (file_exists(public_path('assets/' . $value['zipPath']))) {
                unlink(public_path('assets/' . $value['zipPath']));
            }
        }
        Document::where('archive', '=', 1)->delete();
    }
}
