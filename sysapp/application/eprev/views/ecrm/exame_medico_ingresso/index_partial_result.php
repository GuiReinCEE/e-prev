<?php
$body=array();
$head = array( 
	'Código',
	'RE',
	'Nome',
	'Dt Cadastro',
	'Dt Envio',
	'Enviado por',
	'Dt Recebido',
	'Recebido por'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor("ecrm/exame_medico_ingresso/detalhe/".$item["cd_exame_medico_ingresso"], $item["cd_exame_medico_ingresso"]),
	anchor("ecrm/exame_medico_ingresso/detalhe/".$item["cd_exame_medico_ingresso"],intval($item["cd_empresa"])."/".intval($item["cd_registro_empregado"])."/".intval($item["seq_dependencia"])),
	array(anchor("ecrm/exame_medico_ingresso/detalhe/".$item["cd_exame_medico_ingresso"],$item["nome"]),"text-align:left;"),
	$item["dt_inclusao"],
	$item["dt_envio_exame"],
	$item["ds_usuario_envio_exame"],
	$item["dt_recebido_exame"],
	$item["ds_usuario_recebido_exame"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
