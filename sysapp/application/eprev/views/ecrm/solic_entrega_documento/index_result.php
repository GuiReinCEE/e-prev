<?php
	$head = array(
		'Tipo de Documento',
		'Data',
	    'Horário Inicial',
		'Horário Limite',
		'Prioridade',
		'Dt. Solicitação',
		'Usuário',
		'Dt. Recebimento',
		'Usuário',
		'Status',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		array(anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['ds_solic_entrega_documento_tipo']), 'text-align:left'),
			anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['data_ini']),
			anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['hr_ini']), 
			anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['hr_limite']), 
			'<span class="'.trim($item['ds_class_prioridade']).'">'.$item['ds_prioridade'].'</span>', 
			anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['dt_inclusao']),
			array(anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['ds_usuario_inclusao']), 'text-align:left'),
			anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['dt_recebido']),
			array(anchor('ecrm/solic_entrega_documento/cadastro/'.$item['cd_solic_entrega_documento'], $item['ds_usuario_recebido']), 'text-align:left'),
			'<span class="'.trim($item['ds_class_status']).'">'.$item['ds_status'].'</span>', 
			(trim($item['dt_recebido']) == '' 
				? anchor('ecrm/solic_entrega_documento/pdf/'.$item['cd_solic_entrega_documento'], '[pdf]', 'target="_blank"').
				  ' <a href="javascript:void(0)" onclick="receber('.$item['cd_solic_entrega_documento'].')">[recebido]</a>' 
				: ''
			)
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	