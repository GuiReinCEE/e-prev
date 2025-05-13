<?php
	$head = array(
		'Cen�rio',
		'Ger�ncia Resp.',
		'Plano de A��o',
		'Dt. Prazo Previsto',
		'Dt. Verifica��o da Efic�cia',
		'Dt. Valida��o da Efic�cia',
		'Acompanhamento'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			array(anchor('gestao/cenario_plano_acao/cadastro/'.$item['cd_cenario_plano_acao'], $item['cd_cenario'].'-'.$item['titulo']), 'text-align:left;'),
			$item['cd_gerencia_responsavel'],
		    array(nl2br($item['ds_cenario_plano_acao']), 'text-align:justify;'),
		    $item['dt_prazo_previsto'],
		    $item['dt_verificacao_eficacia'],
		    $item['dt_validacao_eficacia'],
		    $item['ds_acompanhamento']
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>