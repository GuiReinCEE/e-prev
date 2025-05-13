<?php
    $head = array( 
        'Dt. Solciticão',
        'RE',
        'Nome',
        'Telefone',
        'E-mail',
        'Acompanhamento'
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            anchor('planos/contribuicao_patrocinadora/acompanhamento/'. $item['cd_contribuicao_patroc'], $item['dt_solicitacao']),
            $item['re'],
            $item['nome'],
            $item['ds_telefone'],
            $item['ds_email'],
            array(nl2br($item['ds_acompanhamento']), 'text-align:justify')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>
