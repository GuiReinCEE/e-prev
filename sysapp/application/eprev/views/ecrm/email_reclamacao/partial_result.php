<?php
$body=array();
$head = array( 
	'Programa', 'Gerência', 'Email', ''
);

foreach( $collection as $item )
{
	$excluir = button_delete("ecrm/email_reclamacao/excluir",$item["cd_atendimento_programa_gerencia"]);
	$body[] = array(  $item["ds_programa"], $item["cd_divisao"], $item["ds_usuario"], $excluir  );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
