<?php 
set_title('Períodos de Controle dos Indicadores');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_periodo", "str") 
,array("dt_inicio", "date") 
,array("dt_fim", "date") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/indicador_periodo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('gestao/indicador_periodo/salvar');
echo form_hidden( 'cd_indicador_periodo', intval($row['cd_indicador_periodo']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Períodos de Controle dos Indicadores" );
echo form_default_text("ds_periodo", "Descrição *", $row, "style='width:300px;'", "255"); 
echo form_default_date("dt_inicio", "Dt Inicio *", $row, ""); 
echo form_default_date("dt_fim", "Dt Fim *", $row, ""); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_indicador_periodo'])>0  )
{
	echo button_delete("gestao/indicador_periodo/excluir",$row["cd_indicador_periodo"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('gestao/indicador_periodo')."'; }");
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