<?php

namespace App\Http\Controllers;

use App\Exports\ExportEvent;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            if (env('DATABASE_TYPE') == 'csv') {
                $i = 0;
                $array = [];
                if (($open = fopen(public_path('events.csv'), "r")) !== FALSE) {
                    while (($data = fgetcsv($open)) !== FALSE) {
                        $array[$i]['id'] = $data[0];
                        $array[$i]['title'] = $data[1];
                        $array[$i]['start'] = $data[2];
                        $array[$i]['end'] = $data[3];
                        $i++;
                    }
                    fclose($open);
                }
                return response()->json($array);
            }
            $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
            $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
            $data = Event::whereDate('start', '>=', $start)->whereDate('end',   '<=', $end)->get(['id', 'title', 'start', 'end']);
            return response()->json($data);
        }
        return view('event.index');
    }

    public function exportEvents()
    {
        return \Excel::download(new ExportEvent, 'events.csv');
    }
    public function store(Request $request)
    {
        if (env('DATABASE_TYPE') == 'csv') {
            $i = 0;
            $array = [];
            // dd(public_path('events.csv'),$ext);
            if (($open = fopen(public_path('events.csv'), "r")) !== FALSE) {
                while (($data = fgetcsv($open)) !== FALSE) {
                    $array[$i]['id'] = $data[0];
                    $array[$i]['title'] = $data[1];
                    $array[$i]['start'] = $data[2];
                    $array[$i]['end'] = $data[3];
                    $i++;
                    $last_id = !empty($data[0]) ? $data[0] : 0;
                }
                fclose($open);
            }
            $insertArrCsv = [
                $last_id + 1,
                $request->title,
                $request->start,
                $request->end,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),

            ];
            $fp = fopen(public_path('events.csv'), 'a');
            fputcsv($fp, $insertArrCsv);
            fclose($fp);
            return response()->json($array);
        }

        $event = new Event();
        $event->title = $request->title;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->save();
        return response()->json($event);
    }


    public function update(Request $request)
    {
        if (env('DATABASE_TYPE') == 'csv') {
            $table = fopen(public_path('events.csv'), 'r');
            $temp_table = fopen(public_path('temp_events.csv'), 'w');
            $id = $request->id;
            while (($data = fgetcsv($table)) !== FALSE) {
                if ($data[0] == $id) {
                    $insertArrCsv = [
                        $id,
                        $request->title,
                        $request->start,
                        $request->end,
                        $data[4],
                        date('Y-m-d H:i:s'),

                    ];
                    fputcsv($temp_table, $insertArrCsv);
                } else {

                    fputcsv($temp_table, $data);
                }
            }
            fclose($table);
            fclose($temp_table);
            rename(public_path('temp_events.csv'), public_path('events.csv'));
            return response()->json(10);
        }
        $where = array('id' => $request->id);
        $updateArr = ['title' => $request->title, 'start' => $request->start, 'end' => $request->end];
        $event  = Event::where($where)->update($updateArr);

        return response()->json($event);
    }


    public function destroy(Request $request)
    {
        if (env('DATABASE_TYPE') == 'csv') {
            $table = fopen(public_path('events.csv'), 'r');
            $temp_table = fopen(public_path('temp_events.csv'), 'w');
            $id = $request->id; 
            while (($data = fgetcsv($table)) !== FALSE) {
                if (reset($data) == $id) { 
                    continue;
                }
                fputcsv($temp_table, $data);
            }
            fclose($table);
            fclose($temp_table);
            rename(public_path('temp_events.csv'), public_path('events.csv'));

            return  response()->json(10);
        }
        $event = Event::where('id', $request->id)->delete();

        return response()->json($event);
    }
}
