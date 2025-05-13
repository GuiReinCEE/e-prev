<?php
$body = array();
$head = array( 
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

foreach( $collection as $item )
{	
    $body[] = array(
		$item['dt_cadastro'],
		array(anchor(base_url().'up/acompanhamento_wbs/'.$item['ds_arquivo_fisico'], $item['ds_arquivo'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_acompanhamento_wbs'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>