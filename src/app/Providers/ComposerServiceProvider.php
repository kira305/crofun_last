<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;


class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // Prepare the department list and chiefs information for employee's form.
        // View::composer(
        //     [
        //         'user.index',
        //         'user.create',
        //         'user.edit',
        //         'user.concurrent_create',
        //         'user.concurrent_edit',
        //         'customer.index'
        //     ],
        //     'App\Http\ViewComposers\UserIndexViewComposer'
        // );

        View::composer(
            [

                'department.edit',

            ],
            'App\Http\ViewComposers\GetListCompanyComposer'

        );

        View::composer(
            [

                'headquarter.edit',
                'customer.index',

            ],
            'App\Http\ViewComposers\GetListHeadquarterComposer'
        );

        View::composer(
            [

                'department.edit',
                'customer.index',

            ],
            'App\Http\ViewComposers\GetListDepartmentComposer'
        );

        View::composer(

            [

                'group.edit',


            ],
            'App\Http\ViewComposers\GetListGroupComposer'
        );

        View::composer(

            [

                'project.create',
                'project.edit',


            ],
            'App\Http\ViewComposers\GetOrForCreateProjectComposer'
        );

        View::composer(

            [
                'user.index',
                'user.create',
                'user.edit',
                'headquarter.index',
                'headquarter.create',
                'headquarter.edit',
                'department.index',
                'department.create',
                'group.index',
                'group.edit',
                'group.create',
                'department.edit',
                'user.concurrent_create',
                'user.concurrent_edit',
                'cost.index',
                'cost.create',
                'cost.edit',
                'customer.index',
                'customer.edit',
                'customer.create',
                'customer.view',
                'customer.test',
                'receivable.index',
                'process.index',
                'project.index',
                'pca.upload',
                'import_log.index',
                'rule.index',
                'rule.create',
                'rule.edit',
                'position.index',
                'position.create',
                'position.edit',
                'contract.index',
                'credit.log',
                'credit.search',
                'log.index',
                'log.view',
                'diagram.index',
                'custom_name.index',
                'contract.create',
                'contract_type.create',
                'contract_type.edit',
                'contract_type.index',
            ],
            'App\Http\ViewComposers\GetAllCompanyComposer'
        );
        /**View::composer(
            [
                'user.edit'
                            ],
            'App\Http\ViewComposers\Usr_edit_company'
        );**/


        // View::composer(
        //     [
        //         'user.edit'

        //     ],
        //     'App\Http\ViewComposers\GetListCompanyForUserEditComposer'
        // );

        View::composer(
            [
                'headquarter.edit',
                'department.index',
                'department.create',
                'department.edit',
                'group.index',
                'group.edit',
                'group.create',
                'user.concurrent_create',
                'user.concurrent_edit',
                'user.edit',
                'user.index',
                'user.create',
                'cost.index',
                'cost.create',
                'cost.edit',
                'project.index',
                'process.index',
                'contract.index',
                'customer.edit',
                'customer.create',
                'customer.view',
                'customer.test',
                'contract.create',
                'contract.edit',
                'contract.view',
            ],
            'App\Http\ViewComposers\GetAllHeadquarterComposer'
        );

        View::composer(
            [
                'department.edit',
                'group.index',
                'group.create',
                'group.edit',
                'user.concurrent_create',
                'user.concurrent_edit',
                'user.index',
                'user.create',
                'user.edit',
                'cost.index',
                'cost.create',
                'cost.edit',
                'project.index',
                'process.index',
                'contract.index',
                'customer.edit',
                'customer.create',
                'customer.view',
                'customer.test',
                'contract.create',
                'contract.edit',
                'contract.view',

            ],
            'App\Http\ViewComposers\GetAllDepartmentComposer'
        );

        View::composer(
            [
                'user.index',
                'user.create',
                'user.edit',
                'group.edit',
                'user.concurrent_create',
                'user.concurrent_edit',
                'cost.index',
                'cost.create',
                'cost.edit',
                'customer.index',
                'project.index',
                'process.index',
                'contract.index',
                'customer.edit',
                'customer.create',
                'customer.view',
                'customer.test',
                'contract.create',
                'contract.edit',
                'contract.view',

            ],
            'App\Http\ViewComposers\GetAllGroupComposer'
        );

        View::composer(
            [
                'user.index',
                'user.create',
                'user.edit'
            ],
            'App\Http\ViewComposers\RuleListComposer'
        );

        View::composer(
            [
                'user.index',
                'user.create',
                'user.edit',
                'user.concurrent_create',
                'user.concurrent_edit'
            ],
            'App\Http\ViewComposers\PositionListComposer'
        );



        View::composer(
            [
                'layouts.app'
            ],
            'App\Http\ViewComposers\LogoCompanyComposer'
        );


        View::composer(
            [
                'layouts.app'
            ],
            'App\Http\ViewComposers\MenuListComposer'
        );

        /*メニューリスト*/
        View::composer(
            [
                'log.index',
            ],
            'App\Http\ViewComposers\GetListMenuComposer'
        );
        // ツリー図のデータ
        View::composer(
            [
                'diagram.index',
            ],
            'App\Http\ViewComposers\CostListComposer'
        );

        View::composer(
            [
                'diagram.index',
            ],
            'App\Http\ViewComposers\GetListProjectComposer'
        );

        // View::composer(
        //     [
        //         'diagram.index',
        //     ],
        //     'App\Http\ViewComposers\OrForDiagramComposer'
        // );

        View::composer(
            [
                'diagram.index',
            ],
            'App\Http\ViewComposers\GetListHeadquarterDiagramComposer'
        );

        View::composer(
            [
                'diagram.index',
            ],
            'App\Http\ViewComposers\GetListDepartmentDiagramComposer'
        );

        View::composer(
            [
                'diagram.index',
            ],
            'App\Http\ViewComposers\GetListGroupDiagramComposer'
        );


        View::composer(
            [

                'diagram.index'

            ],
            'App\Http\ViewComposers\GetListCalendarDiagramComposer'
        );

        View::composer(
            [

                'layouts.app'

            ],
            'App\Http\ViewComposers\GetListMenuWithRuleComposer'
        );

        View::composer(
            [

                'import_log.index'

            ],
            'App\Http\ViewComposers\GetTokenComposer'
        );
    }
}
