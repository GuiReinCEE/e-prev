<?php
	#echo "<PRE>"; print_r($collection); exit;

	$head = array(
		'Cód',
		'RE',
		'Nome',
		'Situação',
		'Dt. Inclusão',
		'Dt. Limite',
		'Dt. Concluído',
		'Dt. Cancelado',
		'Dt. Finalizado',
		'Cód Liquid'
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			$item['cd_contrato_digital'],
			$item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia'],
			array(anchor('ecrm/contrato_digital/cadastro/'.$item['cd_contrato_digital'], $item['nome_participante']), 'text-align:left;'),
			'<span class="'.$item["situacao_label"].'">'.$item["situacao"].'</span>',
			$item['dt_inclusao'],
			$item['dt_limite'],
			$item['dt_concluido'],
			$item['dt_cancelado'],
			$item['dt_finalizado'],
			$item['cd_liquid']
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>