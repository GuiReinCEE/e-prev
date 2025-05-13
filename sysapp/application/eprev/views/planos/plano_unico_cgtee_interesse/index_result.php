<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'Status',
	'Email',
	'Telefone',
	'Telefone',
	'Contato',
	'Dt Cadastro',
	'Acompanhamento'
);

foreach( $collection as $item )
{
	$acompanhamento = '';
	
	foreach($item['arr_acompanhamento'] as $item2)
	{
		$acompanhamento .= $item2['dt_inclusao'].' : '.$item2['descricao'].br();
	}
	
	
	$body[] = array(
		anchor("planos/plano_unico_cgtee_interesse/acompanhamento/".$item["cd_plano_unico_cgtee_interesse"], $item["cd_plano_unico_cgtee_interesse"]),
		array(anchor("planos/plano_unico_cgtee_interesse/acompanhamento/".$item["cd_plano_unico_cgtee_interesse"],$item["nome"]), "text-align:left;"),
		'<span class="label '.(intval($item['tl_acompanhamento']) > 0 ? 'label-success' : 'label-important').'">'.(intval($item['tl_acompanhamento']) > 0 ? 'Contato' : 'Aguardando').'</span>',
		array($item["email"], "text-align:left;"),
		$item["telefone_1"],
		$item["telefone_2"],
		array($item["descricao"], "text-align:justify;"),
		$item["dt_inclusao"],
		array($acompanhamento, "text-align:justify;"),
	);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>