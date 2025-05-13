<?php
    $head = array( 
        'Ano/Número',
        'Nome',
        'Promotor',
        'Cidade',
        'UF',
        'Dt. Início',
        'Dt. Final',
        'Tipo',
        'Treinamento Replicado',
        'Dt. Finalizado',
        'Usuário'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['numero'],
            array(anchor('servico/avaliacao_treinamento_replica/cadastro/'.$item['cd_treinamento_colaborador_item'], $item['nome']), 'text-align:left;'),
            array($item['promotor'], 'text-align:left;'),            
            array($item['cidade'], 'text-align:left;'),            
            $item['uf'],
            $item['dt_inicio'],
            $item['dt_final'],
            array($item['ds_treinamento_colaborador_tipo'], 'text-align:left;'),
            $item['fl_aplica_replica'],
            $item['dt_finalizado'],
            $item['ds_usuario']
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();