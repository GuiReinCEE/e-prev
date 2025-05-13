<?php
    $head = array(
		'Regulamento',
		'Artigo',
		'Dt. Envio',
		'Pertinente',
		'Dt. Previsto',
		'Dt. Implementado',
		'Acompanhamento',
		''
    );

    $body = array();

    foreach ($collection as $key => $item)
    {
    	$body[] = array(
    		array($item['ds_regulamento_tipo'], 'text-align:left'),
    		array(nl2br($item['ds_artigo']), 'text-align:justify'),
    		$item['dt_envio'],
    		'<span class="'.$item['ds_class_tipo'].'">'.$item['ds_regulamento_alteracao_atividade_tipo'].'</span>',
    		$item['dt_prevista'],
    		$item['dt_implementacao'],
    		array(nl2br($item['ds_regulamento_alteracao_atividade_acompanhamento']), 'text-align:justify'),
    		anchor('atividade/regulamento_alteracao_atividade/index/'.$item['cd_regulamento_alteracao_atividade'], '[atividade]')
    	);
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();