<?php
$body = array();
$head = array(
	'Código',
	'Nome',
	'Usuário',
	'Empresa',
	'Email',
	'Telefone 1',
	'Telefone 2',
	'CPF'
);

foreach ($collection as $item)
{
	$body[] = array(		
		$item['cd_usuario'],
		array(anchor(site_url('ecrm/usuario/cadastro/'.intval($item['cd_usuario'])),$item['nome']),'text-align:left'),
		array($item['usuario'],'text-align:left'),
		array($item['sigla'],'text-align:left'),
		$item['email'],
		$item['telefone_1'],
		$item['telefone_2'],
		$item['cpf']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>