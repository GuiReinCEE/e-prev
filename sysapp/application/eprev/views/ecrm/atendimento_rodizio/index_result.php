<?php	
	$this->load->helper('grid');
	
	$head = array(
		'Data',
		'Turno',
		''
	);
    
    $head_anterior = array(
        'Atendente',
        'Posição'
    );

	$body = array();

	$grid_anterior = new grid();
	$grid_anterior->view_count = false;

	foreach ($collection as $key => $item)
	{	
		$body_anterior = array();

		foreach($collection[$key]['atendente'] as $item_anterior)
		{
			$body_anterior[] = array(
				array($item_anterior['ds_usuario_atendimento'], 'text-align:left;'),
				array($item_anterior['ds_posicao'], 'text-align:left;')
			);
		}

		$grid_anterior->head = $head_anterior;
		$grid_anterior->body = $body_anterior;
		
		$body[] = array(
			anchor('ecrm/atendimento_rodizio/cadastro/'.$item['cd_atendimento_rodizio'], $item['dt_atendimento_rodizio']),
			'<span class="'.$item['ds_class_turno'].'">'.$item['ds_turno'].'</span>',
		    $grid_anterior->render()
 		);
	}

	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>