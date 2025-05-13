<?php

$body = array();
$head = array(
	'Escolaridade',
	'Grau Percentual',
	'Nível'
);

foreach( $escoldaridade as $item )
{
	$grau = '';
	$nivel = '';

	foreach($familias_escolaridades as $item2)
	{
		if($item2['cd_escolaridade'] == $item['cd_escolaridade'])
		{
			$grau = $item2['grau_percentual'];
			$nivel = $item2['nivel'];
		}
	}

	$body[] = array(
		array(anchor("cadastro/avaliacao_familia/familia/" . $cd_familia."/".$item["cd_escolaridade"] , $item["nome_escolaridade"]),'text-align:left;'), 
		$grau,
		$nivel
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
				
?>