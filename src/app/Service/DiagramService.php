<?php

namespace App\Service;

use App\Common\Common;
use App\Diagram;
use Carbon\Carbon;
use Auth;
use DB;

class DiagramService
{
    public function changeFormatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';
    }

    public function changeFormatStartDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d') . ' 00:00:01';
    }
    public function changeFormatToday()
    {
        $day = Carbon::today();
        $day = $day->subDay(1)->format('Y-m-d');
        return $day . ' 23:59:59';
    }

    public function getToday()
    {
        $day = Carbon::today();
        $day = $day->format('Y-m-d');
        return $day . ' 23:59:59';
    }
    // 今日までのツリー図 created_atを取得 group by tree id
    public function getListTreeIdOnToday_tree_id($company_id)
    {
        $date     = $this->getToday();
        $tree_id  = DB::select("select tree_id, max(created_at) as created_at from organization_history where company_id = '" . $company_id . "' and created_at <= '" . $date . "' and tree_id IS NOT NULL and flag = true and tree_id NOT IN (select tree_id from organization_history where created_at <= '" . $date . "' and flag = false and tree_id IS NOT NULL and created_at in (select max(created_at) as created_at from organization_history where created_at <= '" . $date . "' group by tree_id)) group by tree_id ORDER BY tree_id ASC");

        return $tree_id;
    }

    // 今日までのツリー図 created_atを取得 group by pj id
    public function getListTreeIdOnToday_pj_id($company_id)
    {
        $date   = $this->changeFormatToday();
        $pj_id  = DB::select("select pj_id, max(created_at) as created_at from organization_history where company_id = '" . $company_id . "' and created_at <= '" . $date . "' and pj_id IS NOT NULL and flag = true and pj_id NOT IN (select pj_id from organization_history where created_at <= '" . $date . "' and flag = false and pj_id IS NOT NULL and created_at in (select max(created_at) as created_at from organization_history where created_at <= '" . $date . "' group by pj_id)) group by pj_id ORDER BY pj_id ASC");
        return $pj_id;
    }


    // pj_idで取得されたcreated_atとtree_idで取得されたcreated_atを合併
    public function concatListCreateAt($company_id)
    {
        $list_time    = array();
        $list_time_1 = $this->getListTreeIdOnToday_tree_id($company_id);
        $list_time_2 = $this->getListTreeIdOnToday_pj_id($company_id);
        foreach ($list_time_1 as $l) {
            array_push($list_time, $l);
        }

        foreach ($list_time_2 as $l) {
            array_push($list_time, $l);
        }

        return $list_time;
    }
    // 今日までのツリー図
    public function diagramToday($company_id)
    {
        $list_id_data = $this->concatListCreateAt($company_id);
        $diagrams     = Diagram::where('company_id', $company_id)
            ->whereIn('created_at', array_column($list_id_data, 'created_at'))
            ->orderBy('headquarters_code', 'ASC')
            ->orderBy('department_code', 'ASC')
            ->orderBy('group_code', 'ASC')
            ->orderBy('cost_code', 'ASC')
            ->orderBy('sales_management_code', 'ASC')
            ->orderBy('project_code', 'ASC');

        $diagramsArray = $diagrams->get()->toArray();
        $listTitleFilter = $this->getListTitleFilter($diagramsArray);
        return array($diagrams->paginate(30), $listTitleFilter);
    }

    public function diagramTodayCSV($company_id)
    {
        $list_id_data = $this->concatListCreateAt($company_id);
        $diagrams     = Diagram::where('company_id', $company_id)
            ->whereIn('created_at', array_column($list_id_data, 'created_at'))
            ->orderBy('headquarters_code', 'ASC')
            ->orderBy('department_code', 'ASC')
            ->orderBy('group_code', 'ASC')
            ->orderBy('cost_code', 'ASC')
            ->orderBy('sales_management_code', 'ASC')
            ->orderBy('project_code', 'ASC')
            ->orderBy('id', 'DESC')
            ->get();

        return $diagrams;
    }

    // --------------------------------------------------------------------------------------------------------------------
    // 日にちで検索

    // pj_idがある場合 $dateまでの最新created_at
    public function getListTreeIdByTime_1($date)
    {
        $pj_id  = DB::select("select pj_id, max(created_at) as created_at from organization_history where created_at <= '" . $date . "' and pj_id IS NOT NULL and flag = true and pj_id NOT IN (select pj_id from organization_history where created_at <= '" . $date . "' and flag = false and pj_id IS NOT NULL and created_at in (select max(created_at) as created_at from organization_history where created_at <= '" . $date . "' group by pj_id)) group by pj_id ORDER BY pj_id ASC");
        return $pj_id;
    }
    // tree_idがある場合　$dateまでの最新created_at
    public function getListTreeIdByTime_2($date)
    {
        $tree_id  = DB::select("select tree_id, max(created_at) as created_at from organization_history where created_at <= '" . $date . "' and tree_id IS NOT NULL and flag = true and tree_id NOT IN (select tree_id from organization_history where created_at <= '" . $date . "' and flag = false and tree_id IS NOT NULL and created_at in (select max(created_at) as created_at from organization_history where created_at <= '" . $date . "' group by tree_id)) group by tree_id ORDER BY tree_id ASC");
        return $tree_id;
    }
    // pj_idとtree_idのデータ組み合わせて
    public function getListTreeIdByTime($date)
    {
        $list_id    = array();
        $list_id_1 = $this->getListTreeIdByTime_1($date);
        $list_id_2 = $this->getListTreeIdByTime_2($date);

        foreach ($list_id_1 as $l) {
            array_push($list_id, $l);
        }

        foreach ($list_id_2 as $l) {
            array_push($list_id, $l);
        }

        return $list_id;
    }
    // 検索
    public function getDiagramByCondition($search_date, $company_id, $headquarters_code, $department_code, $group_code, $project_code, $sales_management_code, $cost_code, $project_grp_code)
    {

        $list_id_data     = $this->getListTreeIdByTime($search_date);
        $diagrams     = Diagram::whereIn('created_at', array_column($list_id_data, 'created_at'));
        if ($company_id != "") {
            $diagrams  = $diagrams->where('company_id', $company_id);
        }

        if ($headquarters_code != null && $headquarters_code != "") {
            $diagrams  = $diagrams->where('headquarters_code', $headquarters_code);
        }

        if ($department_code != null && $department_code != "") {
            $diagrams  = $diagrams->where('department_code', $department_code);
        }

        if ($group_code != null ) {
            $diagrams  = $diagrams->where('group_code', $group_code);
        }

        if ($project_code != "" && $project_code != null) {
            $diagrams  = $diagrams->where('project_code', $project_code);
        }

        if ($cost_code != "" && $cost_code != null) {
            $diagrams  = $diagrams->where('cost_code', $cost_code);
        }

        if ($sales_management_code != "" && $sales_management_code != null) {
            $diagrams  = $diagrams->where('sales_management_code', $sales_management_code);
        }

        if ($project_grp_code != "" && $project_grp_code != null) {
            $diagrams  = $diagrams->where('project_grp_code', $project_grp_code);
        }
        $diagrams  = $diagrams->orderBy('headquarters_code', 'ASC')
            ->orderBy('department_code', 'ASC')
            ->orderBy('group_code', 'ASC')
            ->orderBy('cost_code', 'ASC')
            ->orderBy('sales_management_code', 'ASC')
            ->orderBy('project_code', 'ASC');
            // ->paginate(30);
        $diagramsArray = $diagrams->get()->toArray();
        $listTitleFilter = $this->getListTitleFilter($diagramsArray);
        return array($diagrams->paginate(30), $listTitleFilter);
    }

    private function getListTitleFilter($diagramsArray){
        $Result['headquarter_id'] = $this->getArrayColumn($diagramsArray,'headquarters','headquarters_code');
        $Result['department_id'] = $this->getArrayColumn($diagramsArray,'department_name','department_code');
        $Result['group_id'] = $this->getArrayColumn($diagramsArray,'group_name','group_code');
        $Result['hanka'] = $this->getArrayColumn($diagramsArray,'sales_management','sales_management_code');
        $Result['genka'] = $this->getArrayColumn($diagramsArray,'cost_name','cost_code');
        $Result['pj_gr_code'] = $this->getArrayColumn($diagramsArray,'project_grp_name','project_grp_code');
        $Result['pj_code'] = $this->getArrayColumn($diagramsArray,'project_name','project_code');

        return $Result;
    }

    private function getArrayColumn($diagramsArray, $columnName, $columnId){
        $result =  array_unique(array_column($diagramsArray, $columnName, $columnId));
        $result = array_filter($result, function($value) { return !is_null($value) && $value != '' ; });
        return $result;
    }

    // CSV
    //csv抽出する際に時間ごとにcreated_atを取得
    public function diagramByTime($date, $company_id)
    {
        $list_id_data = $this->getListTreeIdByTime($date);
        $diagrams     = Diagram::where('company_id', $company_id)
            ->whereIn('created_at', array_column($list_id_data, 'created_at'))
            ->orderBy('headquarters_code', 'ASC')
            ->orderBy('department_code', 'ASC')
            ->orderBy('group_code', 'ASC')
            ->orderBy('cost_code', 'ASC')
            ->orderBy('sales_management_code', 'ASC')
            ->get();

        return $diagrams;
    }
    // 時間で検索
    public function getListDateBetweenStartAndEnd($company_id, $start_date, $end_date)
    {
        $list_day        = array();
        $list_day_return = array();
        $time_list  = Diagram::where('company_id', $company_id)
            ->whereBetween('created_at', [$start_date, $end_date])
            ->orderBy('created_at', 'ASC')
            ->orderBy('headquarters_code', 'ASC')
            ->orderBy('department_code', 'ASC')
            ->orderBy('group_code', 'ASC')
            ->orderBy('cost_code', 'ASC')
            ->orderBy('sales_management_code', 'ASC')
            ->get();

        foreach ($time_list as $time) {
            $day = Carbon::parse($time->created_at)->format('Y-m-d');
            if (!in_array($day, $list_day)) {
                array_push($list_day, $day);
                array_push($list_day_return, $this->changeFormatDate($day));
            }
        }
        return $list_day_return;
    }
    // csv作成
    public function createDiagram($company_id, $start_date, $end_date, $time_list, $today_diagrams, $file_name)
    {
        $list_diagram   = array();
        foreach ($time_list as $time) { // $time_list　は　$start_time から
            $diagrams       = $this->diagramByTime($time, $company_id)->toArray();
            $size1          = sizeof($today_diagrams);
            $size2          = sizeof($diagrams);
            $diagram_by_time = array();
            if ($size1 > $size2) {
                $today_list = array();
                $time_list  = array();
                $tree_id    = array();
                $pj_id      = array();
                for ($i = 0; $i < $size1; $i++) {
                    if ($i < $size2) {
                        foreach ($today_diagrams as $diagram) {
                            if ($diagrams[$i]['tree_id'] != null) {
                                array_push($tree_id, $diagrams[$i]['tree_id']);
                            }
                            if ($diagrams[$i]['pj_id'] != null) {
                                array_push($pj_id, $diagrams[$i]['pj_id']);
                            }
                            if ($result = $this->compareListTree($diagram, $diagrams, $i)) {
                                array_push($diagram_by_time, $result[0]);
                                array_push($today_list, $result[1]);
                                array_push($time_list, $result[2]);
                            }
                        }
                    } else {

                        if (in_array($today_diagrams[$i]['pj_id'], $pj_id) || in_array($today_diagrams[$i]['tree_id'], $tree_id)) {
                            if ($item = $this->compare($diagrams, $today_diagrams[$i])) {
                                array_push($diagram_by_time, $item);
                                array_push($today_list, $today_diagrams[$i]['id']);
                                array_push($time_list,  $today_diagrams[$i]['id']);
                            }
                        }

                        if ($result = $this->concatTreeListToday($today_diagrams, $today_list, $i)) {
                            if ($today_diagrams[$i]['tree_id'] != null) {
                                array_push($tree_id, $today_diagrams[$i]['tree_id']);
                            }
                            if ($today_diagrams[$i]['pj_id'] != null) {
                                array_push($pj_id, $today_diagrams[$i]['pj_id']);
                            }
                            array_push($diagram_by_time, $result[0]);
                            array_push($today_list, $result[1]);
                        }
                    }
                }
                // --------------------------------------------------------------------------------
                foreach ($today_diagrams as $diagram) {

                    if (!in_array($diagram['id'], $today_list) && !in_array($diagram['tree_id'], $tree_id)) {

                        $item = array(
                            $diagram['created_at'],
                            $diagram['id'],
                            $diagram['pj_id'],
                            $diagram['tree_id'],
                            $diagram['headquarters_code'],
                            $diagram['headquarters'],
                            $diagram['department_code'],
                            $diagram['department_name'],
                            $diagram['group_code'],
                            $diagram['group_name'],
                            $diagram['sales_management_code'],
                            $diagram['sales_management'],
                            $diagram['cost_code'],
                            $diagram['cost_name'],
                            $diagram['project_grp_code'],
                            $diagram['project_grp_name'],
                            $diagram['project_code'],
                            $diagram['project_name'],
                            '',
                            '',
                            '',
                            '',
                            '',  '', '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        );
                        array_push($diagram_by_time, $item);
                        array_push($today_list, $diagram['id']);
                    }
                }

                foreach ($diagrams as $diagram) {
                    if (!in_array($diagram['id'], $time_list)) {
                        $item = array(
                            '',
                            '',
                            '',  '', '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $diagram['created_at'],
                            $diagram['id'],
                            $diagram['pj_id'],
                            $diagram['tree_id'],
                            $diagram['headquarters_code'],
                            $diagram['headquarters'],
                            $diagram['department_code'],
                            $diagram['department_name'],
                            $diagram['group_code'],
                            $diagram['group_name'],
                            $diagram['sales_management_code'],
                            $diagram['sales_management'],
                            $diagram['cost_code'],
                            $diagram['cost_name'],
                            $diagram['project_grp_code'],
                            $diagram['project_grp_name'],
                            $diagram['project_code'],
                            $diagram['project_name'],
                        );
                        array_push($diagram_by_time, $item);
                    }
                }
            } else {
                $today_list = array();
                $time_list  = array();
                $tree_id    = array();
                $pj_id      = array();
                for ($i = 0; $i < $size2; $i++) {
                    if ($i <= $size1) {
                        foreach ($today_diagrams as $diagram) {
                            if ($diagram['tree_id'] != null) {

                                array_push($tree_id, $diagram['tree_id']);
                            }
                            if ($diagram['pj_id'] != null) {
                                array_push($pj_id, $diagram['pj_id']);
                            }
                            if ($result = $this->compareListTree($diagram, $diagrams, $i)) {

                                array_push($diagram_by_time, $result[0]);
                                array_push($today_list, $result[1]);
                                array_push($time_list, $result[2]);
                            }
                        }
                    } else {

                        if (in_array($diagrams[$i]['pj_id'], $pj_id) || in_array($diagrams[$i]['tree_id'], $tree_id)) {

                            if ($item = $this->compare($today_diagrams, $diagrams[$i])) {
                                array_push($diagram_by_time, $item);
                                array_push($today_list, $diagrams[$i]['id']);
                                array_push($time_list, $diagrams[$i]['id']);
                            }
                        }
                        if ($result = $this->concatTreeListTime($diagrams, $time_list, $i)) {
                            array_push($diagram_by_time, $result[0]);
                            array_push($time_list, $result[1]);
                        }
                    }
                }

                //------------------------------------------------------------------
                foreach ($today_diagrams as $diagram) {
                    if (!in_array($diagram['id'], $today_list)) {
                        $item = array(
                            $diagram['created_at'],
                            $diagram['id'],
                            $diagram['pj_id'],
                            $diagram['tree_id'],
                            $diagram['headquarters_code'],
                            $diagram['headquarters'],
                            $diagram['department_code'],
                            $diagram['department_name'],
                            $diagram['group_code'],
                            $diagram['group_name'],
                            $diagram['sales_management_code'],
                            $diagram['sales_management'],
                            $diagram['cost_code'],
                            $diagram['cost_name'],
                            $diagram['project_grp_code'],
                            $diagram['project_grp_name'],
                            $diagram['project_code'],
                            $diagram['project_name'],
                            '',
                            '',
                            '',
                            '',  '', '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        );
                        array_push($diagram_by_time, $item);
                        array_push($today_list, $diagram['id']);
                    }
                }

                foreach ($diagrams as $diagram) {
                    if (!in_array($diagram['id'], $time_list)) {
                        $item = array(
                            '',
                            '',
                            '',
                            '', '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $diagram['created_at'],
                            $diagram['id'],
                            $diagram['pj_id'],
                            $diagram['tree_id'],
                            $diagram['headquarters_code'],
                            $diagram['headquarters'],
                            $diagram['department_code'],
                            $diagram['department_name'],
                            $diagram['group_code'],
                            $diagram['group_name'],
                            $diagram['sales_management_code'],
                            $diagram['sales_management'],
                            $diagram['cost_code'],
                            $diagram['cost_name'],
                            $diagram['project_grp_code'],
                            $diagram['project_grp_name'],
                            $diagram['project_code'],
                            $diagram['project_name'],
                        );
                        array_push($diagram_by_time, $item);
                        array_push($time_list, $diagram['id']);
                    }
                }
            }
            array_push($diagram_by_time, array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',));
            array_push($list_diagram, $diagram_by_time); //
        }

        echo "\xEF\xBB\xBF";
        $columns = array(
            '日付',
            'id',
            'pj_id',
            'tree_id',
            '事業本部コード',
            '事業本部',
            '部署コード',
            '部署',
            'グループコード',
            'グループ',
            '販管コード',
            '販管費',
            '原価コード',
            '原価',
            '集計コード',
            '集計コード名',
            'プロジェクトコード',
            'プロジェクト名',
            '変更日',
            'id',
            'pj_id',
            'tree_id',
            '事業本部コード',
            '事業本部',
            '部署コード',
            '部署',
            'グループコード',
            'グループ',
            '販管コード',
            '販管費',
            '原価コード',
            '原価',
            '集計コード',
            '集計コード名',
            'プロジェクトコード',
            'プロジェクト名'
        );

        $callback = function () use ($columns, $list_diagram) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($list_diagram as $list_d) {
                foreach ($list_d as $list) {
                    fputcsv($file, $list);
                }
            }
            fclose($file);
        };
        return $callback;
    }

    public function compare($today_diagrams, $diagram)
    {
        foreach ($today_diagrams as $d) {
            if ($d['id'] == $diagram['id']) {
                $item = array(
                    $d['created_at'],
                    $d['id'],
                    $d['pj_id'],
                    $d['tree_id'],
                    $d['headquarters_code'],
                    $d['headquarters'],
                    $d['department_code'],
                    $d['department_name'],
                    $d['group_code'],
                    $d['group_name'],
                    $d['sales_management_code'],
                    $d['sales_management'],
                    $d['cost_code'],
                    $d['cost_name'],
                    $d['project_grp_code'],
                    $d['project_grp_name'],
                    $d['project_code'],
                    $d['project_name'],
                    $diagram['created_at'],
                    $diagram['id'],
                    $diagram['pj_id'],
                    $diagram['tree_id'],
                    $diagram['headquarters_code'],
                    $diagram['headquarters'],
                    $diagram['department_code'],
                    $diagram['department_name'],
                    $diagram['group_code'],
                    $diagram['group_name'],
                    $diagram['sales_management_code'],
                    $diagram['sales_management'],
                    $diagram['cost_code'],
                    $diagram['cost_name'],
                    $diagram['project_grp_code'],
                    $diagram['project_grp_name'],
                    $diagram['project_code'],
                    $diagram['project_name'],
                );
                return   $item;
            }
        }
        return false;
    }

    public function compareListTree($diagram, $diagrams, $i)
    {
        $result  = array();
        $tree_id = $diagram['tree_id'];
        $pj_id   = $diagram['pj_id'];

        if ((($diagram['tree_id'] == $diagrams[$i]['tree_id']) && ($diagram['tree_id'] != null)) ||
            (($diagram['pj_id']   == $diagrams[$i]['pj_id']) && ($diagram['pj_id'] != null))
        ) {

            $item = array(

                $diagram['created_at'],
                $diagram['id'],
                $diagram['pj_id'],
                $diagram['tree_id'],
                $diagram['headquarters_code'],
                $diagram['headquarters'],
                $diagram['department_code'],
                $diagram['department_name'],
                $diagram['group_code'],
                $diagram['group_name'],
                $diagram['sales_management_code'],
                $diagram['sales_management'],
                $diagram['cost_code'],
                $diagram['cost_name'],
                $diagram['project_grp_code'],
                $diagram['project_grp_name'],
                $diagram['project_code'],
                $diagram['project_name'],
                $diagrams[$i]['created_at'],
                $diagrams[$i]['id'],
                $diagrams[$i]['pj_id'],
                $diagrams[$i]['tree_id'],
                $diagrams[$i]['headquarters_code'],
                $diagrams[$i]['headquarters'],
                $diagrams[$i]['department_code'],
                $diagrams[$i]['department_name'],
                $diagrams[$i]['group_code'],
                $diagrams[$i]['group_name'],
                $diagrams[$i]['sales_management_code'],
                $diagrams[$i]['sales_management'],
                $diagrams[$i]['cost_code'],
                $diagrams[$i]['cost_name'],
                $diagrams[$i]['project_grp_code'],
                $diagrams[$i]['project_grp_name'],
                $diagrams[$i]['project_code'],
                $diagrams[$i]['project_name'],
            );
            array_push($result, $item);
            array_push($result, $diagram['id']);
            array_push($result, $diagrams[$i]['id']);
            array_push($result, $tree_id);
            array_push($result, $pj_id);
            return  $result;
        }
        return false;
    }

    public function concatTreeListToday($today_diagrams, $today_list, $i)
    {

        $result  = array();
        if (!in_array($today_diagrams[$i]['id'], $today_list)) {

            $item = array(
                $today_diagrams[$i]['created_at'],
                $today_diagrams[$i]['headquarters_code'],
                $today_diagrams[$i]['headquarters'],
                $today_diagrams[$i]['department_code'],
                $today_diagrams[$i]['department_name'],
                $today_diagrams[$i]['group_code'],
                $today_diagrams[$i]['group_name'],
                $today_diagrams[$i]['sales_management_code'],
                $today_diagrams[$i]['sales_management'],
                $today_diagrams[$i]['cost_code'],
                $today_diagrams[$i]['cost_name'],
                $today_diagrams[$i]['project_grp_code'],
                $today_diagrams[$i]['project_grp_name'],
                $today_diagrams[$i]['project_code'],
                $today_diagrams[$i]['project_name'],
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            );

            array_push($result, $item);
            array_push($result, $today_diagrams[$i]['id']);
        }

        return false;
    }

    public function concatTreeListTime($diagrams, $time_list, $i)
    {

        $result  = array();
        if (!in_array($diagrams[$i]['id'], $time_list)) {

            $item = array(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $diagrams[$i]['created_at'],
                $diagrams[$i]['headquarters_code'],
                $diagrams[$i]['headquarters'],
                $diagrams[$i]['department_code'],
                $diagrams[$i]['department_name'],
                $diagrams[$i]['group_code'],
                $diagrams[$i]['group_name'],
                $diagrams[$i]['sales_management_code'],
                $diagrams[$i]['sales_management'],
                $diagrams[$i]['cost_code'],
                $diagrams[$i]['cost_name'],
                $diagrams[$i]['project_grp_code'],
                $diagrams[$i]['project_grp_name'],
                $diagrams[$i]['project_code'],
                $diagrams[$i]['project_name'],
            );
            array_push($result, $item);
            array_push($result, $diagrams[$i]['id']);
        }
        return false;
    }

    /*
    最新のツリー図を作成
    param $today_diagrams 最新ツリー図のデータ
     */
    public function createDiagram2($today_diagrams)
    {


        $list_diagram    = array();

        $size1           = sizeof($today_diagrams);

        $diagram_by_time = array();

        for ($i = 0; $i < $size1; $i++) {

            $item = array(
                $today_diagrams[$i]['created_at'],
                $today_diagrams[$i]['headquarters_code'],
                $today_diagrams[$i]['headquarters'],
                $today_diagrams[$i]['department_code'],
                $today_diagrams[$i]['department_name'],
                $today_diagrams[$i]['group_code'],
                $today_diagrams[$i]['group_name'],
                $today_diagrams[$i]['sales_management_code'],
                $today_diagrams[$i]['sales_management'],
                $today_diagrams[$i]['cost_code'],
                $today_diagrams[$i]['cost_name'],
                $today_diagrams[$i]['project_grp_code'],
                $today_diagrams[$i]['project_grp_name'],
                $today_diagrams[$i]['project_code'],
                $today_diagrams[$i]['project_name']
            );
            array_push($diagram_by_time, $item);
        }

        array_push($diagram_by_time, array('', '', '', '', '', '', '', '', '', '', '', '', '', '',));
        array_push($list_diagram, $diagram_by_time); //

        echo "\xEF\xBB\xBF";
        $columns = array(
            '日付',
            '事業本部コード',
            '事業本部',
            '部署コード',
            '部署',
            'グループコード',
            'グループ',
            '販管コード',
            '販管費',
            '原価コード',
            '原価',
            '集計コード',
            '集計コード名',
            'プロジェクトコード',
            'プロジェクト名',
        );

        $callback = function () use ($columns, $list_diagram) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($list_diagram as $list_d) {
                foreach ($list_d as $list) {
                    fputcsv($file, $list);
                }
            }
            fclose($file);
        };
        return $callback;
    }

    public function getDataForView(){
            $search_date = empty(request()->search_date) ? $this->getToday() : request()->search_date;
            $list_id_data = $this->getListTreeIdByTime($this->changeFormatDate($search_date));

            if (!empty(request()->company_id)) {
                $diagrams      = Diagram::where('company_id', request()->company_id);
            } else {
                $usr_id        = Auth::user()->id;
                $company_id    = Common::checkUserCompany($usr_id);
                $diagrams      = Diagram::whereIn('company_id', $company_id);
            }

            $diagrams  = !empty($list_id_data)  ? $diagrams->whereIn('created_at', array_column($list_id_data, 'created_at'))->get() : $diagrams->get();
            return $diagrams;
    }
}

