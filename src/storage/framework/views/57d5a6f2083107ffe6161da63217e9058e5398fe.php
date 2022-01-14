<?php $__env->startSection('content'); ?>
<style>
    .ab-c-t {
    position: absolute;
    top: 0px;
    left: 50%;
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
    }
</style>
<div class="login-box">

    <!-- /.login-logo -->
    <div class="login-box-body">
        <h2 style="font-family: Times New Roman, Times, serif;text-align: center">Cro-Fun</h2>
        <span class="text-danger"><?php echo \Session::get('message'); ?></span>
        <form action="<?php echo e(url('/login'), false); ?>" method="post" autocomplete="off" id="cross-form">
            <?php echo e(csrf_field(), false); ?>

            <?php if(isset($ok_message)): ?>
            <br><span class="text-info"><?php echo e($ok_message, false); ?></span>
            <?php endif; ?>

            <div class="form-group has-feedback">
                <label>社員番号</label>

                <input class="form-control" id="usr_id" name="usr_code" type="interger" autocomplete="nope"
                    value="<?php echo e(empty(old('usr_code')) ? Cookie::get('usr_code') :  old('usr_code'), false); ?>">
                <?php if($errors->has('usr_code')): ?>

                <br><span class="text-danger"><?php echo e(trans('auth.username'), false); ?></span>

                <?php endif; ?>
                <?php echo csrf_field(); ?>
            </div>
            <div class="form-group has-feedback">
                <label>パスワード</label>
                <input type="password" id="password" class="form-control" name="pw" autocomplete="new-password" value="">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                <?php if($errors->has('pw')): ?>
                    <br><span class="text-danger"><?php echo e(trans('auth.password'), false); ?></span>
                <?php endif; ?>
            </div>
            <div>
                <?php if(isset($message)): ?>
                    <span class="text-danger"><?php echo e($message, false); ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group has-feedback" style="text-align: right">
                    <button type="submit" class="btn btn-primary">ログイン</button>
            </div>
        </form>
        <a href="<?php echo e(url('user/reset-password'), false); ?>">パスワードを忘れた場合</a><br>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        document.getElementById("cross-form").reset();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>