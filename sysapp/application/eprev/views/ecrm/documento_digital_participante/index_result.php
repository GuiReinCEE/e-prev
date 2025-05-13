<?php
	#echo "<PRE>"; print_r($collection); exit;

	$head = array(
		'Dt. Inclusão',
		'RE',
		'Nome',
		'Situação',
		'Protocolo',
		'Descrição',
		'#'
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			$item['dt_inclusao'],
			$item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia'],
			array( $item['nome'], 'text-align:left;'),
			'<span class="'.$item["cor_status"].'">'.$item["ds_status"].'</span>',
			$item['id_doc'],
			array( $item['ds_doc'], 'text-align:left;'),
			anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/".$item["id_doc"], "[consultar]", array('target' => "_blank")),
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>