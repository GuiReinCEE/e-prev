<?php
	#echo "<PRE>"; print_r($collection); exit;

	$head = array(
		'C�d',
		'Tipo',
		'Assinado',
		'Dt. Assinatura'
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			$item['cd_contrato_digital_assinatura'],
			array($item['ds_tp_assinatura'], 'text-align:left;'),
			'<span class="'.($item["fl_assinatura"] == "S" ? "label label-success" : "label label-important").'">'.($item["fl_assinatura"] == "S" ? "Sim" : "N�o").'</span>',
			$item['dt_assinatura']
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>