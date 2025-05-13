<?php
#echo "<PRE>".print_r($ar_lista,true)."</PRE>";exit;

echo form_start_box("botoes_box", "Opções",true);
	echo form_default_row('', '', '
<input type="button" onclick="imprimirCertificado(\'C\');" value="Imprimir Frente e Verso" class="btn btn-mini" style="width: 180px;"> 
<input type="button" onclick="imprimirEtiqueta();" value="Imprimir Etiquetas Correios" class="btn btn-mini" style="width: 180px;">
<input type="button" onclick="protocolo();" value="Protocolo Digitalização" class="btn btn-mini" style="width: 180px;">
								   ');
/*
<input type="button" onclick="imprimirCertificado(\'F\');" value="Imprimir Só Frente" class="botao_disabled" style="width: 180px;"> 
<input type="button" onclick="imprimirCertificado(\'V\');" value="Imprimir Só Verso" class="botao_disabled" style="width: 180px;">
*/								   
echo form_end_box("botoes_box");

echo form_start_box("lista_box", "Participantes",false);
$body=array();
$head = array( 
	'<input type="checkbox"  id="checkboxCheckAll" checked onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'RE',  
	'Nome',
	'Dt Ingresso',
	''
);

foreach($ar_lista as $ar_item)
{
	$campo_check = array(
		'name'        => 'part_'.$ar_item['cd_empresa']."_".$ar_item['cd_registro_empregado']."_".$ar_item['seq_dependencia'],
		'id'          => 'part_'.$ar_item['cd_empresa']."_".$ar_item['cd_registro_empregado']."_".$ar_item['seq_dependencia'],
		'value'       => $ar_item['re_cripto'],
		'checked'     => TRUE,
		);
	$body[] = array(
			form_checkbox($campo_check),
			$ar_item['cd_empresa']."/".$ar_item['cd_registro_empregado']."/".$ar_item['seq_dependencia'],
			array($ar_item['nome'],'text-align:left;'),
			$ar_item['dt_ingresso'],
			anchor("ecrm/certificado_participante/certificadoRE/".$ar_item['cd_empresa']."/".$ar_item['cd_registro_empregado']."/".$ar_item['seq_dependencia'],'[ IMPRIMIR ]',array('title' => 'Imprimir','target' => '_blank')),
		);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = 'tabela_certificado';
$grid->head       = $head;
$grid->body       = $body;
echo $grid->render();
echo form_end_box("lista_box");

echo "<BR><BR><BR>";
?>