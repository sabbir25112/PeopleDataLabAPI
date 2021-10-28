<?php

namespace App\Console\Commands;

use App\Logger;
use App\Models\People;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PullFromPeopleDataLab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:people-data-lab';

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
        $summary = [
            'found' => 0,
            'not_found' => 0,
        ];

        $peoples = People::where('requested', false)->get();

        Logger::verbose(count($peoples) . " people(s) need to sync");
        foreach ($peoples as $people)
        {
            $work_email = $people->work_email;

            Logger::verbose("$work_email is Processing");

            $api = env('PEOPLE_DATA_LAB_ENRICH_URL');

            $request = Http::get($api, [
                'api_key'   => env('PEOPLE_DATA_LAB_API_KEY'),
                'email'     => $work_email,
                'required'  => 'personal_emails'
            ]);

            $response = $request->json();

            $updated_data = [
                'response'  => $response,
                'requested' => true,
            ];

            if ($request->successful()) {
                Logger::verbose("Found Personal Email");
                $updated_data['personal_email'] = implode(',', $response['data']['personal_emails']);
                $summary['found']++;
            } elseif ($request->failed()) {
                Logger::verbose("404 Not Found");
                $summary['not_found']++;
            }

            $people->update($updated_data);

            $rate_limit_left = (int) $request->header('X-RateLimit-Remaining');
            if ($rate_limit_left < 3) {
                Logger::verbose("Rate Limit Threshold Reached. Sleeping For 30 Seconds");
                sleep(30);
            }
        }

        Logger::verbose("Total Found: " . $summary['found']);
        Logger::verbose("Total 404: " . $summary['not_found']);
    }
}
