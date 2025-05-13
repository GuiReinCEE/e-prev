<?php
    $head = array( 
        'Grupo',
        'Lista',
        'Dt. Inclusão',
        ''       
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array(anchor('ecrm/lista_negra_divulgacao/email/'.$item['cd_lista_negra_divulgacao'], $item['ds_lista_negra_divulgacao']), 'text-align:left'),
            array((count($item['emails']) > 0 ? implode(br(), $item['emails']) : ''), 'text-align:left'),
            $item['dt_inclusao'],
            '<a href="javascript:void(0);" onclick="excluir('.$item['cd_lista_negra_divulgacao'].' )">[excluir]</a>'
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>