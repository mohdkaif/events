<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public  function index(Request $request)
    {
        if (env('DATABASE_TYPE') == 'csv') {
            $i = 0;
            $array = [];
            // dd(public_path('events.csv'),$ext);
            if (($open = fopen(public_path('events.csv'), "r")) !== FALSE) {
                while (($data = fgetcsv($open)) !== FALSE) {
                    $array[$i]['id'] = $data[0];
                    $i++;
                }
                fclose($open);
            }
            $data['events_count'] = count($array);
        } else {
            $data['events_count'] = Event::count();
        }
        $data['user_count'] = User::count();

        return view('dashboard', $data);
    }
}
