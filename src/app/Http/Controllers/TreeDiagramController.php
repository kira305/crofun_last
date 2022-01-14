<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\DiagramService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Group_MST;
use App\Cost_MST;
use App\Project_MST;
use App\Department_MST;
use App\Headquarters_MST;
use App\Cost;
use App\Diagram;
use Carbon\Carbon;
use Excel;
use Exception;
use Common;
use Auth;
use Crofun;

class TreeDiagramController extends Controller
{
    protected $diagram_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DiagramService $diagram_service)
    {
        $this->diagram_service   = $diagram_service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Crofun::getTimeForDiagram();
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'search_date'        => 'nullable|date_format:Y/m/d',
            ], [
                'search_date.date_format'       => trans('validation.credit_start_time'),
            ]);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $request->request->remove('search_date');
                $errors     = $validator->errors();
                list($diagrams, $listTitleFilter)   = $this->diagram_service->diagramToday(Auth::user()->company_id);
                session(['company_id_d'                     => $request->company_id]);
                session(['headquarter_id_tr'                => $request->headquarter_id]);
                session(['department_id_tr'                 => $request->department_id]);
                session(['group_id_tr'                      => $request->group_id]);
                session(['genka'                            => $request->genka]);
                session(['hanka'                            => $request->hanka]);
                session(['pj_gr_code'                       => $request->pj_gr_code]);
                session(['pj_code'                          => $request->pj_code]);
                return view('diagram.index', ["diagrams" => $diagrams, "listTitleFilter" => $listTitleFilter, "errors" => $errors]);
            }

            $date = $this->diagram_service->changeFormatDate(Carbon::today()->format('Y-m-d'));
            if ($request->search_date != "") {
                $date = $this->diagram_service->changeFormatDate($request->search_date);
            }

            $headquarter_code  = $request->headquarter_id;
            $deparment_code    = $request->department_id;
            $group_code        = $request->group_id;
            $company_id       = $request->company_id;
            $genka            = $request->genka;
            $hanka            = $request->hanka;
            $pj_gr_code       = $request->pj_gr_code;
            $pj_code          = $request->pj_code;
            try {
                list($diagrams, $listTitleFilter) = $this->diagram_service->getDiagramByCondition(
                    $date,
                    $company_id,
                    $headquarter_code,
                    $deparment_code,
                    $group_code,
                    $pj_code,
                    $hanka,
                    $genka,
                    $pj_gr_code
                );
                session(['date_from_rs' => $request->search_date]);
                // }
                // 一旦検索された部分をキャッシュメモリに蓄積する
                session(['date'                             => $date]);
                session(['company_id_d'                     => $request->company_id]);
                session(['headquarter_id_tr'                => $request->headquarter_id]);
                session(['department_id_tr'                 => $request->department_id]);
                session(['group_id_tr'                      => $request->group_id]);
                session(['genka'                            => $request->genka]);
                session(['hanka'                            => $request->hanka]);
                session(['pj_gr_code'                       => $request->pj_gr_code]);
                session(['pj_code'                          => $request->pj_code]);
                return view('diagram.index', ["diagrams" => $diagrams, "listTitleFilter" => $listTitleFilter]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
            return view('diagram.index', ["diagrams" => $diagrams, "listTitleFilter" => $listTitleFilter, 'company_id' => $company_id]);
        }
        // セッションに存在しているﾃﾞｰﾀがある場合は条件で検索する
        if ($this->checkSession() > 0) {
            $search_condition   = $this->searchSession($request);
            list($diagrams, $listTitleFilter)           = $this->diagram_service->getDiagramByCondition(
                $search_condition[0],
                $search_condition[1],
                $search_condition[2],
                $search_condition[3],
                $search_condition[4],
                $search_condition[5],
                $search_condition[6],
                $search_condition[7],
                $search_condition[8]
            );

            return view('diagram.index', ["diagrams" => $diagrams, "listTitleFilter" => $listTitleFilter]);
        }
        list($diagrams, $listTitleFilter) = $this->diagram_service->diagramToday(Auth::user()->company_id);
        return view('diagram.index', ["diagrams" => $diagrams, "listTitleFilter" => $listTitleFilter]);
    }


    public function checkSession()
    {
        if (session('date') != null && session('date') != "") {
            return 1;
        }
        if (session('company_id_d') != null && session('company_id_d') != "") {
            return 1;
        }
        if (session('headquarter_id_tr') != null && session('headquarter_id_tr') != "") {
            return 1;
        }
        if (session('department_id_tr') != null && session('department_id_tr') != "") {
            return 1;
        }
        if (session('group_id_tr') != null && session('group_id_tr') != "") {
            return 1;
        }
        if (session('genka') != null && session('genka') != "") {
            return 1;
        }
        if (session('hanka') != null && session('hanka') != "") {
            return 1;
        }
        if (session('pj_gr_code') != null && session('pj_gr_code') != "") {
            return 1;
        }
        if (session('pj_code') != null && session('pj_code') != "") {
            return 1;
        }

        return  0;
    }
    // セッションに蓄積されたデータを取得
    public function searchSession($request)
    {
        $condition = array();
        if (session('date') != null && session('date') != "") {
            $date = session('date');
            array_push($condition, $date);
        } else {
            $date             = Carbon::today()->format('Y-m-d');
            $date             = $this->diagram_service->changeFormatDate($date);
            array_push($condition, $date);
        }
        if (session('company_id_d') != null && session('company_id_d') != "") {
            $company_id = session('company_id_d');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('headquarter_id_tr') && session('headquarter_id_tr') != null && session('headquarter_id_tr') != "") {
            // $headquarter      = Headquarters_MST::where('id',session('headquarter_id_tr'))->first();
            // $headquarter_code = $headquarter->headquarter_list_code;
            array_push($condition, session('headquarter_id_tr'));
        } else {
            $headquarter_code = "";
            array_push($condition, $headquarter_code);
        }

        if ($request->session()->exists('department_id_tr') && session('department_id_tr') != null && session('department_id_tr') != "") {

            // $department       = Department_MST::where('id',session('department_id_tr'))->first();
            // $department_code  = $department->department_list_code;
            array_push($condition, session('department_id_tr'));
        } else {

            $department_code  = "";
            array_push($condition, $department_code);
        }


        if ($request->session()->exists('group_id_tr')  && session('group_id_tr') != null && session('group_id_tr') != "") {
            // $group            = Group_MST::where('id',session('group_id_tr'))->first();
            // $group_code       = $group->group_list_code;
            array_push($condition, session('group_id_tr'));
        } else {

            $group_code = "";
            array_push($condition, $group_code);
        }

        if ($request->session()->exists('pj_code')) {

            $pj_code = session('pj_code');
            array_push($condition, $pj_code);
        } else {

            $pj_code = "";
            array_push($condition, $pj_code);
        }

        if ($request->session()->exists('hanka')) {

            $hanka = session('hanka');
            array_push($condition, $hanka);
        } else {

            $hanka = "";
            array_push($condition, $hanka);
        }

        if ($request->session()->exists('genka')) {

            $genka = session('genka');
            array_push($condition, $genka);
        } else {

            $genka = "";
            array_push($condition, $genka);
        }



        if ($request->session()->exists('pj_gr_code')) {

            $pj_gr_code = session('pj_gr_code');
            array_push($condition, $pj_gr_code);
        } else {

            $pj_gr_code = "";
            array_push($condition, $pj_gr_code);
        }




        return  $condition;
    }

    // public function diagramFilter(Request $request){


    //          $condition = $request->condition;

    //          try {


    //             $diagrams = $this->diagram_service->getDiagramByCondition($condition);

    //             return view('diagram.index', ["diagrams"=>$diagrams]);

    //          }catch(Exception $e){

    //               throw new Exception($e);

    //          }

    // }

    public  function getListGenka(Request $request)
    {


        $list_cost_1 = array();
        $company_id  = $request->company_id;

        $groups      = Group_MST::where('group_mst.status', true)
            ->get();

        foreach ($groups as $group) {

            if ($group->headquarter()->company_id == $company_id) {

                $cost             = new Cost();
                $cost->cost_code  = $group->cost_code;
                $cost->cost_name  = $group->cost_name;
                $cost->group_id   = $group->id;
                $cost->company_id = $group->headquarter()->company_id;

                array_push($list_cost_1, $cost);
            }
        }

        $costs = Cost_MST::where('company_id', $company_id)
            ->where('type', 1)
            ->get();

        foreach ($costs as $c) {

            $cost             = new Cost();
            $cost->cost_code  = $c->cost_code;
            $cost->cost_name  = $c->cost_name;
            $cost->group_id   = $c->group_id;
            $cost->company_id = $c->company_id;

            array_push($list_cost_1, $cost);
        }

        return response()->json(['list_genka' => $list_cost_1]);
    }

    public function getListHanka(Request $request)
    {

        $list_cost_2 = array();
        $company_id  = $request->company_id;

        $costs = Cost_MST::where('company_id', $company_id)
            ->where('type', 2)
            ->get();

        foreach ($costs as $c) {

            $cost             = new Cost();
            $cost->cost_code  = $c->cost_code;
            $cost->cost_name  = $c->cost_name;
            $cost->group_id   = $c->group_id;
            $cost->company_id = $c->company_id;

            array_push($list_cost_2, $cost);
        }

        return response()->json(['list_hanka' => $list_cost_2]);
    }

    public function getListProject(Request $request)
    {

        $company_id  = $request->company_id;

        $projects    = Project_MST::where('company_id', $company_id)
            ->get();

        return response()->json(['list_project' => $projects]);
    }

    /*
    開始時点から終了時点までのツリー図

    */
    public function diagram(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'start_date'        => 'nullable|date_format:Y/m/d',
                'end_date'        => 'nullable|date_format:Y/m/d',
            ], [
                'start_date.date_format'       => trans('validation.credit_start_time'),
                'end_date.date_format'       => trans('validation.credit_start_time'),
            ]);

            if ($validator->fails()) {
                $errors     = $validator->errors();
                return back()->withErrors($errors);
            }

            $start_date  = $request->start_date;
            $end_date    = $request->end_date;
            if ($request->end_date == null) {

                $end_date = Crofun::getMaxTimeInOr();
                $end_date = $end_date[0]->created_at;
                $time     =  explode(' ', $end_date);
                $end_date = $time[0];
            }

            $company_id     = $request->company_id;
            $start_date     = $this->diagram_service->changeFormatStartDate($start_date);

            $end_date       = $this->diagram_service->changeFormatDate($end_date);
            $file_name      = 'ツリー図_' . Crofun::changeFormatDateyymmdd($start_date) . '~' . Crofun::changeFormatDateyymmdd($end_date);
            /*今日のデータ*/
            $today_diagrams = $this->diagram_service->diagramTodayCSV($company_id)->toArray();
            /*設定されたのデータの間の変更された日付の配列*/
            $time_list      = $this->diagram_service->getListDateBetweenStartAndEnd($company_id, $start_date, $end_date);
            /*ツリー図のデータ作成*/
            $callback       = $this->diagram_service->createDiagram($company_id, $start_date, $end_date, $time_list, $today_diagrams, $file_name);

            /*CSVファイルの作成*/

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $file_name . '.csv',
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        } catch (Exception $e) {

            throw new Exception($e);
        }
        return response()->stream($callback, 200, $headers);
    }
    /*
     検索条件における開始時間と終了時間が同じ場合 過去時点のツリー図

    */
    public function diagram2(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'start_date'        => 'nullable|date_format:Y/m/d',
                'end_date'        => 'nullable|date_format:Y/m/d',
            ], [
                'start_date.date_format'       => trans('validation.credit_start_time'),
                'end_date.date_format'       => trans('validation.credit_start_time'),
            ]);

            if ($validator->fails()) {
                $errors     = $validator->errors();
                return back()->withErrors($errors);
            }

            $company_id       = $request->company_id;
            $start_date       = $this->diagram_service->changeFormatDate($request->start_date);
            $end_date         = $this->diagram_service->changeFormatDate($request->end_date);
            $file_name        = 'ツリー図_' . Crofun::changeFormatDateyymmdd($start_date);
            // 入力された時点までの最新ツリー図
            $diagram_by_time  = $this->diagram_service->diagramByTime($start_date, $company_id)->toArray();

            $callback         = $this->diagram_service->createDiagram2($diagram_by_time);

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=" . $file_name . '.csv',
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
        } catch (Exception $e) {

            throw new Exception($e);
        }
        return response()->stream($callback, 200, $headers);
    }
}
