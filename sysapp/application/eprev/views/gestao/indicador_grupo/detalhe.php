<?php 
set_title('Grupos de Indicadores');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_indicador_grupo", "str") 

	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/indicador_grupo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('gestao/indicador_grupo/salvar');
echo form_hidden( 'cd_indicador_grupo', intval($row['cd_indicador_grupo']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Grupos de Indicadores" );
echo form_default_text("ds_indicador_grupo", "Descrição *", $row, "style='width:300px;'", "255"); 
echo form_default_textarea("ds_missao", "Missão", $row); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_indicador_grupo'])>0  )
{
	echo button_delete("gestao/indicador_grupo/excluir",$row["cd_indicador_grupo"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('gestao/indicador_grupo')."'; }");
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