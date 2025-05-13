<?php
$body = array();
$head = array(
	'Gerência Destino',
	'Usuário',
	''
);
	
foreach ($collection as $item)
{	
	$usuario = '';
	
	foreach($item['usuario'] as $item2)
	{
		$usuario .= $item2['usuario'].br();
	}
	
	$body[] = array(
		array(anchor('ecrm/correspondencia_recebida_grupo/cadastro/'.$item['cd_correspondencia_recebida_grupo'], $item['ds_nome']), 'text-align:left'),
		array($usuario, 'text-align:left'),
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_correspondencia_recebida_grupo'].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>