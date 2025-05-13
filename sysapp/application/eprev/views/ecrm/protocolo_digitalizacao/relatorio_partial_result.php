<?php
$body = array();
$head = array(
  'Obs Digitalização',
  'Protocolo',
  'Ger.',
  'Observação',
  'Cadastro',
  'Envio',
  'Usuário Envio',
  'Recebimento',
  'Usuário Receb.',
  'Indexação',
  'Devolução',
  'Motivo devolução',
  'RE',
  'Participante',
  'Doc',
  'Tipo de documento',
  'Banco',
  'Caminho',
  'ID',
  'Páginas',
  'Descartar',
  'Prazo de Guarda',
  'Classificação da Informação',
  'Processo',
  'Arquivo'
);
$fl_obs_digitalizacao = true;
foreach ($collection as $item)
{
    if(($fl_obs_digitalizacao == true) and (trim($item['ds_observacao_indexacao']) != ""))
	{
		$fl_obs_digitalizacao = false;
	}

	$caminho = $item['caminho'];

	if(trim($item['ds_caminho_liquid']) != '')
	{
		$caminho = $item['ds_caminho_liquid'];
	}
	
    $body[] = array(
		array(nl2br($item['ds_observacao_indexacao']), 'color:red; font-weight:bold;text-align:left;'),
		($permissao_digitalizacao ? anchor('ecrm/protocolo_digitalizacao/editar_indexado/'.$item['cd_documento_protocolo'], $item['nr_protocolo'] . ' - ' . $item['tipo']) : $item['nr_protocolo'] . ' - ' . $item['tipo']),
		$item['cd_gerencia_origem'],
		array($item['observacao'], 'text-align:left;'),
		$item['dt_cadastro'],
		$item['dt_envio'],
		$item['nome_usuario_envio'] . " (" . $item['divisao_usuario_envio'] . ")",
		$item['dt_ok'],
		(trim($item['nome_usuario_ok']) != '' ? $item['nome_usuario_ok'] . " (" . $item['divisao_usuario_ok'] . ")" : ''),
		$item['dt_indexacao'],
		$item['dt_devolucao'],
		$item['motivo_devolucao'],
		$item['cd_empresa'] . '/' . $item['cd_registro_empregado'] . '/' . $item['seq_dependencia'],
		array($item["nome_participante"], "text-align:left;"),
		(($item['cd_tipo_doc']) ? $item['cd_tipo_doc'] : $item['cd_doc_juridico']),
		array((($item['nome_documento']) ? $item['nome_documento'] : $item['nome_documento_juridico']), "text-align:left;"),
		array($item['banco'], 'text-align:left;'),
		array($caminho, 'text-align:left;'),
		$item['nr_id_contrato'],
		$item['nr_folha'],
		$item['fl_descartar'],
		$item['ds_tempo_descarte'],
		$item['id_classificacao_info_doc'],
		$item['ds_processo'],
		(trim($item['arquivo']) != "" ? '<a href="' . base_url() . 'up/protocolo_digitalizacao_' . $item['cd_documento_protocolo'] . '/' . $item['arquivo'] . '" target="_blank">' . $item['arquivo'] . '</a>' : "")
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

if(trim($ds_mes_ano_indicador) != '')
{
	echo form_start_box('default_box', 'Info Indicador - Documentos Disponibilizados Fora do Prazo');
		echo form_default_row('', 'Qt Documentos Recebidos:', $qt_doc_recebido);
		echo form_default_row('', 'Qt Documentos Fora do Prazo:', $qt_doc_fora);
	echo form_end_box('default_box');
}

echo $grid->render();
?>