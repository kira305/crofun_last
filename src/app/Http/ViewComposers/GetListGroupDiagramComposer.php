<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Service\DiagramService;

class GetListGroupDiagramComposer
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
            if (!in_array($diagram->group_code, $checked_diagram) && $diagram->group_name != null) {
                array_push($list_diagrams, $diagram);
                array_push($checked_diagram, $diagram->group_code);
            }
        }

        $view->with([
            'groups' =>  $list_diagrams,
        ]);
    }
}
