<?php
	$head = array( 
		'Ano/Número',
		'Documento',
		'Arquivo',
		'Responsável',
		'Substituto',
		'Dt. Prazo',
		'Dt. Envio',
		'Dt. Respondido',
		'Status',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('cadastro/protocolo_sg/cadastro/'.$item['cd_protocolo_sg'], $item['ano_numero']),
			array(anchor('cadastro/protocolo_sg/cadastro/'.$item['cd_protocolo_sg'], $item['ds_protocolo_sg']), 'text-align:left;'),
			array(anchor(base_url().'up/protocolo_sg/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			array($item['cd_gerencia_responsavel'].' - '.$item['ds_usuario_responsavel'], 'text-align:left;'),
			array((trim($item['cd_gerencia_substituto']) != '' ? $item['cd_gerencia_substituto'].' - ' : '').$item['ds_usuario_substituto'], 'text-align:left;'),
			'<span class="'.$item['class_prazo'].'">'.$item['dt_prazo'].'</span>',
			$item['dt_envio'],
			$item['dt_respondido'],
			'<span class="'.$item['class_status'].'">'.$item['status'].'</span>',
			(trim($item['dt_envio']) == '' 
				? '<a href="javascript:void(0)" onclick="enviar('.$item['cd_protocolo_sg'].')">[enviar]</a> <a href="javascript:void(0)" onclick="excluir('.$item['cd_protocolo_sg'].')">[excluir]</a>' 
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