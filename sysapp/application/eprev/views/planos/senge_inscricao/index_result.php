<?php
$body = array();
$head = array( 
	'RE',
	'Nome',
	'Dt Inscriчуo',
	'Dt E-mail OK',
	'Dt SENGE OK',
	'Dt Documentaчуo OK'
);

foreach( $collection as $item )
{
	$body[] = array(
		$item["cd_registro_empregado"],
		array(anchor(site_url('planos/senge_inscricao/cadastro/'.$item["cd_registro_empregado"]), $item["nome"]), "text-align:left;"),
		$item["dt_inscricao"],
		$item["dt_email_confirmado"],
		$item["dt_senge_confirmado"],
		$item["dt_documentacao_confirmada"],
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>