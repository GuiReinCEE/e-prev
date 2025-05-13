<?php
	$head = array( 
        'Ano/N°',
        'Pergunta',
        'Resposta',
        'Responsável',
        'Dt. Encaminhamento',
        'Dt. Resposta'
    );

    $body = array();

	foreach($collection as $item)
	{
		$body[] = array(
            $item['nr_ano_pergunta'],
            array(anchor('cadastro/pergunta_resposta/cadastro/'.$item['cd_pergunta_resposta'], $item['ds_pergunta']), 'text-align:justify'),
            array($item['ds_resposta'], 'text-align:justify'),
            array($item['ds_usuario_responsavel'], 'text-align:left'),
            $item['dt_encaminha_responsavel'],
            $item['dt_resposta']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>