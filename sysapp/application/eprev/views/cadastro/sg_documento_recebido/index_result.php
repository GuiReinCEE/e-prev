<?php
	$head = array( 
		'Ano/Número', 
		'Data', 
		'Remetente', 
		'Destino'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array( 
		    anchor('cadastro/sg_documento_recebido/cadastro/'.$item['ano'].'/'.$item['numero'], $item['ano_numero']), 
			$item['datahora'],
			array($item['remetente'], 'text-align:left'), 
			array($item['destino_nome'], 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>
