<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'CPF',
	'Dt Cadastro',
	'Status',
	'Email',
	'Telefone',
	'Telefone',
	'Contato',
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
		anchor("planos/senge_precadastro/acompanhamento/".$item["cd_senge_precadastro"], $item["cd_senge_precadastro"]),
		array(anchor("planos/senge_precadastro/acompanhamento/".$item["cd_senge_precadastro"],$item["nome"]), "text-align:left;"),
		$item["cpf"],
		$item["dt_inclusao"],
		'<span class="label '.(intval($item['tl_acompanhamento']) > 0 ? 'label-success' : 'label-important').'">'.(intval($item['tl_acompanhamento']) > 0 ? 'Contato' : 'Aguardando').'</span>',
		array($item["email"], "text-align:left;"),
		$item["telefone_1"],
		$item["telefone_2"],
		array($item["descricao"], "text-align:justify;"),
		
		array($acompanhamento, "text-align:justify;"),
	);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>