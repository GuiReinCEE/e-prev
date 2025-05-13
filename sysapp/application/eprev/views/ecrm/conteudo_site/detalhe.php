<?php
set_title('Sites Institucionais - Cadastro Página');
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
						'cd_materia',
						'ds_titulo',
						'ds_item_menu',
						'cd_secao',
						'nr_ordem',
						'fl_excluido'
					)
			 );
	?>

	function ir_sites()
	{
		location.href='<?php echo site_url("ecrm/site"); ?>';
	}

	function ir_paginas()
	{
		location.href='<?php echo site_url("ecrm/conteudo_site/index/".intval($cd_site)."/".intval($cd_versao)); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("ecrm/conteudo_site/historico/".intval($cd_site)."/".intval($cd_versao)."/".intval($cd_materia)); ?>';
	}		
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_sites()');
	$abas[] = array('aba_pagina', 'Páginas', FALSE, "ir_paginas()");
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload()');
	$abas[] = array('aba_historico', 'Histórico', FALSE, "ir_historico()");
	
	echo aba_start( $abas );
	
		echo form_open('ecrm/conteudo_site/salvar');
			echo form_start_box( "default_box", "Home" );
				echo form_default_text('cd_site', "Código:", intval($cd_site), "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('cd_versao', "Versão:", intval($cd_versao), "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('cd_materia', "Página:", intval($cd_materia), "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('dt_inclusao', "Dt Inclusão:", $row, "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('dt_alteracao', "Dt Alteração:", $row, "style='width:100%;border: 0px;' readonly" );
				echo (trim($row['dt_exclusao']) != "" ? form_default_text('dt_exclusao', "Dt Exclusão:", $row, "style='width:100%;border: 0px;' readonly" ) : "");
				echo form_default_text('ds_titulo', "Título:*", $row, "style='width: 500px;'");
				echo form_default_text('ds_item_menu', "Item Menu:*", $row, "style='width: 500px;'");				
				echo form_default_dropdown('cd_secao', 'Seção:*', $ar_secao, array($row['cd_secao']));
				echo form_default_integer('nr_ordem', "Ordem:*", $row);
				echo form_default_dropdown('fl_excluido', 'Excluir:*', array(array('value'=>'S', 'text'=>'Sim'), array('value'=>'N', 'text'=>'Não')), array($row['fl_excluido']));
				echo form_default_editor_html('conteudo_pagina', "Conteúdo:*", $row,'style="height: 400px;"');
			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				echo button_save("Salvar");
			echo form_command_bar_detail_end();
		echo form_close();
	
	echo br(4);
	echo aba_end();

	$this->load->view('footer_interna');
?>