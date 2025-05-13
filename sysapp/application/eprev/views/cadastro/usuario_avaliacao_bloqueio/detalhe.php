<?php 
set_title('Bloqueio de usuários para avaliação');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("cd_usuario", "usuario") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/usuario_avaliacao_bloqueio"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('cadastro/usuario_avaliacao_bloqueio/salvar');
echo form_hidden( 'cd_usuario_avaliacao_bloqueio', intval($row['cd_usuario_avaliacao_bloqueio']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Bloqueio de usuários para avaliação" );
echo form_default_usuario_ajax("cd_usuario", '', $row['cd_usuario']);

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_usuario_avaliacao_bloqueio'])>0  )
{
	echo button_delete("cadastro/usuario_avaliacao_bloqueio/excluir",$row["cd_usuario_avaliacao_bloqueio"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('cadastro/usuario_avaliacao_bloqueio')."'; }");
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