<?php
	$head = array(
		'Cód. Rentenção',
		'Cód. Atendimento',
		'Retido',
		'RE',
		'Nome',
		'Dt. Inclusão',
		'Usuário',
		'Acompanhamento'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('ecrm/atendimento_retencao/cadastro/'.$item['cd_atendimento_retencao'], $item['cd_atendimento_retencao']),
			anchor('ecrm/atendimento_retencao/cadastro/'.$item['cd_atendimento_retencao'], $item['cd_atendimento']),
			(trim($item['fl_retido']) != '' ? img(array('src' => './img/retencao_'.$item['fl_retido'].'.png')) : ''),
			anchor('ecrm/atendimento_retencao/cadastro/'.$item['cd_atendimento_retencao'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			array($item['nome'], 'text-align: left;'),
			$item['dt_inclusao'],
			array($item['ds_usuario_inclusao'], 'text-align: left;'),
			array(nl2br($item['ds_acompanhamento']), 'text-align: justify')
		); 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo '<input type="hidden" id="qt_anterior" value="'.$qt_anterior.'">';
	echo $grid->render();
?>