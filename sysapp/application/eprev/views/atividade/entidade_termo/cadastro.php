<?php
set_title('Entidade - Termos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('dt_inicial'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/entidade_termo"); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('atividade/entidade_termo/salvar');
		echo form_start_box( "default_box", "Entidade" );
			echo form_default_hidden('cd_termo', "", $row);	
			echo form_default_date('dt_inicial', 'Dt. Inicial :*', $row);
			echo form_default_date('dt_final', 'Dt. Final :', $row);
			echo form_default_integer('nr_dia_termo', 'Dia Limite :', $row);
			echo form_default_editor_html('ds_termo', 'Texto :', $row['ds_termo']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>