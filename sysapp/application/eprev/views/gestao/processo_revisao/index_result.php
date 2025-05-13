<?php
	$head = array(
	    'Procedimento',
	    'Dt. Refer�ncia',
	    'Dt. Limite',
	    'Dt. Revis�o',
	    'Usu�rio Revis�o',
	    'Altera��o no Processo',
	    'Observa��o'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$body[] = array(
			array(anchor('gestao/processo/revisao_cadastro/'.$item['cd_processo_revisao'], $item['procedimento']), 'text-align:left;'),
			anchor('gestao/processo/revisao_cadastro/'.$item['cd_processo_revisao'], $item['dt_referencia']),
			$item['dt_limite'],
			$item['dt_revisao'],
			array($item['usuario_revisao'], 'text-align:left;'),
			'<label class="'.$item['class_alterado'].'">'.$item['alterado'].'</label>',
			array(nl2br($item['observacao']), 'text-align:justify;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>