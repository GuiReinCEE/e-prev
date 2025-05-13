<?php



$body=array();
$head = array( 
	'EMP/RE/SEQ',
	'Data Gravação',
	'Gravação'
);

foreach( $collection as $item )
{
	
	if($item['tp_arquivo'] == "XCALLY")
	{
		$gravacao='<a href="'.site_url("ecrm/atendimento_lista/gravacaoXcally/".$item['cd_atendimento']).'" title="Clique para ouvir a Gravação" target="_blank">[Ouvir]</a>';
	}
	else
	{
		$gravacao='<a href="'.pasta_gravacao().trim($item['nome_arquivo']).'" title="Clique para ouvir a Gravação" target="_blank">[Ouvir]</a>';
	}	
	
	$body[] = array(
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	$item["dt_gravacao"],
	$gravacao
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
