<?php
// app/Repositories/PostRepository.php
namespace App\Repositories;
use App\Project_MST;
class CreditInforRepository
{
     
    public function getTransactionMoney($client_id){

        $credit_expect = Project_MST::leftjoin('customer_mst','customer_mst.id','=','project_mst.client_id')
            ->where('client_id', $client_id)
            ->where('project_mst.status','true')
            ->sum('transaction_money');
            //単発
        $transaction_shot = Project_MST::leftjoin('customer_mst','customer_mst.id','=','project_mst.client_id')
              ->where('client_id', $client_id)
              ->where('project_mst.status','true')
              ->where('project_mst.once_shot','true')
              ->sum('transaction_shot');

        $transaction =  $credit_expect + $transaction_shot;

        return $transaction;

    }



}