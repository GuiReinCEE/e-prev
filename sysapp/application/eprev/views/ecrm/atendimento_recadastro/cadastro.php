<?php
set_title('Recadastramento GAP');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia'), 'verifica_re_ano()');
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_recadastro"); ?>';
	}
	
	function verifica_re_ano()
	{
		if($('#cd_atendimento_recadastro').val() == 0)
		{
			$.post('<?php echo site_url('ecrm/atendimento_recadastro/verifica_re_ano');?>',
			{
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val(),
				seq_dependencia       : $('#seq_dependencia').val()
			},
			function(data)
			{
				if(data > 0)
				{
					alert('RE já foi cadastrado esse ano.');
					return false;
				}
				else
				{
					$('form').submit();
					return true;
				}
			});
		}
		else
		{
			$('form').submit();
			return true;
		}
	}
	
	$(function(){
		consultar_participante_focus__cd_empresa();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('ecrm/atendimento_recadastro/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atendimento_recadastro', "", $row);	
			echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'Participante :*', array('cd_empresa' => $row['cd_empresa'],'cd_registro_empregado' => $row['cd_registro_empregado'],'seq_dependencia' => $row['seq_dependencia']), false, true);
			if(intval($row['cd_atendimento_recadastro']) > 0)
			{
				echo form_default_row('', 'Dt. Inclusão :', $row['dt_criacao']);
			}
			echo form_default_textarea('observacao', 'Observação :', $row);
			echo form_default_textarea('servico_social', 'Serviço Social :', $row);
			if(intval($row['cd_atendimento_recadastro']) > 0)
			{
				echo form_default_row('', 'Ano :', $row['dt_periodo']);
				echo form_default_row('', 'Dt. Atualização :', $row['dt_atualizacao']);
				echo form_default_row('', 'Dt. Cancelamento :', $row['dt_cancelamento']);
				echo form_default_row('', 'Motivo Cancelamento :', $row['motivo_cancelamento']);
			}
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(trim($row['dt_cancelamento']) == '')
			{
				echo button_save("Salvar");	
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>