<?php
set_title('Eventos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_projeto'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/evento"); ?>';
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
$arr_drop[] = array('value' => 'N', 'text' => 'Não');
$arr_drop[] = array('value' => 'S', 'text' => 'Sim');
echo aba_start( $abas );
	echo form_open('gestao/evento/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_evento', "", $row);	
			echo form_default_dropdown('cd_projeto', 'Projeto :*', $arr_projeto, array($row['cd_projeto']));
			echo form_default_text('nome', 'Evento :', $row, 'style="width:500px;"');
			echo form_default_dropdown('tipo', 'Tipo do Evento :', $arr_evento, array($row['tipo']));
			echo form_default_dropdown('dt_referencia', 'Data Referência Para o Evento :', $arr_referencia_evento, array($row['dt_referencia']));
			echo form_default_integer('dias_dt_referencia', 'Dias a Partir da Data de Referência :', $row);
			echo form_default_dropdown('indic_historico', 'Considerar no Histórico :', $arr_drop, array($row['indic_historico']));
			echo form_default_dropdown('indic_email', 'Enviar Email :', $arr_drop, array($row['indic_email']));
			echo form_default_checkbox_group('arr_destino', 'Destino Principal do Email :', $arr_destino, $arr_destino_checked, 300);
			echo form_default_checkbox_group('arr_alternativo', 'Destino Alternativo do Email:'.br().'(na ausência do principal, envia-se para o alternativo)', $arr_destino, $arr_destino_alternativo_checked, 300);
			echo form_default_textarea('email', 'Texto do Email :', $row);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>