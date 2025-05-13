<?php
	$body=array();
	$head = array( 
		'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Prot. Interno',
		'Dt Envio Prot.',
		'RE',
		'Nome',
		'Dt Contato',
		'Dt Envio Cadastro',
		'Usuário'
	);	
	foreach( $collection as $item )
	{
		$campo_check = array(
			'name'  => 'prevendacontato_'.$item['cd_pre_venda_contato'],
			'id'    => 'prevendacontato_'.$item['cd_pre_venda_contato'],
			'value' => $item['cd_pre_venda_contato']
		);	
	
		$body[] = array(
			form_checkbox($campo_check),
			anchor("ecrm/cadastro_protocolo_interno/detalhe/".$item["cd_documento_recebido"], $item["nr_documento_recebido"]." "),
			$item['dt_protocolo_envio'],
			$item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia'],
			array($item['nome'],"text-align:left;"),
			$item['dt_pre_venda_contato'],
			$item['dt_envio_inscricao'],
			array($item['ds_usuario_contato'],"text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela = "tb_protocolo_interno";
	$grid->head = $head;
	$grid->body = $body;
	
	echo '
			<table border="0" align="center" cellspacing="20">
				<tr style="height: 30px;">
					<td>
						<input type="button" value="Protocolo Interno" onclick="criar_protocolo_interno();" class="btn btn-small btn-primary"" style="width: 120px;">
					</td>	
				</tr>
			</table>
		 ';	
	echo $grid->render();
?>