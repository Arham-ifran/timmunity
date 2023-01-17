<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTelescope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ClearTelescope';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ClearTelescope';

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
     * @return mixed
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('telescope_monitoring')->truncate();
        DB::table('telescope_entries_tags')->truncate();
        DB::table('telescope_entries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
