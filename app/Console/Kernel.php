<?php

namespace App\Console;

require 'C:\Users\Dell\example-app\Carbon-2.52.0\autoload.php';

use App\Models\info;
use Carbon\Carbon;
use Carbon\CarbonInterval;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            //$out = new \Symfony\Component\Console\Output\ConsoleOutput();
            //$out->writeln("hi");
            $res=DB::select(DB::raw("SELECT end_hour
            FROM infos
            ORDER BY end_hour ASC
            "));
            //Log::info("array before sorting: " . print_r($info, true));
            //$result = json_decode($info);
            $info=[];
            for($i=0;$i<count($res);$i++)
            {
                $info[$i]=$res[$i]->end_hour;
            }
            $trips_count=ceil(count($info)/5);
            //Log::info("array before sorting: " . print_r($info, true));
            //Log::info("array before sorting: " . $trips_count);
            
            $times=[];
            $trips_s=[];
            for($i=0;$i<$trips_count;$i++)
            {
                $times[$i]= $info[(count($info)/$trips_count*($i+1)-1)];
                $trips_s[count($times)-1]=array();

                for($j=floor((count($info)/$trips_count*($i)));$j<floor((count($info)/$trips_count*($i+1)));$j++)
                    {
                         array_push($trips_s[count($times)-1],$info[$j]);
                    }
            }
            //Log::info("trips: " . print_r($times, true));
            //Log::info("trips_s: " . print_r($trips_s, true));

            $mytime = Carbon::now();

            //Log::info("mytime: " . $mytime);

            $date = Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');

            //Log::info("date: " . $date);

            for($i=0;$i<count($times);$i++)
            {
                 DB::table('trips')->insert([
                    'seats' => '50',
                    'date' => $date,
                    'hour' => $times[$i],
                    'starting_point' => 'جامعة الرشيد',
                    'distination_point' => 'مكتب السويداء',
                    'state' => 'togo',
                    'type' => 'scheduled',
                    'created_at' => $mytime,
                    'updated_at' => $mytime
                  ]);
            }
            info::truncate();

        })->dailyAt('11:00')->days([0,1,2,3,4]);
        //})->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
