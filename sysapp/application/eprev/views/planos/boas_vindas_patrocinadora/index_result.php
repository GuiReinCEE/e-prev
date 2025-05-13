<?php
$body = array();
$head = array(
	'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'EMP/RE/SEQ', 
	'Nome', 
	'Email',
	'Email',
	'Eletrônico',
	'Ingresso', 
	'Inscrição', 
	'Certificado', 
	'Enviado', 
	'Dt Geração', 
	'Dt Envio Email'
);

foreach ($collection as $item)
{
	$campo_check = array(
		'name'  => 'part_'.$item['cd_empresa']."_".$item['cd_registro_empregado']."_".$item['seq_dependencia'],
		'id'    => 'part_'.$item['cd_empresa']."_".$item['cd_registro_empregado']."_".$item['seq_dependencia'],
		'value' => $item['re_cripto']
	);	

    $body[] = array(
		(((trim($item["fl_enviar"]) == "S") AND (trim($item["fl_email"]) == "S")) ? form_checkbox($campo_check) : ""),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"], "text-align:left;"),
		array($item["email"].(((trim($item["email"]) != "") and (trim($item["email_profissional"]) != "")) ? br() : "").$item["email_profissional"], "text-align:left;"),
		'<span class="label label-'.(trim($item["fl_email"]) == 'S' ? "success" : 'important').'">'.(trim($item["fl_email"]) == "S" ? 'Sim' : 'Não').'</span>',
		'<span class="label '.(trim($item["fl_eletronico"]) == 'I' ? "label-success" : '').'">'.(trim($item["fl_eletronico"]) == "I" ? 'Sim' : 'Não').'</span>',
		$item["dt_ingresso"],
		$item["dt_inscricao"],
		$item["dt_certificado"],
		'<span class="label label-'.(trim($item["dt_envio_email"]) != '' ? "success" : 'important').'">'.(trim($item["dt_envio_email"]) != "" ? 'Sim' : 'Não').'</span>',
		$item["dt_gerado"],
		$item["dt_envio_email"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->id_tabela = "tabela_boas_vindas";

echo '
	<table border="0" align="center" cellspacing="20">
		<tr style="height: 30px;">
			<td>
				<input type="button" onclick="enviar();" value="Enviar Email Boas Vindas" class="btn btn-danger btn-small">
			</td>	
		</tr>
	</table>'.
	$grid->render();


?>