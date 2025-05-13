<?php
$head = array( 
	'Data',
	'Gerência',
	'Usuário',
	'Tipo de Doc.',
	'Quant. de Imagem'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		anchor('ecrm/solicitacao_digitalizacao/cadastro/'.$item['cd_solicitacao_digitalizacao'], $item['dt_solicitacao_digitalizacao']),
		anchor('ecrm/solicitacao_digitalizacao/cadastro/'.$item['cd_solicitacao_digitalizacao'],$item['cd_gerencia_responsavel']),
		array(anchor('ecrm/solicitacao_digitalizacao/cadastro/'.$item['cd_solicitacao_digitalizacao'], $item['cd_usuario_responsavel']), 'text-align:left;'),
		array($item['ds_solicitacao_digitalizacao'], 'text-align:left;'),
		'<label class="badge badge-info">'.$item['nr_solicitacao_digitalizacao'].'</label>'
	);		
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>