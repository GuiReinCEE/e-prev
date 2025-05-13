<?php

#echo "<PRE>".print_r($ar_lista,true)."</PRE>";exit;

$arr_order[] = array('text' => 'RE', 'value' => "(TO_CHAR(p.cd_empresa,'FM00') || '-' || TO_CHAR(p.cd_registro_empregado,'FM000000') || '-' || TO_CHAR(p.seq_dependencia,'FM00'))");
$arr_order[] = array('text' => 'Nome', 'value' => 'p.nome');
$arr_order[] = array('text' => 'Cód Documento', 'value' => 'cd_documento');

$arr_tipo_order[] = array('text' => 'Ascendente', 'value' => 'ASC');
$arr_tipo_order[] = array('text' => 'Descendente', 'value' => 'DESC');

$ar_ingresso[] = array('text' => 'Sim', 'value' => 'S');
$ar_ingresso[] = array('text' => 'Não', 'value' => 'N');

$config['callback_buscar'] = 'carrega_protocolo_interno($("#cd_tipo_doc").val())';

echo form_start_box("protocolo_box", "Protocolo Interno", true);
	echo form_default_integer('nr_ano', 'Ano:', "", 'maxlenght="4"');
	echo form_default_integer('nr_contador', "Número:", "");
	echo form_default_row('', '', '<input type="button" onclick="carrega_protocolo_interno();" value="Listar" class="botao" >');
	echo form_default_row('lista_protocolo_interno', '', '<div id="result_protocolo_interno"></div>');
	echo form_default_row('btn_add_protocolo_interno', '', '<input type="button" onclick="adcionar_protocolo_interno();" value="Adicionar" class="botao" id="add_protocolo_interno" style="display:none">');
echo form_end_box("protocolo_box");
echo form_start_box("protocolo_documento_box", "Documento", true);
	echo form_default_text('cod_documento', 'Cód. Documento :', '');
	echo form_default_row('', '', '<input type="button" onclick="check_documento(true)" value="Marcar" class="botao" > <input type="button" onclick="check_documento(false)" value="Desmarcar" class="botao_vermelho" >');

echo form_end_box("protocolo_documento_box");
echo form_open('certificado_participante/protocolo_gerar/', 'name="gerar_protocolo"');
echo form_start_box("order_box", "Opções", true);
	echo form_default_dropdown('fl_ingresso_f', 'Dt Ingresso:*', $ar_ingresso, array('N'));
	echo form_default_dropdown('fl_ordenacao_1_f', 'Ordenação 1:*', $arr_order);
	echo form_default_dropdown('fl_tipo_order_1_f', 'Tipo Ordenação 1:*', $arr_tipo_order);
	echo form_default_dropdown('fl_ordenacao_2_f', 'Ordenação 2:*', $arr_order);
	echo form_default_dropdown('fl_tipo_order_2_f', 'Tipo Ordenação 2:*', $arr_tipo_order);
	echo form_default_row('', '', '<input type="button" onclick="protocoloGerar();" value="Gerar Protocolo Digitalização" class="botao" style="width: 180px;">');
echo form_end_box("order_box");

echo form_close();

echo form_start_box("lista_box", "Participantes", false);
$body = array();
$head = array(
  '<input type="checkbox"  id="checkboxCheckAll" checked onclick="checkAllProtocolo();" title="Clique para Marcar ou Desmarcar Todos">',
  'RE',
  'Nome',
  'Cod.',
  'Documento',
  'Tipo'
);

foreach ($ar_lista as $ar_item)
{
    $campo_check = array(
      'name' => 'prot_' . $ar_item['cd_empresa'] . "_" . $ar_item['cd_registro_empregado'] . "_" . $ar_item['seq_dependencia'] . "_" . $ar_item['cd_documento'],
      'id' => 'prot_' . $ar_item['cd_empresa'] . "_" . $ar_item['cd_registro_empregado'] . "_" . $ar_item['seq_dependencia'] . "_" . $ar_item['cd_documento'],
      'value' => $ar_item['re_cripto'] . md5($ar_item['cd_documento']),
      'checked' => TRUE,
	  
	  'onclick' => "clickCheck('prot_". $ar_item['cd_empresa'] . "_" . $ar_item['cd_registro_empregado'] . "_" . $ar_item['seq_dependencia'] . "_" . $ar_item['cd_documento']."');",
	  
	  'idunico' => $ar_item['idunico'],
	  'tipo' => $ar_item['tipo'],
	  'fl_verificar' => $ar_item['fl_verificar'],
	  
	  'cd_tipo_doc' => $ar_item['cd_documento'],
	  'cd_documento_recebido' => "",
	  'cd_documento_recebido_item' => "",
	  'cd_empresa' => $ar_item['cd_empresa'],
	  'cd_registro_empregado' => $ar_item['cd_registro_empregado'],
	  'seq_dependencia' => $ar_item['seq_dependencia'],
	  're_cripto' => $ar_item['re_cripto'],
	  'arquivo' => "",
	  'arquivo_nome' => ""

	   
    );
    $body[] = array(
      form_checkbox($campo_check),
      $ar_item['cd_empresa'] . "/" . $ar_item['cd_registro_empregado'] . "/" . $ar_item['seq_dependencia'],
      array($ar_item['nome'], 'text-align:left;'),
      $ar_item['cd_documento'],
      array($ar_item['ds_documento'], 'text-align:left;'),
	  ($ar_item['tipo'] == "D" ? '<span class="label label-important">DIGITAL</span>' : '<span class="label label-success">PAPEL</span>')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = 'tabela_protocolo';
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo form_end_box("lista_box");

echo "<BR><BR><BR>";
?>