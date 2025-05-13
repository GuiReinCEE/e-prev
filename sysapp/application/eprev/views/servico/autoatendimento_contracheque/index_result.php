<?php
	$head = array( 
		'Data Referência',
		'Arquivo',
		'Tipo'
	);

	$body = array();
	
	foreach($collection as $item)
	{
		$body[] = array(
				anchor('servico/autoatendimento_contracheque/cadastro/'.$item['cd_contracheque_imagem'], $item['dt_referencia']),
				anchor('../up/contracheque_imagem/'.$item['arquivo'], $item['arquivo_nome']),
				$item['tipo']
			);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>

