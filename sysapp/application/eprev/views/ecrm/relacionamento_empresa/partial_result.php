<?php
$body = array();

$head = array( 
	'Cуdigo',
	'Descriзгo',
	'Qt Colab./Assoc.',
	'Grupo', 
	'Segmento', 
	'Evento',
	'Origem',
	'Dt Contato', 
	'Qt Contatos', 
	'Qt Pessoas',
	'Cidade',
	'UF'
);

foreach( $collection as $item )
{
	$grupos    = "";
	$segmentos = "";
	$evento    = "";
	$sep       = "";
	
	foreach( $item['grupos'] as $grupo )
	{
		$grupos .= $sep.$grupo['ds_empresa_grupo'];
		$sep = ", ";
	}
	
	$sep="";
	
	foreach( $item['segmentos'] as $segmento )
	{
		$segmentos .= $sep.$segmento['ds_empresa_segmento'];
		$sep = ", ";
	}
	
	$sep="";
	
	foreach( $item['arr_evento'] as $item_evento )
	{
		$evento .= $sep.$item_evento['ds_empresa_evento'];
		$sep = ", ";
	}

	$body[] = array( 
		$item["cd_empresa"], 
		array(anchor( "ecrm/relacionamento_empresa/cadastro/".$item["cd_empresa"], $item["ds_empresa"] ),"text-align:left;"), 
		number_format($item['nr_colaborador'],0,",","."),
		array($grupos,"text-align:left;"), 
		array($segmentos,"text-align:left;"),
		array($evento,"text-align:left;"),
		$item['ds_empresa_patroc'],
		$item['dt_contato'],
		$item['tl_contato'],
		$item['tl_pessoa'],
		array($item['cidade'],"text-align:left;"),
		$item['uf']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>