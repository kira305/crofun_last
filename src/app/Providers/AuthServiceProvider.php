<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Project_MST;
use App\Group_MST;
use App\Department_MST;
use App\Headquarters_MST;
use App\Company_MST;
use App\Receivable_MST;
use App\User;
use App\Customer_MST;
use App\Cost_MST;
use App\Rule_MST;
use App\Position_MST;
use App\Process_MST;
use App\Contract_MST;
use App\Credit_MST;
use App\Log_MST;
use App\Policies\UserPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\GroupPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\HeadquarterPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\CostPolicy;
use App\Policies\Receivable_Policy;
use App\Policies\PositionPolicy;
use App\Policies\RulePolicy;
use App\Policies\ProcessPolicy;
use App\Policies\ContractPolicy;
use App\Policies\CreditPolicy;
use App\Policies\LogPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */

    protected $policies = [
        'App\Model'             => 'App\Policies\ModelPolicy',
        Customer_MST::class     => CustomerPolicy::class,
        User::class             => UserPolicy::class,
        Project_MST::class      => ProjectPolicy::class,
        Group_MST::class        => GroupPolicy::class,
        Department_MST::class   => DepartmentPolicy::class,
        Headquarters_MST::class => HeadquarterPolicy::class,
        Company_MST::class      => CompanyPolicy::class,
        Cost_MST::class         => CostPolicy::class,
        Receivable_MST::class   => Receivable_Policy::class,
        Position_MST::class     => PositionPolicy::class,
        Rule_MST::class         => RulePolicy::class,
        Process_MST::class      => ProcessPolicy::class,
        Contract_MST::class     => ContractPolicy::class,
        Credit_MST::class       => CreditPolicy::class,
        Log_MST::class          => LogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user-index', function ($user) {
            return $user->getRuleAction(config('constant.USER_INDEX'));
        });

        Gate::define('group-index', function ($user) {
            return $user->getRuleAction(config('constant.GROUP_INDEX'));
        });

        Gate::define('department-index', function ($user) {
            return $user->getRuleAction(config('constant.DEPARTMENT_INDEX'));
        });

        Gate::define('headquarter-index', function ($user) {
            return $user->getRuleAction(config('constant.HEADQUATER_INDEX'));
        });

        Gate::define('company-index', function ($user) {
            return $user->getRuleAction(config('constant.COMPANY_INDEX'));
        });

        Gate::define('customer-index', function ($user) {
            return $user->getRuleAction(config('constant.CLIENT_INDEX'));
        });

        Gate::define('customer-name', function ($user) {
            return $user->getRuleAction(config('constant.CLIENT_NAME'));
        });

        Gate::define('customer-edit', function ($user) {
            return $user->getRuleAction(config('constant.CLIENT_EDIT'));
        });

        Gate::define('customer-view', function ($user) {
            return $user->getRuleAction(config('constant.CLIENT_VIEW'));
        });

        // customer_name delete
        Gate::define('position-index', function ($user) {
            return $user->getRuleAction(config('constant.POSITION_INDEX'));
        });

        Gate::define('tree-index', function ($user) {
            return $user->getRuleAction(config('constant.TREE_INDEX'));
        });

        Gate::define('project-index', function ($user) {
            return $user->getRuleAction(config('constant.PROJECT_INDEX'));
        });

        Gate::define('project-add', function ($user) {
            return $user->getRuleAction(config('constant.PROJECT_ADD'));
        });

        Gate::define('project-edit', function ($user) {
            return $user->getRuleAction(config('constant.PROJECT_EDIT'));
        });

        Gate::define('project-view', function ($user) {
            return $user->getRuleAction(config('constant.PROJECT_VIEW'));
        });

        Gate::define('rule-index', function ($user) {
            return $user->getRuleAction(config('constant.RULE_INDEX'));
        });

        Gate::define('credit-index', function ($user) {
            return $user->getRuleAction(config('constant.CREDIT_INDEX'));
        });

        Gate::define('credit-add', function ($user) {
            return $user->getRuleAction(config('constant.CREDIT_ADD'));
        });

        Gate::define('credit-edit', function ($user) {
            return $user->getRuleAction(config('constant.CREDIT_EDIT'));
        });

        Gate::define('credit-log', function ($user) {
            return $user->getRuleAction(config('constant.CREDIT_LOG'));
        });

        Gate::define('process-index', function ($user) {
            return $user->getRuleAction(config('constant.PROCESS_INDEX'));
        });

        Gate::define('receivable-index', function ($user) {
            return $user->getRuleAction(config('constant.RSCEIVABLE_INDEX'));
        });

        Gate::define('cost-index', function ($user) {
            return $user->getRuleAction(config('constant.COST_INDEX'));
        });

        Gate::define('importlog-index', function ($user) {
            return $user->getRuleAction(config('constant.IMPORT_LOG_INDEX'));
        });

        Gate::define('importlog-delete', function ($user) {
            return $user->getRuleAction(config('constant.IMPORT_LOG_DELETE'));
        });

        Gate::define('pca-log-index', function ($user) {
            return $user->getRuleAction(config('constant.PCA_LOG'));
        });

        Gate::define('contract-index', function ($user) {
            return $user->getRuleAction(config('constant.CONTRACT_INDEX'));
        });

        // Gate::define('contract-view', function ($user) {
        //     return $user->getRuleAction(config('constant.CONTRACT_DISPLAY'));
        // });

        Gate::define('contract-create', function ($user) {
            return $user->getRuleAction(config('constant.CONTRACT_CREATE'));
        });

        Gate::define('change-password', function ($user) {
            return $user->getRuleAction(config('constant.PASSWORD'));
        });

        Gate::define('mail-text', function ($user) {
            return $user->getRuleAction(config('constant.MAIL_TEXT'));
        });

        Gate::define('system-infor', function ($user) {
            return $user->getRuleAction(config('constant.SYSTEM_INFOR'));
        });

        Gate::define('system-new', function ($user) {
            return $user->getRuleAction(config('constant.SYSTEM_NEW'));
        });
    }
}
