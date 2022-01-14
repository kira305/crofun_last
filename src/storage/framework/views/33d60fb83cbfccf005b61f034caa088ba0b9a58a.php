<?php $__env->startSection('content'); ?>
<?php $__env->startSection('breadcrumbs', Breadcrumbs::render('home')); ?>
<div class="container-fluid">
    <?php
        $canUpdate = Auth::user()->can('update','App\Customer_MST');
        $canView =  Auth::user()->can('view','App\Customer_MST');
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">お知らせ</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <tbody>
                            <?php $__currentLoopData = $global_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $set_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php switch($set_data->important_flg):
                                    case ("1"): ?>
                                        <tr class="danger">
                                        <?php break; ?>
                                    <?php case ("2"): ?>
                                        <tr class="warning">
                                        <?php break; ?>
                                    <?php case ("3"): ?>
                                        <tr class="active">
                                        <?php break; ?>
                                    <?php default: ?>
                                        <tr class="active">
                                        <?php break; ?>
                                <?php endswitch; ?>
                                <?php if(!empty($set_data->save_ol_name)): ?>
                                    <td class="hfsz text-center" width="5%">
                                        <a href="<?php echo e(route('global_info.download', ['id' => $set_data->id,'ol_name' =>"__" ,'sv_name' => "__"]), false); ?>"
                                            title="<?php echo e($set_data->save_ol_name, false); ?>">
                                            <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                                        </a>
                                    </td>
                                <?php else: ?>
                                    <td width="5%"></td>
                                <?php endif; ?>
                                    <td class="hfsz" width="83%"><?php echo e($set_data->global_info_title, false); ?><?php echo e("  :  ", false); ?><?php echo $set_data->global_info_content_change; ?></td>
                                    <td class="hfsz" width="10%"><?php echo e(date('Y/m/d', strtotime($set_data->updated_at)), false); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-danger">
                <div class="panel-heading">契約終了アラート</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th  class="active ">申請番号</th>
                                <th  class="active ">顧客名</th>
                                <th  class="active ">期限</th>
                                <th  class="active ">申請本部</th>
                                <th  class="active ">申請部</th>
                                <th  class="active ">申請グループ</th>
                                <th  class="active ">参照</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $contractAlert; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php ($contractAction = Crofun::getPermissionContract($data, Auth::user(), true)); ?>
                                <?php if($contractAction): ?>
                                    <tr class="">
                                        <td width=""><?php echo e($data->application_num, false); ?></td>
                                        <td class="" width=""><?php echo e($data->getCustomerName(), false); ?></td>
                                        <td class="" width=""><?php echo e($data->contract_end_date, false); ?></td>
                                        <td class="" width=""><?php echo e($data->headquarter->headquarters, false); ?></td>
                                        <td class="" width=""><?php echo e($data->department->department_name, false); ?></td>
                                        <td class="" width=""><?php echo e($data->group->group_name, false); ?></td>
                                        <td class="" width="">
                                            <a class="btn btn-primary" href="<?php echo e(route('contract.edit', ['id' => $data->id]), false); ?>">参照</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-success">
                <div class="panel-heading">仮登録一覧</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th  class="active ">登録日</th>
                                <th  class="active ">顧客名</th>
                                <th  class="active ">申請部署</th>
                                <th  class="active ">申請Gr</th>
                                <th  class="active ">参照</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class=""><?php echo e(date('Y/m/d',strtotime($customer_date->created_at)), false); ?></td>
                                    <td class="">
                                        <?php if($customer_date->client_name_ab == null): ?>
                                            <?php echo e($customer_date->client_name, false); ?>

                                        <?php else: ?>
                                            <?php echo e($customer_date->client_name_ab, false); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td class=""><?php echo e($customer_date->com_grp()->department_name, false); ?></td>
                                    <td class=""><?php echo e($customer_date->com_grp()->group_name, false); ?></td>
                                    <td class="">
                                        <?php if($canUpdate): ?>
                                            <a href="<?php echo e(route('customer_edit', ['id' => $customer_date->id]), false); ?>" class="btn btn-primary" style="float: left">参照</a>
                                        <?php elseif($canView): ?>
                                            <a href="<?php echo e(route('customer_view', ['id' => $customer_date->id]), false); ?>" class="btn btn-primary" style="float: left">参照</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-warning">
                <div class="panel-heading">与信限度額要確認</div>
                <div class="table-parent" id="receivable">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th class="active">顧客名</th>
                                <th class="active">対象月</th>
                                <th class="active ">与信限度額</th>
                                <th class="active ">取引想定額</th>
                                <th class="active ">売掛金残</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $over_receivable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="">
                                    <?php if($value->client_name_ab == null): ?>
                                        <?php echo e($value->client_name, false); ?>

                                    <?php else: ?>
                                        <?php echo e($value->client_name_ab, false); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="">
                                    <?php if($receivable_date[$key]->target_data != null): ?>
                                        <?php echo e(date('Y/m',strtotime($receivable_date[$key]->target_data)), false); ?>

                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e(number_format($value->credit_expect/1000), false); ?></td>
                                <td class="" <?php if($transaction_date[$key]> $value->credit_expect): ?>  style = "background-color: #FFB6C1;" <?php endif; ?>
                                    ><?php echo e(number_format($transaction_date[$key]/1000), false); ?>

                                </td>
                                <td class=""
                                    <?php if($receivable_date[$key]->receivable != ""): ?>
                                        <?php if($receivable_date[$key]->receivable > $value->credit_expect): ?>
                                            style = "background-color: #FFB6C1;"
                                        <?php endif; ?>
                                    <?php endif; ?>>
                                    <?php if($receivable_date[$key]->receivable != ""): ?>
                                        <?php echo e(number_format($receivable_date[$key]->receivable/1000), false); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>