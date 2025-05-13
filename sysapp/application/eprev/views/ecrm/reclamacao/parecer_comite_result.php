<?php
	$head = array( 
		'Número',
		'RE',
		'Nome',
		'Descrição',
		'Dt. Classificação',
		'Status',
		'Sem Resposta',
		'Dt. Parecer Final',
		'Parecer Final',
		'',
		'' 
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = anchor('ecrm/reclamacao/acao/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], $item['cd_reclamacao']);

		if(trim($item['fl_validar']) == 'S')
		{
			$link = anchor('ecrm/reclamacao/parecer_comite_avaliacao/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], $item['cd_reclamacao']);
		}

		$class = 'label';

		if(trim($item['fl_retorno']) == 'S')
		{
			$class = 'label label-warning';
		}
		elseif(trim($item['fl_retorno']) == 'N')
		{
			$class = 'label label-success';
		}

		$body[] = array(
			$link,
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;'),
			array(nl2br($item['descricao']), 'text-align:justify;'),
			$item['dt_classificacao'],
			'<span class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</span>',
			array(implode(br(), $item['membros']), 'text-align:left;'),
			$item['dt_parecer_final'],
			'<span class="'.$class.'">'.$item['ds_confirma'].'</span>',
			(intval($item['membros']) > 0 ? anchor('ecrm/reclamacao/dispensar_membro/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], '[dispensar membro]') : ''),
			anchor('ecrm/reclamacao/pdf/'.$item['numero'].'/'.$item['ano'].'/'.$item['tipo'], '[PDF]','target="_blank"')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>