<?php
$body = array();
$head = array(
	'Ano/Número',
	'Dt Cadastro',
	'Fundo/Carteira',
	'Status',
	'Origem',
	'Usuário Inclusão',
	'Usuário Confirmação',
	'Dt Encaminhado',
	'Dt Confirma',
	''
);

foreach ($collection as $item)
{	
	$body[] = array(
		anchor("gestao/desenquadramento_cci/cadastro/".$item["cd_desenquadramento_cci"], $item['ano_numero']),
		$item['dt_desenquadramento_cci'],
		array(anchor("gestao/desenquadramento_cci/cadastro/".$item["cd_desenquadramento_cci"],$item['ds_desenquadramento_cci_fundo']), 'text-align:left;'),
		'<span class="label '.trim($item['class_status']).'">'.$item['status'].'</span>',
		(intval($item['cd_desenquadramento_cci_pai']) > 0 ? anchor("gestao/desenquadramento_cci/cadastro/".$item["cd_desenquadramento_cci_pai"],trim($item['dt_desenquadramento_cci_pai'])) : ""),
		array($item['usuario_inclusao'], 'text-align:left;'),
		array($item['usuario_enviado'], 'text-align:left;'),
		$item['dt_encaminhado'],
		$item['dt_enviado'],
		'<a href="'.site_url('gestao/desenquadramento_cci/pdf/'.intval($item["cd_desenquadramento_cci"])).'" target="_blank">[PDF]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>