<?php
echo form_hidden('qt_total', count($collection));

$head = array('Obs Digitaliza��o', 'Tipo Protocolo', 'Caminho LIQUID','Ano', 'Descri��o',  'ID', 'Usu�rio', 'Data', 'Descartar', 'P�ginas', 'Arquivo', '');
$body = array();
$fl_obs_digitalizacao = true;
foreach ($collection as $item)
{
    if(($fl_obs_digitalizacao == true) and (trim($item['ds_observacao_indexacao']) != ""))
	{
		$fl_obs_digitalizacao = false;
	}
	
    $body[] = array(
		array(nl2br($item['ds_observacao_indexacao']), 'color:red; font-weight:bold;text-align:left;'),
		$item['ds_tipo_protocolo_contrato'],
		array(nl2br($item['ds_caminho_liquid']), 'text-align:justify'),
		$item['nr_ano_contrato'],
		array(nl2br($item['observacao']), 'text-align:justify;'),
		$item['nr_id_contrato'],
		$item['nome_usuario_cadastro'],
		$item['dt_cadastro'],
		($item['fl_descartar'] == 'Sim' ? '<span style="color:red; font-weight:bold;">' . $item['fl_descartar'] . '</span>' : $item['fl_descartar']),
		$item['nr_folha'],
		(trim($item['arquivo']) != "" ? '<a href="' . base_url() . 'up/protocolo_digitalizacao_' . $item['cd_documento_protocolo'] . '/' . $item['arquivo'] . '" target="_blank">' . $item['arquivo'] . '</a>' : ""),
		(($item['cd_usuario_cadastro'] == usuario_id() && $item['dt_envio'] == '') ? comando('editar_button', 'Editar', 'editar_documento(' . $item['cd_documento_protocolo_item'] . ')')
		  . " " . comando('excluir_button', 'Excluir', 'excluir_documento(' . $item['cd_documento_protocolo_item'] . ')', array('class' => 'botao_vermelho')) : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

if($fl_obs_digitalizacao)
{
	$grid->col_oculta = array(0);
}

echo $grid->render();
?>