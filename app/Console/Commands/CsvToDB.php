<?php

namespace App\Console\Commands;

use App\Logger;
use App\Models\People;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CsvToDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:csv-to-db';

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
        $csv_path = public_path("csv");
        $csv_files = array_diff(scandir($csv_path), ['..', '.']);

        Logger::verbose(count($csv_files) . " file(s) is going to be processed");

        foreach ($csv_files as $csv_file)
        {
            Logger::verbose("Processing $csv_file");

            $file_with_full_path = $csv_path . '/' . $csv_file;

            if (($handle = fopen($file_with_full_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $email = $data[0];
                    $first_name = $data[1];
                    $last_name = $data[2];

                    $people = People::where('work_email', $email)->first();
                    if (!$people) {
                        People::create([
                            'work_email' => $email,
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                        ]);
                        Logger::verbose("CREATED:: New People Stored in DB. ($email)");
                    } else Logger::verbose("DUPLICATE:: $email already exist in DB");
                }
                fclose($handle);
            }
        }
        return ;
    }
}
