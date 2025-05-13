<?php
	set_title('Envio Extrato');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_envio'), 'enviar_email(form)') ?>

	function ir_liberado()
	{
		location.href = "<?= site_url('planos/envio_extrato') ?>";
	}

	function enviar_email(form)
	{
		var confirmacao = '';

		if($("#fl_libera_envio").val() != '')
		{
			confirmacao += 'ATENÇÃO!!!!!!\n\n'+ 
			               'INCONSISTÊNCIA\n\n';
		}

		confirmacao +=
					  'Confirma o envio do Extrato para: \n\n'+
		              'Plano: ' + $("#cd_plano").val() +'\n'+
		              'Empresa: ' + $("#cd_empresa").val() +'\n'+
		              'Nr. Extrato: ' + $("#nro_extrato").val() +'\n'+
		              'Dt Envio: ' + $("#dt_envio").val() +'\n\n\n'+
					  'Clique [Ok] para Sim\n\n'+
					  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{
			form.submit();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "RE",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(1, false);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_liberados', 'Lista', FALSE, 'ir_liberado();');
	$abas[] = array('aba_agendar', 'Agendar Envio', TRUE, 'location.reload();');

	$qt_total = intval($extrato['qt_extrato_participante'] - ($extrato['qt_obito'] + $extrato['qt_sem_plano'] + $extrato['qt_sem_email']));

	$head = array( 
		'RE',
		'Nome'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('planos/envio_extrato/salvar_agendamento');
			echo form_default_hidden('cd_plano', '', $extrato['cd_plano']);
			echo form_default_hidden('cd_empresa', '', $extrato['cd_empresa']);
			echo form_default_hidden('nro_extrato', '', $extrato['nro_extrato']);
			echo form_default_hidden('nr_ano', '', $extrato['nr_ano']);
			echo form_default_hidden('nr_mes', '', $extrato['nr_mes']);
			echo form_default_hidden('qt_extrato', '', $extrato['qt_extrato']);
			echo form_default_hidden('dt_base', '', $extrato['dt_base']);
			echo form_default_hidden('fl_enviar_email_cadastro', '', $extrato['fl_enviar_email_cadastro']);
			echo form_default_hidden('fl_libera_envio', '', $extrato['fl_libera_envio']);
			echo form_start_box('default_box', 'Extrato');
				echo form_default_row('', 'Plano:', $extrato['ds_plano']);
				echo form_default_row('', 'Empresa:', $extrato['ds_empresa']);
				echo form_default_row('', 'Nr. Extrato:', $extrato['nro_extrato']);
				echo form_default_row('', 'Dt. Base:', $extrato['dt_base']);
				echo form_default_row('', 'Enviar e-mail (GP Cadastro):', (trim($extrato['fl_enviar_email_cadastro']) == 'S' ? 'Sim' : 'Não'));
			echo form_end_box('default_box');
			echo form_start_box('default_extratos_box', 'Envido de Extratos');
				echo form_default_row('', 'Status:', '<span class="label label-'.(trim($extrato['fl_libera_envio']) == 'S' ? 'info">Liberado para Envio' : 'warning">Inconsistência' ).'</span>');
				echo form_default_row('', 'Qt. de Extratos Eletro:', '<span class="badge badge-info">'.$extrato['qt_extrato'].'</span>');
				echo form_default_row('', 'Qt. de Extratos Internet:', '<span class="badge badge-info">'.$extrato['qt_extrato_participante'].'</span>');
				echo form_default_row('', 'Qt. Óbito:', '<span class="badge badge-important">'.$extrato['qt_obito'].'</span>');
				echo form_default_row('', 'Qt. sem Plano:', '<span class="badge badge-important">'.$extrato['qt_sem_plano'].'</span>');
				echo form_default_row('', 'Qt. sem E-mail:', '<span class="badge badge-important">'.$extrato['qt_sem_email'].'</span>');
				echo form_default_row('', 'Qt. Total para Envio:', '<span class="badge badge-success">'.$qt_total.'</span>');
			echo form_end_box('default_extratos_box');
			/*
			echo form_start_box('default_agendar_box', 'Agendar Envio');
			echo form_default_date('dt_envio', 'Dt. Agendamento: (*)', date('d/m/Y'));
			echo form_default_row('', '', '<span class="label label-inverse">O envio dos e-mails ocorre um dia após a data do agendamento.</i>');
			echo form_end_box('default_agendar_box');
			echo form_command_bar_detail_start();
			*/
				/*echo button_save('Agendar');	*/
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo '<h1 style="text-align:center; font-size:18px;">Participantes Sem e-mail</h1>';
		echo $grid->render();
	echo aba_end();

	$this->load->view('footer');
?>