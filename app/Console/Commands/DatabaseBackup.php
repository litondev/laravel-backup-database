<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try{
            $filename = "backup-".now()->format("Y-m-d_h:i").".sql";

            $command = "".env('DUMP_PATH').
                " --user='" . env('DB_USERNAME') ."'".
                " --password='" . env('DB_PASSWORD') ."'".
                " --host='" . env('DB_HOST') . "'".
                " " . env('DB_DATABASE') . 
                "  > " . storage_path() .
                "/app/backup/" . $filename;

            $returnVar = NULL;
            $output = NULL;

            // DO COMMAND
            exec($command, $output, $returnVar);

            // SEND EMAIL
            // \Mail::to(env("MAIL_BACKUP_DATABASE"))->send(new \App\Mail\BackupDatabase());

            
            // DELETE PREVIOS BACKUP
            foreach(scandir(storage_path()."/app/backup/") as $item){            
                if(is_file(storage_path()."/app/backup/".$item) && $item != ".gitignore" && $item != $filename){
                    unlink(storage_path()."/app/backup/".$item);
                }
            }

            echo "Backup Success\n";
        }catch(\Exception $e){
            echo "Backup Failed\n";
            \Log::info("Backup Failed : ".$e->getMessage());
        }
    }
}
