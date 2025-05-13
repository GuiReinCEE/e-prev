<?php 
set_title('Torcida - Enquete');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array( array("nome", "str")/*, array("dt_inicio", "date")*/ )); ?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_enquete"); ?>';
	}

	function ir_estrutura()
	{
		location.href="<?php echo site_url('ecrm/ri_torcida_enquete/estrutura/'.$row['cd_enquete']); ?>";
	}

	function ir_resultado()
	{
		location.href="<?php echo site_url('ecrm/ri_torcida_enquete/resultado/'.$row['cd_enquete']); ?>";
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
if( intval($row['cd_enquete'])>0  )
{
	$abas[] = array('aba_estrutura', 'Estrutura', false, 'ir_estrutura();');
	$abas[] = array('aba_resultado', 'Resultado', false, 'ir_resultado();');
}
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_enquete/salvar');
echo form_hidden( 'cd_enquete', intval($row['cd_enquete']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - Enquete" );
echo form_default_text("nome", "Nome *", $row, "style='width:300px;'", "0"); 
//echo form_default_date("dt_inicio", "Dt Inicio *", $row, ""); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_enquete'])>0  )
{
	echo button_delete("ecrm/ri_torcida_enquete/excluir",$row["cd_enquete"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_enquete')."'; }");
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