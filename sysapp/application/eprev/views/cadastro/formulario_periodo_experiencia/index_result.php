<?php
	$head = array(
		'Dt. Solicitação', 
		'Avaliador',
		'Avaliado',
		'Dt. Limite',
		'Formulário',
		'Respondido',
		'Dt. Respondido',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('cadastro/formulario_periodo_experiencia/cadastro/'.$item['cd_formulario_periodo_experiencia_solic'], $item['dt_inclusao']),
		    array($item['ds_avaliador'], 'text-align:left;'),
		    array($item['ds_avaliado'], 'text-align:left;'),
		    $item['dt_limite'],
		    $item['ds_formulario_periodo_experiencia'],
		    '<span class="'.$item['ds_class_resposta'].'">'.$item['ds_resposta'].'</span>',
		    $item['dt_resposta'],
		    (trim($item['dt_resposta']) != '' ? (trim($item['arquivo']) != '' 
		    		? anchor(base_url('up/formulario_periodo_experiencia/'.$item['arquivo']), '[Arquivo]')
		    		: anchor('cadastro/formulario_periodo_experiencia/pdf/'.$item['cd_formulario_periodo_experiencia_solic'], '[PDF]')
		    	) : '').' '.
		    (trim($item['dt_resposta']) == '' ? anchor('cadastro/formulario_periodo_experiencia/responder/'.$item['cd_formulario_periodo_experiencia_solic'], '[Responder]') : '')
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>