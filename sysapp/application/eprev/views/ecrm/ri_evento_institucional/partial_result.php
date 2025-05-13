<?php
$body=array();
$head = array( 
	'Cód.',  
	'Nome', 
	'Data',
    'Qt Inscrito',	
    'Qt Presente',	
    '% Presente',	
	'Cidade', 
	'Local'
);

foreach($collection as $item)
{
	$pr_presente = 0;
	if((intval($item["qt_inscrito"]) > 0) and (intval($item["qt_presente"]) > 0))
	{
		$pr_presente = (intval($item["qt_presente"]) * 100) / intval($item["qt_inscrito"]);
	}
	
	$body[] = array(
	    anchor("ecrm/ri_evento_institucional/detalhe/".$item["cd_evento"], $item["cd_evento"]),
	    array(anchor("ecrm/ri_evento_institucional/detalhe/" . $item["cd_evento"], $item["nome"]),'text-align:left;'),
		$item["dt_inicio"],
		'<span class="label label-info">'.$item["qt_inscrito"].'</span>',
		'<span class="label label-success">'.$item["qt_presente"].'</span>',
		array(progressbar(intval($pr_presente)),'text-align:left;'),
		array($item["nome_cidade"],'text-align:left;'),
		array($item["local_evento"],'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>