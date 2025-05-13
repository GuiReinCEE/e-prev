<?php
$body = array();
$head = array( 
	"Data", 
	"Atividade",
	"Contato", 
	"Origem",
	"Anexos",
	"Usuário", 
	"" 
);

foreach( $collection as $item )
{
	$anexos = '';
	
	foreach($item['arr_anexo'] as $item2)
	{
		$anexos .= anchor(base_url('up/relacionamento_empresa/'.$item2['arquivo']), $item2['arquivo_nome'] , array('target' => "_blank"))."<br/>";
	}
	
	$editar  = anchor(site_url('ecrm/relacionamento_empresa/contato/'.$item['cd_empresa'].'/'.$item['cd_empresa_contato']), '[editar]');
	$excluir = '<a href="javascript:void(0)" onclick="excluir_contato('.$item["cd_empresa_contato"].')">[excluir]</a>';
	$anexo = '<a href="javascript:void(0)" onclick="ir_anexo_contato('.$item["cd_empresa_contato"].')">[anexo]</a>';

	$body[] = array( 
		$item["dt_contato"], 
		array($item["ds_empresa_contato_atividade"], 'text-align:left;'), 
		array(nl2br($item["ds_contato"]), 'text-align:left;'), 
		array(nl2br($item["ds_empresa_origem_contato"]), 'text-align:left;'), 
		array(nl2br($anexos), 'text-align:left;'), 
		array($item["nome_usuario"], 'text-align:left;'), 
		$editar.' '.$anexo.' '.$excluir
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count=FALSE;
echo $grid->render();
?>