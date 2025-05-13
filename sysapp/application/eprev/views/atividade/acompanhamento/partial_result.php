<?php
$body=array();
$head = array( 
	'',
	'Acompanhamento',
	'Dt Acompanhamento',
	'Dt Encerramento',
	'Previsto Próximo Mês', 
	'Responsáveis'
);

foreach( $collection as $item )
{
	$responsaveis = "";
	$sep          = "";
	
	foreach($ar_analista[$item["cd_acomp"]."-".$item["cd_projeto"]] as $analista)
	{
		$responsaveis.=$sep.$analista['analista'];
		$sep=", ";
	}

	$body[] = array(
		$item["cd_acomp"],
		array(anchor('atividade/acompanhamento/cadastro/'.$item['cd_acomp'], $item["nome"]),'text-align:left;'),
		$item["dt_acomp"],
		$item["dt_encerramento"],
		$item["mes_ano"],
		$responsaveis
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>