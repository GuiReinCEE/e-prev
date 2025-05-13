<?php
set_title('Grupos de Pesquisa');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('ds_titulo', 'ds_titulo', 'cd_enquete_sim', 'cd_enquete_nao'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/operacional_enquete_grupo"); ?>';
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
	echo form_open('ecrm/operacional_enquete_grupo/salvar');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_enquete_grupo', "", $row);	
			if(intval($row['cd_enquete_grupo']) > 0)
			{
				echo form_default_row('', 'Código da Pesquisa :', $row['cd_enquete_grupo']);
			}
			
			echo form_default_text('ds_titulo', 'Título :*', $row, 'style="width:500px;"');
			echo form_default_text('ds_pergunta', 'Pergunta :*', $row, 'style="width:500px;"');
			echo form_default_integer('cd_enquete_sim', 'Código da Pesquisa para Sim :*', $row);
			echo form_default_integer('cd_enquete_nao', 'Código da Pesquisa para Não :*', $row);
			
			if(intval($row['cd_enquete_grupo']) > 0)
			{
				echo form_default_row('', 'Link da Pesquisa para enviar :', "http://".$_SERVER['SERVER_NAME']."/controle_projetos/enquete_inicio.php?c=".$row['cd_enquete_grupo']);
			}
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>