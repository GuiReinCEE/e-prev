<?php
set_title('Campanha Aumento de Contribuição - Cadastro');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_empresa', 'ds_assunto', 'ds_tpl')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('planos/campanha_aumento_contrib_inst')?>";
	}

	function ir_participante()
	{
		location.href = "<?= site_url('planos/campanha_aumento_contrib_inst/participante/'.$row['cd_campanha_aumento_contrib_inst'])?>";
	}

	function get_campanha_anterior(cd_empresa)
	{
		$.post("<?= site_url('planos/campanha_aumento_contrib_inst/get_campanha_anterior') ?>",
		{
			cd_empresa : cd_empresa
		},
		function(data)
		{ 
			$('#ds_assunto').val(data.ds_assunto);
			$('#ds_tpl').val(data.ds_tpl);

		}, 'json', true);
	}

	function enviar_email()
	{
		var confirmacao = 'Deseja enviar a campanha com os dados do MEU RETRATO com base na data de <?= $row['dt_base_extrato'] ?>?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('planos/campanha_aumento_contrib_inst/agendar_envio/'.$row['cd_campanha_aumento_contrib_inst']) ?>";
		}
	}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_campanha_aumento_contrib_inst']) > 0)
{
	$abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante();');
}

echo aba_start($abas);
	echo form_open('planos/campanha_aumento_contrib_inst/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_campanha_aumento_contrib_inst', '', $row);

			if(intval($row['cd_campanha_aumento_contrib_inst']) > 0)
			{
				echo form_default_row('', 'Meu Retrato Edição :', '<span style="font-size:14pt; font-weight:bold;">'.$row['cd_edicao'].'</span>');
				echo form_default_row('', 'Dt. Base Extrato :', '<span style="font-size:14pt; color:red; font-weight:bold;">'.$row['dt_base_extrato'].'</span>');
				echo form_default_row('cd_empresa', 'Instituidor :', $row['ds_instituidor']);
			}
			else
			{
			  	echo form_default_dropdown('cd_empresa', 'Instituidor : (*)', $instituidor, '',	'onchange="get_campanha_anterior(this.value)"');
			}

			echo form_default_text('ds_assunto', 'Assunto E-mail : (*)', $row['ds_assunto'], 'style="width:500px"');
			echo form_default_text('ds_tpl', 'Template : (*)', $row['ds_tpl'], 'style="width:500px"');

		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			if(trim($row['dt_envio']) == '' AND trim($row['dt_agenda_envio']) == '')
			{
				echo button_save('Salvar');

				if(intval($row['cd_campanha_aumento_contrib_inst']) > 0)
				{
					echo button_save('Enviar', 'enviar_email()', 'botao_verde');	
				}
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer');
?>