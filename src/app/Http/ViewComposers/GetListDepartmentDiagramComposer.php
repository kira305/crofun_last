<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Service\DiagramService;

class GetListDepartmentDiagramComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $diagramService = new DiagramService();
        $diagrams  = $diagramService->getDataForView();

        $list_diagrams   = array();
        $checked_diagram = array();

        foreach ($diagrams as $diagram) {
            if (!in_array($diagram->department_code, $checked_diagram) && $diagram->department_name != null) {
                array_push($list_diagrams, $diagram);
                array_push($checked_diagram, $diagram->department_code);
            }
        }

        $view->with([
            'departments' =>  $list_diagrams,
        ]);
    }
}
