<?php 
set_title('Email de reclamação');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(
		array("cd_programa", "fk"), array("cd_usuario", "fk") 
	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/email_reclamacao"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/email_reclamacao/salvar');
echo form_hidden( 'cd_atendimento_programa_gerencia', intval($row['cd_atendimento_programa_gerencia']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Email de reclamação" );

echo form_default_dropdown_db(
	"cd_programa"
	, "Programa *"
	, array( "public.listas", "codigo", "descricao" )
	, array( $row["cd_programa"] )
	, ""
	, ""
	, FALSE
	, " categoria = 'PRFC' "
);
echo form_default_usuario_ajax("cd_usuario", "", $row["cd_usuario"]);

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_atendimento_programa_gerencia'])>0 )
{
	echo button_delete("ecrm/email_reclamacao/excluir",$row["cd_atendimento_programa_gerencia"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/email_reclamacao')."'; }");
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
