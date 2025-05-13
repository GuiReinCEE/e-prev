<?php

$head = array(
    'Dt. Cadastro', 
    '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">', 
    'Descartar',
    'Data Indexação', 
    'Observações',
    'Caminho LIQUID',  
    'Participante', 
    'Documento', 
    'Prazo de Guarda',
	'Classificação da Informação',
    'Processo', 
    'Páginas'
);

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
      $item["dt_cadastro"],
      form_hidden('cd_documento_protocolo_item_' . $item['cd_documento_protocolo_item'], intval($item['cd_documento_protocolo_item'])) .
      '<input  name="marcar_check_' . $item['cd_documento_protocolo_item'] . '"id="visto_check_' . $item['cd_documento_protocolo_item'] . '" 
          type="checkbox" value="receber" onclick="marcar(this, ' . $item['cd_documento_protocolo_item'] . ');"/>',
      ($item["fl_descartar"] == "S" ? array('SIM', 'text-align:center; color:red; font-weight:bold;') : array('NÃO', 'text-align:center; color:black; font-weight:bold;')),
      form_date("dt_indexacao_" . $item['cd_documento_protocolo_item'], $item['dt_indexacao']),
      form_input(array('name' => 'observacao_text_' . $item['cd_documento_protocolo_item'],
        'id' => 'observacao_text_' . $item['cd_documento_protocolo_item']), $observacao),
      array(nl2br($item['ds_caminho_liquid']), 'text-align:justify;'),
      $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"],
      array($documento, 'text-align:left'),
      $item['ds_tempo_descarte'],
	  $item['id_classificacao_info_doc'],
      $item["ds_processo"],
      $item["nr_folha"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

if(count($row) > 0)
{
    echo form_open('ecrm/protocolo_digitalizacao/salvar_indexar');
        echo form_start_box("default_box", "Protocolo");
            echo form_hidden('cd_documento_protocolo', intval($row['cd_documento_protocolo']));
            echo form_hidden('return', '0');
            echo form_default_text('', "Código: ", $row['codigo'], "style='width:500px;border: 0px;' readonly");
            echo form_default_text('', "Enviado por: ", $row['nome'], "style='width:500px;border: 0px;' readonly");
            echo form_default_text('', "Tipo: ", ($row['tipo'] == "D" ? 'Digital' : 'Papel'), "style='width:500px;border: 0px;' readonly");
        echo form_end_box("default_box");
        
        echo form_start_box("default_box", "");
            echo form_default_date('dt_indexacao', 'Data de indexação:');
            echo form_hidden('valor', 0);
            echo form_default_row('', '', button_save("Carregar", "carregar_tl_indexados()"));
            echo form_default_row('', 'Total de indexação no dia informado:', '<b><span id="total_indexados"></span></b>');
            echo form_default_row('', 'Total de ítens devolvidos deste protocolo:', $total_devolvidos['quantos']);
            echo form_default_row('', 'Total de ítens listados abaixo (não devolvidos): ', '<b><span id="total">'.count($collection).'</span></b>');
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
        echo form_command_bar_detail_end();
    echo form_close();
}
else
{
    echo 'Protocolo ainda não foi enviado.';
}

?>