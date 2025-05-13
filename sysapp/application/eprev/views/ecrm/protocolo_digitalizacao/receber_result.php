<?php

$head = array(
    'Caminho LIQUID',
	'Observações', 
    'Dt. Cadastro', 
    'Ger.',
	'<input type="checkbox"  id="checkboxCheckAllVisto" onclick="checkAllVisto();" title="Clique para Marcar ou Desmarcar Todos"> Visto', 
    'Devolver', 
    'Descartar', 
	'Observação Retorno',
	'Dt Index', 
	'Participante', 
	'Documento', 
	'Classificação da Informação',
	//'Banco',
	//'Caminho',
    'Ano',
    'ID',
	'Processo', 
	'Páginas', 
	'Arquivo');
$body = array();

foreach ($collection as $item)
{
    $documento = '';
    if($item["cd_tipo_doc"]!="")
    {
        $documento = $item["cd_tipo_doc"] . " - " . $item["nome_documento"];
    }
    else
    {
        if($item["cd_doc_juridico"]!="")
        {
            $documento = $item["cd_doc_juridico"] . ' - ' . $item["descricao_documento_juridico"];
        }
    } 
    
    $observacao = "";
    if( $item["ds_observacao_indexacao"]!="" ) 
    {
        $observacao .= $item["ds_observacao_indexacao"];
    }
    if( $item["ds_observacao_indexacao"]!="" && $item["motivo_devolucao"]!="" )
    {
        $observacao .= " - ";
    }
    if( $item["motivo_devolucao"]!="" ) 
    {
        $observacao .= $item["motivo_devolucao"];
    }
    
    $body[] = array(
        array(nl2br($item['ds_caminho_liquid']), 'text-align:justify;'),
		array(nl2br($item['observacao']), 'color:red; font-weight:bold;text-align:left;'),
      
		$item["dt_cadastro"],
	  $item["cd_gerencia_origem"],
      form_hidden('cd_documento_protocolo_item_'.$item['cd_documento_protocolo_item'], intval($item['cd_documento_protocolo_item'])).
      '<input '. ( $item["fl_recebido"]=="S" ? "checked" : '' ).' name="marcar_check_'.$item['cd_documento_protocolo_item'].'" id="visto_check_'.$item['cd_documento_protocolo_item'].'" type="checkbox" value="receber" onclick="visto(this.checked, '.$item['cd_documento_protocolo_item'].');"/>',
      '<input '.( $item["dt_devolucao"]!= ""  ? "checked" : '').' name="marcar_check_'.$item['cd_documento_protocolo_item'].'" id="devolver_check_'.$item['cd_documento_protocolo_item'].'" type="checkbox" value="devolver" onclick="devolver(this, '.$item['cd_documento_protocolo_item'].');" />',
      
	  ($item["fl_descartar"] == "S" ? array('SIM', 'text-align:center; color:red; font-weight:bold;') : array('NÃO', 'text-align:center; color:black; font-weight:bold;')),
	  
      form_input(array('name' => 'observacao_text_'.$item['cd_documento_protocolo_item'],'id' => 'observacao_text_'.$item['cd_documento_protocolo_item']), $observacao),
      form_date("dt_indexacao_".$item['cd_documento_protocolo_item'], $item['dt_indexacao']),
      $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"]."<br/>".$item['nome_participante'],
      array((trim($row['fl_contrato']) == 'S' ? $item['ds_tipo_protocolo_contrato'] : $documento), 'text-align:left'),
	  $item['id_classificacao_info_doc'],
	  //array($item["banco"], 'text-align:left'),
	  //array($item["caminho"], 'text-align:left'),
      $item["nr_ano_contrato"],
      $item["nr_id_contrato"],
      $item["ds_processo"],
      $item["nr_folha"],
      (trim($item['arquivo_nome']) != "" ? '<a href="'.base_url().'up/protocolo_digitalizacao_'.intval($item["cd_documento_protocolo"]).'/'.$item['arquivo'].'" target="_blank">'.$item['arquivo_nome'].'</a>' : "")
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela = "tbDocReceber";
$grid->head = $head;
$grid->body = $body;

if(trim($row['fl_contrato']) == 'S')
{
    //$grid->col_oculta = array(9,11,12,15);
    $grid->col_oculta = array(9,14);
}
else if(isset($row['cd_gerencia_origem']) AND (trim($row['cd_gerencia_origem']) == 'SG' OR trim($row['cd_gerencia_origem']) == 'GC'))
{
   // $grid->col_oculta = array(9,10,13,14);
	$grid->col_oculta = array(9,10,12,13);
}
else
{
    //$grid->col_oculta = array(11,12,13,14);
	$grid->col_oculta = array(12,13);
}

if(count($row) > 0)
{
    echo form_open('ecrm/protocolo_digitalizacao/salvar_receber');
        echo form_start_box("default_box", "Protocolo");
            echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));
            echo form_hidden('return', '0');
            echo form_default_row('', "Protocolo: ", '<span class="label label-inverse">'.$row['codigo']."</span>");
            echo form_default_row('', "Tipo: ", '<span class="label label-important">'.($row['tipo'] == "D" ? 'DIGITAL' : 'PAPEL')."</span>");
			echo form_default_text('', "Enviado por: ", $row['nome'], "style='width:500px;border: 0px;' readonly");
			echo form_default_text('', "Dt Envio: ", $row['dt_envio'], "style='width:500px;border: 0px;' readonly");
	
			
        echo form_end_box("default_box");

        echo form_start_box("default_box", "");
            echo form_default_date('dt_indexacao', 'Data de indexação:');
            echo form_hidden('valor', 0);
            echo form_default_row('', '', button_save("Carregar", "carregar_tl_indexados()"));
            echo form_default_row('', 'Total de indexação no dia informado:', '<b><span id="total_indexados"></span></b>');
            echo form_default_row('', 'Total de ítens recebidos deste protocolo:', $total_indexados['quantos']);
            echo form_default_row('', 'Total de ítens devolvidos deste protocolo:', $total_devolvidos['quantos']);
            echo form_default_row('', 'Total de ítens deste procolo:', '<b><span id="total">'.count($collection).'</span></b>');
        echo form_end_box("default_box");

        echo form_start_box('grid_documentos', 'Documentos Adicionados', false);
            echo $grid->render();
        echo form_end_box('grid_documentos', false);
        echo form_command_bar_detail_start();
            if($row['tipo'] == 'D')
            {
                echo button_save("Download Arquivos", "download(".$row['cd_documento_protocolo'].")", 'botao_amarelo');
            }
            echo button_save("Salvar", "salvar()");
            echo button_save("Salvar e Confirmar", "salvar_confirmar()", 'botao_verde');
            echo button_save("Devolver Protocolo", "devolverTudo()", 'botao_vermelho');
        echo form_command_bar_detail_end();
    echo form_close();
}
else
{
    echo 'Protocolo ainda não foi enviado.';
}


?>