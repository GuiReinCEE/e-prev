<?php
$head = array( 
	'Data',
	'Descriчуo'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		$item['dt_calendario_folha_pagamento'],
		array(nl2br(anchor('ecrm/calendario_folha_pagamento/cadastro/'.$item['cd_calendario_folha_pagamento'], $item['ds_calendario_folha_pagamento'])), 'text-align:justify;')
	);		
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>