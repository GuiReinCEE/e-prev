<?php $this->load->view('header', array('topo_titulo'=>'Cadastro, Contrato, Grupo')); ?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("ds_contrato_formulario_grupo", "str") 
,array("cd_contrato_formulario", "fk") 
,array("nr_ordem", "int") 


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/contrato_formulario_grupo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('cadastro/contrato_formulario_grupo/salvar');
echo form_hidden( 'cd_contrato_formulario_grupo', intval($row['cd_contrato_formulario_grupo']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Cadastro, Contrato, Grupo" );
echo form_default_text("ds_contrato_formulario_grupo", "Descrição *", $row, "style='width:300px;'", "200"); 
echo form_default_dropdown_db("cd_contrato_formulario", "Formulário *", array( "projetos.contrato_formulario", "cd_contrato_formulario", "ds_contrato_formulario" ), array( $row["cd_contrato_formulario"] ), ""); 
echo form_default_integer("nr_ordem", "Ordem *", $row, ""); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_contrato_formulario_grupo'])>0 )
{
	echo button_delete("cadastro/contrato_formulario_grupo/excluir",$row["cd_contrato_formulario_grupo"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('cadastro/contrato_formulario_grupo')."'; }");
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