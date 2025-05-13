<?php
set_title('Sites Institucionais - Cadastro Home');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit
			 (
				Array
					(
						'cd_site',
						'cd_versao',
						'tit_capa',
						'texto_capa',
						'endereco'
					)
			 );
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/site"); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("ecrm/site/historico/".intval($cd_site)."/".intval($cd_versao)); ?>';
	}	

	function siteExcluir(cd_site,cd_versao)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("ecrm/site/excluir"); ?>' + "/" + cd_site + "/" + cd_versao;
		}
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload()');
	$abas[] = array('aba_historico', 'Histórico', FALSE, 'ir_historico()');
	
	echo aba_start( $abas );
	
		echo form_open('ecrm/site/salvar');
			echo form_start_box( "default_box", "Home" );
				echo form_default_text('cd_site', "Código:", $cd_site, "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('cd_versao', "Versão:", $cd_versao, "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('tit_capa', "Título:*", $row, "style='width: 500px;'");
				echo form_default_editor_html('texto_capa', "Conteúdo:*", $row,'style="height: 400px;"');
				echo form_default_text('endereco', "Endereço:", $row, "style='width: 100%;'");
			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				echo button_save("Salvar");
				if(intval($cd_site) > 0)
				{
					echo button_save("Excluir","siteExcluir(".$cd_site.",".$cd_versao.")","botao_vermelho");
				}			
			echo form_command_bar_detail_end();
		echo form_close();

	echo aba_end();
	$this->load->view('footer_interna');
?>