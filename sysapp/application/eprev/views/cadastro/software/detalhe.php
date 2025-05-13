<?php 
set_title('Softwares');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		array("programa", "str")
		, array("cd_divisao", "fk")
		, array("tipo_programa", "fk")
		, array("tipo_licenciamento", "fk")


	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/software"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('cadastro/software/salvar');
echo form_hidden( 'programa_md5', md5($row['programa']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "" );

if( trim($row["programa"])=='' )
{
	echo form_default_text("programa", "Programa *", $row, "style='width:300px;'", "0");
}
else
{
	echo form_default_text("programa", "Programa *", $row, "style='width:300px;' readonly='readonly'", "0");
}

echo form_default_dropdown_db("cd_divisao"
, "Gerência *"
, array( "projetos.divisoes", "codigo", "nome" )
, array( $row["cd_divisao"] )
, ""
, ""
, FALSE
, "");

echo form_default_textarea("definicao", "Definição", $row, "style='width:300px;height:100px;'", "0");

echo form_default_dropdown_db(
"tipo_programa"
, "Tipo programa *"
, array( "public.listas", "codigo", "descricao" )
, array( $row["tipo_programa"] )
, ""
, ""
, FALSE
, " categoria = 'SOFT' and divisao = 'GI' "
);

echo form_default_text("fabricante", "Fabricante", $row, "style='width:300px;'", "0");

echo form_default_dropdown_db(
"tipo_licenciamento"
, "Tipo de licenciamento *"
, array( "public.listas", "codigo", "descricao" )
, array( $row["tipo_licenciamento"] )
, ""
, ""
, FALSE
, " categoria = 'TLIC' and divisao = 'GI' "); 

echo form_default_integer("num_licencas", "Núm de licenças", $row, "");

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('cadastro/software')."'; }");
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
