<?php
set_title('Recadastro Devolução');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(Array('cd_empresa','cd_registro_empregado','seq_dependencia','nome','dt_devolucao','cd_atendimento_recadastro_devolucao_motivo')); ?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/recadastro_devolucao"); ?>';
	}
	
	function nova()
	{
		location.href='<?php echo site_url('ecrm/recadastro_devolucao/cadastro');?>';
	}	

	function excluir()
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url('ecrm/recadastro_devolucao/excluir/'.$row['cd_atendimento_recadastro_devolucao']);?>';
		}
	}		
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro',  TRUE, 'location.reload();');

$participante['cd_empresa']            = $row['cd_empresa'];
$participante['cd_registro_empregado'] = $row['cd_registro_empregado'];
$participante['seq_dependencia']       = $row['seq_dependencia'];

echo aba_start( $abas );
	echo form_open('ecrm/recadastro_devolucao/salvar');
	echo form_start_box( "default_box", "Dados" );
		echo form_default_hidden('cd_atendimento_recadastro_devolucao', "Cod. Devolução: ", $row);
		echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante*:", $participante, TRUE, TRUE );			
		echo form_default_text('nome', "Nome*:", $row, "style='width:600px;'");
		echo form_default_date('dt_devolucao', "Dt Devolução*:", $row);
		echo form_default_dropdown('cd_atendimento_recadastro_devolucao_motivo', 'Motivo*:', $ar_devolucao_motivo, array($row['cd_atendimento_recadastro_devolucao_motivo']));
		echo form_default_text('descricao', "Descrição:", $row, "style='width:600px;'");
		echo form_default_textarea('observacao', "Observação:", $row, "style='width:600px; height: 80px;'");
		echo form_default_text('dt_inclusao', "Dt. Inclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('nome_usuario', "Usuário Inclusão:", $row, "style='width:100%;border: 0px;' readonly" );	
		
		if(trim($row['dt_alteracao']) != "")
		{
			echo form_default_text('dt_alteracao', "Dt. Alteração: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('nome_usuario_alteracao', "Usuário Alteração:", $row, "style='width:100%;border: 0px;' readonly" );			
		}
		
		if(trim($row['dt_exclusao']) != "")
		{
			echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('nome_usuario_exclusao', "Usuário Excluido:", $row, "style='width:100%;border: 0px;' readonly" );			
		}
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		echo button_save("Salvar");
		
		if(intval($row['cd_atendimento_recadastro_devolucao']) > 0)
		{
			echo button_save("Excluir", "excluir();", "botao_vermelho");
			echo button_save("Nova Devolução", "nova();", "botao_verde");
		}
	echo form_command_bar_detail_end();
	
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>