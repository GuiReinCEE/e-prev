<?php 
set_title('Links quebrados');
$this->load->view('header'); 
?>
<script>
	
	<?php echo form_default_js_submit(array(

		

	));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("servico/link_quebrado"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('servico/link_quebrado/salvar');
echo form_hidden( 'cd_log_link', intval($row['cd_log_link']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Links quebrados" );
echo form_default_text("nr_ip", "Nr Ip", $row, "style='width:300px;'", "0"); 
echo form_default_textarea("ds_link_pagina", "Ds Link Pagina", $row, "", "0"); 
echo form_default_textarea("ds_link_quebrado", "Ds Link Quebrado", $row, "", "0"); 
echo form_default_text("dt_erro", "Dt Erro", $row, "style='width:300px;'", "0"); 

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_log_link'])>0  && false  )
{
	echo button_delete("servico/link_quebrado/excluir",$row["cd_log_link"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('servico/link_quebrado')."'; }");
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