<?php

if(count($indices) > 0)
{	
	$head = array(
		'M�s',
		'Mensal',
		'Acumulado'
	);

	$body = array();
	
	foreach ($collection as $item)
	{	
		$body[] = array(
			$item['meses'],
			(trim($item['ar_cota_mes']) != '' ? number_format($item['ar_cota_mes'], 2, ',', '.').' %' : ''),
			(trim($item['ar_cota_acumulada']) != '' ? number_format($item['ar_cota_acumulada'], 2, ',', '.').' %' : '')
		); 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo br();
	if(trim($dt_referencia) != '')
	{
		echo '<h2>Posi��o referente � '.$dt_referencia.'</h2>';
	}
	echo '<img src="'.base_url($imagem).'"/>';
	echo $grid->render();
}
else
{
	echo 'Nenhum registro encontrado';
}

?>