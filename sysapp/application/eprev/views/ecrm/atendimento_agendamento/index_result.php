<?php
	$head = array(
		'Dt. Agendamento',
		'Cód. Atend.',
		'Tipo',
		'Nome',
		'CPF',
		'RE',
		'Tipo',
		'E-mail',
		'Telefone',
		'Solicitação',
		'Cancelamento',
		'Compareceu',
		'',
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$cancelamento = '';

		$telefone = array();
		
		if(trim($item['telefone_1']) != '')
		{
			$telefone[] = str_replace(' ', '', $item['telefone_1']);
		}

		if(trim($item['telefone_2']) != '')
		{
			$telefone[] = str_replace(' ', '', $item['telefone_2']);
		}

		if(trim($item['dt_cancelado']) != '')
		{
			$cancelamento = 'Usuário: '.$item['ds_usuario_cancelado'].'<br/>';
			$cancelamento .= $item['dt_cancelado'].'<br/>';

			if(trim($item['ds_justificativa_cancelamento']) != '')
			{
				$cancelamento .= '<i>'.$item['ds_justificativa_cancelamento'].'</i><br/>';
			}	
		}

		$body[] = array(
			anchor("ecrm/atendimento_agendamento/editar_agendamento/".$item['cd_atendimento_agendamento'], $item['dt_agenda']),
			$item['cd_atendimento'],
			$item['ds_tipo_agendamento'],
			array($item['nome'], 'text-align:left'),
			$item['cpf'],
			(trim($item['cd_registro_empregado']) != '' ? $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'] : ''),
			array($item['ds_atendimento_agendamento_tipo'], 'text-align:left'),
			$item['email'],
			implode(br(), $telefone),
			array('Usuário: '.$item['ds_usuario_inclusao'].'<br/>' . $item['dt_inclusao'], 'text-align:justify'),
			array($cancelamento, 'text-align:justify'),
			'<span id="ajax_compareceu_valor_'.$item['cd_atendimento_agendamento'].'"></span> '.(trim($item['fl_compareceu']) == 'N' ? '<span id="compareceu_valor_'.$item['cd_atendimento_agendamento'].'" class="label label-important">Não</span>' : '<span id="compareceu_valor_'.$item['cd_atendimento_agendamento'].'" class="label label-info";>Sim</span>' )
			.
			form_dropdown("fl_compareceu_".$item['cd_atendimento_agendamento'], array('S'=> 'Sim', 'N' => 'Não'), array($item['fl_compareceu']))
			."<script> 
					$('#fl_compareceu_".$item['cd_atendimento_agendamento']."').hide();
					$('#fl_compareceu_".$item['cd_atendimento_agendamento']."').blur(function() { setcompareceu(".$item['cd_atendimento_agendamento']."); });
			</script>".
			'<br/><a id="compareceu_editar_'.$item['cd_atendimento_agendamento'].'" href="javascript: void(0)" onclick="editarcompareceu('.$item['cd_atendimento_agendamento'].');" title="Editar o Status">[editar]</a>'
			.'<a id="compareceu_salvar_'.$item['cd_atendimento_agendamento'].'" href="javascript: void(0)" style="display:none" title="Salvar o Status">[salvar]</a>',
			($item['dt_cancelado'] == '' ? '<a href="javascript:void(0);" onclick="cancelar('.$item['cd_atendimento_agendamento'].')">[cancelar]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>