<?php
$body = array();
$head = array(
	'Dt. Simulaчуo',
	'Nome',
	'Telefone ',
	'E-mail',
	'Acompanhamento '
);

foreach ($collection as $item)
{	
	$body[] = array(
		anchor('planos/simulacao_site_senge/simulacao/'.$item['cd_simulacao_site'],$item["dt_simulacao"]),
		array(anchor('planos/simulacao_site_senge/simulacao/'.$item['cd_simulacao_site'],$item["nome"]), "text-align:left;"),
		$item["telefone"],
		array($item["email"],"text-align:left;"),
		array(nl2br($item['acompanhamento']),"text-align:left;"),
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>