<?php
    $head = array( 
        'Nº Atividade',
        'RE',
        'Nome Participante',
        'Dt. Retorno',
        'Observação'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            anchor('ecrm/atendimento_retorno_participante/cadastro/'.$item['cd_atendimento_retorno_participante'], $item['cd_atividade']),
            $item['ds_re'],
            array($item['nome'], 'text-align:left'),
            $item['dt_retorno'],
            array($item['ds_observacao'], 'text-align:justify')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();