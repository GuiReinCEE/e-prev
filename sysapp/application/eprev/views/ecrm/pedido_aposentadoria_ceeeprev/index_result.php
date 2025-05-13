<?php
	$head = array( 
		'Cód.',
		'RE',
		'Nome Participante',
		'Tipo de Solicitação',
		'Dt. Encaminhamento',
		'Status',
		'Dt. Análise.',
		'Dt. Assinatura',
		'Dt. Deferido',
		'Dt. Indeferido'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$item['cd_pedido_aposentadoria_ceeeprev'], $item['cd_pedido_aposentadoria_ceeeprev']),
			anchor('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$item['cd_pedido_aposentadoria_ceeeprev'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			anchor('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$item['cd_pedido_aposentadoria_ceeeprev'], $item['ds_nome']),
			$item['ds_pedido_aposentadoria'],
			$item['dt_encaminhamento'],
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
			$item['dt_analise'],
			$item['dt_assinatura'],
			$item['dt_deferido'],
			$item['dt_indeferido']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>