<?php
$body = array();
$head = array(
  '<input type="checkbox"  id="checkboxCheckAllProtocoloInterno" checked onclick="checkAllProtocoloInterno();" title="Clique para Marcar ou Desmarcar Todos">',
  'RE',
  'Nome',
  'Cod.',
  'Documento',
  'Arquivo'
);

foreach ($collection as $ar_item)
{
    $campo_check = array(
      'name' => 'prot_' . $ar_item['cd_empresa'] . "_" . $ar_item['cd_registro_empregado'] . "_" . $ar_item['seq_dependencia'] . "_" . $ar_item['cd_documento_recebido_item'],
      'id' => 'prot_' . $ar_item['cd_empresa'] . "_" . $ar_item['cd_registro_empregado'] . "_" . $ar_item['seq_dependencia'] . "_" . $ar_item['cd_documento_recebido_item'],
      'value' => $ar_item['re_cripto'] . md5($ar_item['cd_documento_recebido_item']),
      'checked' => TRUE,
	  
	  'nome'       => $ar_item['nome'],
	  'nome_documento' => $ar_item['nome_documento'],
	  
	  'tipo' => $ar_item['tipo'],
	  
	  'cd_tipo_doc' => $ar_item['cd_tipo_doc'],
	  'cd_documento_recebido' => $ar_item['cd_documento_recebido'],
	  'cd_documento_recebido_item' => $ar_item['cd_documento_recebido_item'],
	  'cd_empresa' => $ar_item['cd_empresa'],
	  'cd_registro_empregado' => $ar_item['cd_registro_empregado'],
	  'seq_dependencia' => $ar_item['seq_dependencia'],
	  're_cripto' => $ar_item['re_cripto'],
	  'arquivo' => $ar_item['arquivo'],
	  'arquivo_nome' => $ar_item['arquivo_nome']
    );
	
    $body[] = array(
      form_checkbox($campo_check),
      $ar_item['cd_empresa'] . "/" . $ar_item['cd_registro_empregado'] . "/" . $ar_item['seq_dependencia'],
      array($ar_item['nome'], 'text-align:left;'),
      $ar_item['cd_tipo_doc'],
      array($ar_item['nome_documento'], 'text-align:left;'),
	  (trim($ar_item['arquivo']) != "" ? anchor(base_url()."up/documento_recebido/".$ar_item['arquivo'],$ar_item['arquivo_nome'],array('title' => 'Ver','target' => '_blank')) : ""),
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tabela_protocolo_interno';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>