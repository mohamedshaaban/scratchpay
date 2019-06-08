<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class CalculatorController extends Controller
{
    public function show(Request $request)
    {
        //Validate Input paramters
        $params =array(
            'initialDate'=>$request->initialDate,
            'delay'=>$request->delay
        );
        $validate = Validator::make($params, [
            'initialDate' => 'required',
            'delay' => 'required|numeric|max:255',
        ]);
        if ($validate->fails()) {
            return ['ok' => false, 'results' => $validate->errors()->all()];
        }
        //List of holidays 
        $holidaysStatic = ['01-01-2019','21-01-2019','18-02-2019','27-05-2019','04-07-2019','02-09-2019','14-10-2019','11-11-2019','28-11-2019','25-12-2019'] ;
        $holidayDays = 0 ;
        $weekendDays = 0 ;
        $workingsDays = 0 ;
        $StaticholidayDays= 0 ;
        //Convert string to datetime and add delay days 
       $intitaldate =  Carbon::parse($request->initialDate);
       
       $endDate = Carbon::parse($request->initialDate)->addDays((int)$request->delay);
       //Getting Weekend days 
       $workingsDays = $intitaldate->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $endDate);
        $weekendDays = $request->delay -  $workingsDays;
       //Dynamicly getting holidays 
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "https://calendarific.com/api/v2/holidays?&api_key=6313270482a0155f0a7fad8c87aaa56bd5e951ac&country=US&year=2019");
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json'));     
        // $output contains the output string
        $output = json_decode(curl_exec($ch));
        // close curl resource to free up system resources
        curl_close($ch);     
        $holidays  = [];
        foreach($output->response->holidays as $holiday)
        {

           if(!in_array($holiday->date->iso, $holidays) && isset($holiday->date->iso) && Carbon::parse($holiday->date->iso)->between(($intitaldate), ($endDate)))
           {
               array_push($holidays, $holiday->date->iso);
              $holidayDays++;
           }
        }
        //End of Dynamically Getting Holidays 

        //Caulcaute number of holidays in predefined
        foreach($holidaysStatic as $holiday)
        {
          if( Carbon::parse($holiday)->between(($intitaldate), ($endDate)))
           {
              $StaticholidayDays++;
           }
            
        }
            
       $results = array(
           'businessDate'=>$params['initialDate'],
           'totalDays'=>$params['delay'],
           //Used in predefined Holidays 
           'holidayDays'=>$StaticholidayDays,
           //Used in getting synamic holidays 
//           'holidayDays'=>$holidayDays,
           'weekendDays'=>$weekendDays,
       );
           return ['ok' => true,'initialQuery' => $params, 'results' => $results];
    }
}
