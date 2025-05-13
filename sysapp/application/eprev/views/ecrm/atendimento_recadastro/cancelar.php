<?php
set_title('Recadastramento GAP');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('motivo_cancelamento'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_recadastro"); ?>';
	}
	
	$(function(){
		consultar_participante_focus__cd_empresa();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cancelamento', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('ecrm/atendimento_recadastro/salvar_cancelamento');
		echo form_start_box( "default_box", "Cancelamento" );
			echo form_default_hidden('cd_atendimento_recadastro', "", $row);	
			echo form_default_row('', 'Participante :', $row["cd_empresa"].'/'.$row["cd_registro_empregado"].'/'.$row["seq_dependencia"].'  '.$row['nome']);
			echo form_default_row('', 'Nome :', $row["nome"]);
			echo form_default_row('', 'Dt. Inclusão :', $row['dt_criacao']);
			echo form_default_textarea('motivo_cancelamento', 'Motivo Cancelamento :', $row);
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