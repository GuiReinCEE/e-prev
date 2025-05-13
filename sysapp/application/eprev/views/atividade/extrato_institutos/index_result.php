<?php
	$body = array();
	$head = array(
		'<input type="checkbox" id="checkboxCheckAll" onclick="check_all();" title="Clique para Marcar ou Desmarcar Todos">',
		'Empresa/Instituidor',
		'RE',
		'CEEE-G',
		'Nome',
		'Eletrônico',
		'Status',
		'Email 1',
		'Email 2',
		'Dt. Emissão Extrato',
		'Dt. Limite Extrato',
		'Dt. Documento',
		'Dt. Receb. Extrato',
		'Dt. Enviado',
		''
	);

	foreach($collection as $item)
	{
		$campo_check = array(
			'name'  => 'part_'.$item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'],
			'id'    => 'part_'.$item['cd_empresa'].'_'.$item['cd_registro_empregado'].'_'.$item['seq_dependencia'],
			'value' => $item['re_cripto']
		);	

		$body[] = array(
			(trim($item['fl_enviar']) == 'S' ? form_checkbox($campo_check) : ''),
			$item['ds_empresa'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			'<span class="label '.trim($item['ds_ceeeg_class_status']).'">'.$item['ds_ceeeg'].'</span>',
			array($item['nome'],'text-align:left;'),
			'<span class="label '.(trim($item['fl_eletronico']) == 'I' ? 'label-success' : 'label-important').'">'.(trim($item['fl_eletronico']) == 'I' ? 'Sim' : 'Não').'</span>',
			'<span class="label '.trim($item['class_status']).'">'.trim($item['status']).'</span>',
			$item['email'],
			$item['email_profissional'],
			$item['dt_emissao_extrato'],
			$item['dt_limite_extrato'],
			$item['dt_documento'],
			$item['dt_recebido_extrato'],
			$item['dt_envio_email'],
			(trim($item['fl_documento_liquid']) == 'N' ? anchor('atividade/extrato_institutos/atualizar_documento/'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'], '[atualizar documento]') : ''),
		);
	}
		
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->id_tabela = 'tabela_demonstrativo';
		
	if(count($collection) > 0)
	{
		echo '
			<table border="0" align="center" cellspacing="20">
				<tr style="height: 30px;">
					<td>
						<input type="button" value="Enviar Emails" onclick="enviar();" class="btn btn-danger btn-small" style="width: 120px;">
					</td>
					<td>
						<input type="button" value="Enviar Manual" onclick="enviar_manual();" class="btn btn-success btn-small" style="width: 120px;">
					</td>
					<td>
						<input type="button" value="Enviar Correio" onclick="enviar_correio();" class="btn btn-warning btn-small" style="width: 120px;">
					</td>	
				</tr>
			</table>';
	}

	echo $grid->render();
?>
