<?php

namespace App\Console\Commands;

use App\GlobalQueue;
use Illuminate\Console\Command;

class flushGlobalQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'globalq:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all items and reset auto_inc on global_queue table';

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
        $count = GlobalQueue::count();

        $this->info("Flushing $count items from Global Queue");

        GlobalQueue::truncate();
    }
}
