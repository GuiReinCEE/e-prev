<?php 
set_title('Atendimentos');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		

	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/atendimento_lista"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/atendimento_lista/salvar');
echo form_hidden( 'cd_atendimento', intval($row['cd_atendimento']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Atendimentos" );
echo form_default_integer("cd_plano", "Cd_plano", $row, ""); 
echo form_default_integer("cd_empresa", "Cd_empresa", $row, ""); 
echo form_default_integer("cd_registro_empregado", "Cd_registro_empregado", $row, ""); 
echo form_default_integer("seq_dependencia", "Seq_dependencia", $row, ""); 
echo form_default_date("dt_hora_inicio_atendimento", "Dt_hora_inicio_atendimento", $row, ""); 
echo form_default_date("dt_hora_fim_atendimento", "Dt_hora_fim_atendimento", $row, ""); 
echo form_default_integer("id_atendente", "Id_atendente", $row, ""); 
echo form_default_text("origem_atendimento", "Origem_atendimento", $row, "style='width:100px;'", "0"); 
echo form_default_text("indic_ativo", "Indic_ativo", $row, "style='width:100px;'", "0"); 
echo form_default_text("obs", "Obs", $row, "style='width:100px;'", "0"); 
echo form_default_text("tipo_atendimento_indicado", "Tipo_atendimento_indicado", $row, "style='width:100px;'", "0"); 
echo form_default_text("opt_atendimento", "Opt_atendimento", $row, "style='width:100px;'", "0"); 
echo form_default_date("dt_encaminhamento", "Dt_encaminhamento", $row, ""); 
echo form_default_integer("resp_encaminhamento", "Resp_encaminhamento", $row, ""); 
echo form_default_text("hora_senha", "Hora_senha", $row, "style='width:100px;'", "0"); 
echo form_default_text("tipo_reclamacao", "Tipo_reclamacao", $row, "style='width:100px;'", "0"); 
echo form_default_text("cd_programa", "Cd_programa", $row, "style='width:100px;'", "0"); 
echo form_default_text("cd_tipo_obs", "Cd_tipo_obs", $row, "style='width:100px;'", "0"); 
echo form_default_text("cd_tipo_solicitante", "Cd_tipo_solicitante", $row, "style='width:100px;'", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_atendimento'])>0 )
{
	echo button_delete("ecrm/atendimento_lista/excluir",$row["cd_atendimento"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/atendimento_lista')."'; }");
echo form_command_bar_detail_end();
?>
<script>
	// $('{PRIMEIRO_CAMPO}').focus();
</script>

<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
?>