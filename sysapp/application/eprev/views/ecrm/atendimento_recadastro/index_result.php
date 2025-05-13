<?php
$body = array();
$head = array( 
	'RE',
	'Nome',
	'Observaчѕes',
	'Serviчo Social',
	'Telefones',
	'Usuсrio',
	'Dt Inclusуo',
	'Dt Alteraчуo',
	'Dt Cancelamento',
	''
);

foreach( $collection as $item )
{
	$telefone = '';
	
	if(trim($item["telefone"]) != '')
	{
		$telefone = $item["ddd"].'-'.$item["telefone"];
	}
	
	if(trim($item['telefone_outro']) != '')
	{
		if(trim($telefone) != '')
		{
			$telefone .= "\n";
		}
		
		$telefone .= $item["ddd_outro"].'-'.$item["telefone_outro"];
	}
	
	$body[] = array(
		anchor("ecrm/atendimento_recadastro/cadastro/".$item["cd_atendimento_recadastro"], $item["cd_empresa"].'/'.$item["cd_registro_empregado"].'/'.$item["seq_dependencia"]),
		array(anchor("ecrm/atendimento_recadastro/cadastro/".$item["cd_atendimento_recadastro"], $item["nome"]),  'text-align:left'),
		array($item['observacao'], 'text-align:justify'),
		array($item['servico_social'], 'text-align:justify'),
		$telefone,
		$item["nome_gap"],
		$item["dt_criacao"],
		(trim($item["dt_atualizacao"]) !='' ? $item["dt_atualizacao"].' por '.$item["nome_usuario_atualizacao"]  : '' ),
		array($item["dt_cancelamento"], 'text-align:center;'.(trim($item['dt_cancelamento']) != '' ? 'color:red; font-weight:bold;' : '')),
		(trim($item["dt_cancelamento"]) == '' ? anchor("ecrm/atendimento_recadastro/cancelar/".$item["cd_atendimento_recadastro"], '[cancelar]') : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>