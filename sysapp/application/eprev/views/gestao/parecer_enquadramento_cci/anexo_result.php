<?php
$body=array();
$head = array( 
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{
	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_parecer_enquadramento_cci_anexo'].')">[excluir]</a>';
	
    $body[] = array(
		$item['dt_inclusao'],
		array(anchor(base_url().'up/parecer_enquadramento_cci/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
		array($item['nome'], "text-align:left;"),
		($fl_salvar ? $excluir : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>