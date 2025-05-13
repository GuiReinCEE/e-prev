<?php
	$head = array(
		'Dt. Cadastro',
		'RE',
		'Nome',
		'Status',
		'Dt. Último Acompanhamento',
		'Acompanhamento',
		'Dt. Agendamento'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			anchor('ecrm/portabilidade/cadastro/'.$item['cd_portabilidade'], $item['dt_inclusao']),
			anchor('ecrm/portabilidade/cadastro/'.$item['cd_portabilidade'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			array(anchor('ecrm/portabilidade/cadastro/'.$item['cd_portabilidade'], $item['nome']), 'text-align:left'),
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_portabilidade_status'].'</label>',
			$item['dt_acompanhamento'],
			array(nl2br($item['ds_portabilidade_acompanhamento']), 'text-align:justify'),
			$item['dt_agendamento_alerta']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>