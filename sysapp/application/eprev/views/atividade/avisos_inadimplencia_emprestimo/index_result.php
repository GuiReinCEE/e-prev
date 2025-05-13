<?php
	#print_r($collection);

	$body = array();
	$head = array('<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	              "RE","Nome","Status","Email 1","Email 2","Dt Aviso","Dt Gerado","Dt Enviado");

	foreach($collection as $ar_item)
	{
		$campo_check = array(
			'name'        => 'part_'.$ar_item['cd_empresa']."_".$ar_item['cd_registro_empregado']."_".$ar_item['seq_dependencia'],
			'id'          => 'part_'.$ar_item['cd_empresa']."_".$ar_item['cd_registro_empregado']."_".$ar_item['seq_dependencia'],
			'value'       => $ar_item['re_cripto']
			);	
	
		$body[] = array(
			(trim($ar_item["fl_enviar"]) == "S" ? form_checkbox($campo_check) : ""),
			$ar_item["cd_empresa"]."/".$ar_item["cd_registro_empregado"]."/".$ar_item["seq_dependencia"],
			array($ar_item["nome"],'text-align:left;'),
			array($ar_item["status"],$ar_item["status_cor"]),
			$ar_item["email"],
			$ar_item["email_profissional"],
			$ar_item["dt_aviso"],
			$ar_item["dt_gerado"],
			$ar_item["dt_envio_email"]
	   );
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->id_tabela = "tabela_avisos_inadimplencia_emprestimo";
	
	echo '
			<table border="0" align="center" cellspacing="20">
				<tr style="height: 30px;">
					<td>
						<input type="button" value="Enviar Emails" onclick="enviar();" class="botao_vermelho" style="width: 120px;">
					</td>	
				</tr>
			</table>	
	     ';
	
	echo $grid->render();
	
	echo br(5);
?>
