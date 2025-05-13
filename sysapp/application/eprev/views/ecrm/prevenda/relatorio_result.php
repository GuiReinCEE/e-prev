<?php
	$body=array();
	$head = array( 
		'Ano/Mês',
		'Qt de visitas',
		'Qt de pessoas',
		'Qt de contatos',
		'Qt de inscrições',
		'Qt de ingressos contatos',
		'Qt de ingressos (dt ingresso)',
		'Qt de ingressos (dt digita)'
	);
	
	foreach($collection as $ar_item)
	{	
		$body[] = array(
			$ar_item['ano'].'/'.$ar_item['mes'],
			array($ar_item['quantos_locais'],'text-align:center;','int'),
			array($ar_item['quantos_participantes'],'text-align:center;','int'),
			array($ar_item['quantos_contatos'],'text-align:center;','int'),
			array($ar_item['quantos_contatos_enviados'],'text-align:center;','int'),
			array($ar_item['quantos_contatos_ingresso'],'text-align:center;','int'),
			array($ar_item['quantos_ingresso_fceee'],'text-align:center;','int'),
			array($ar_item['quantos_digita_ingresso_fceee'],'text-align:center;','int')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->id_tabela  = 'tabela_relatorio';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();
?>
