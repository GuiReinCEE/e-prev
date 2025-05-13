<?php
echo form_hidden('qt_total', count($collection));

$head = array('Obs Digitalização','Caminho LIQUID', 'RE', 'Nome', 'Documento', 'Dt Cadastro', 'Observação', 'Descartar', 'Páginas', 'Arquivo', '','Usuário');
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
		array(nl2br($item['ds_caminho_liquid']), 'text-align:justify'),
		$item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia'],
		array($item['nome'], 'text-align:left;'),
		array($item["cd_tipo_doc"] . " - " . $item['descricao_documento'], 'text-align:left;'),
		$item['dt_cadastro'],
		$item['observacao'],
		($item['fl_descartar'] == 'Sim' ? '<span style="color:red; font-weight:bold;">' . $item['fl_descartar'] . '</span>' : $item['fl_descartar']),
		$item['nr_folha'],
		(trim($item['arquivo']) != "" ? '<a href="' . base_url() . 'up/protocolo_digitalizacao_' . $item['cd_documento_protocolo'] . '/' . $item['arquivo'] . '" target="_blank">' . $item['arquivo'] . '</a>' : ""),
		(($item['cd_usuario_cadastro'] == usuario_id() && $item['dt_envio'] == '') ? comando('editar_button', 'Editar', 'editar_documento(' . $item['cd_documento_protocolo_item'] . ')')
		. " " . comando('excluir_button', 'Excluir', 'excluir_documento(' . $item['cd_documento_protocolo_item'] . ')', array('class' => 'botao_vermelho')) : ''),
		$item['nome_usuario_cadastro']
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