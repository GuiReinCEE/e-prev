<?php 
set_title('Torcida - Novidade e Gol Contra');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(
		array("ds_titulo", "str")
		, array("ds_noticia", "text") 
		, array("tp_noticia", "str") 
	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_noticia"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('ecrm/ri_torcida_noticia/salvar');
echo form_hidden( 'cd_noticia', intval($row['cd_noticia']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Torcida - Notícias" );
echo form_default_text("ds_titulo", "Título *", $row, "style='width:300px;'", "0"); 
echo form_default_textarea("ds_noticia", "Notícia *", $row, "", "0"); 
echo form_default_textarea("ds_resumo", "Resumo", $row, "", "0"); 
$tp_noticia_dd[]=array('text'=>'::selecione::','value'=>'');
$tp_noticia_dd[]=array('text'=>'Novidade','value'=>'N');
$tp_noticia_dd[]=array('text'=>'Gol contra','value'=>'G');
echo form_default_dropdown("tp_noticia", "Tipo *", $tp_noticia_dd, array($row["tp_noticia"]));

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_noticia'])>0  )
{
	echo button_delete("ecrm/ri_torcida_noticia/excluir",$row["cd_noticia"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('ecrm/ri_torcida_noticia')."'; }");
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