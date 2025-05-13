<?php
set_title('Exame Médico Ingresso - Acompanhamento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('acompanhamento'));
	?>
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"DateTimeBR",
					"CaseInsensitiveString",
					"CaseInsensitiveString"
					]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}	
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/exame_medico_ingresso"); ?>';
	}
	
	function ir_cadastro(cd_exame_medico_ingresso)
	{
		location.href='<?php echo site_url("ecrm/exame_medico_ingresso/detalhe"); ?>' + "/" + cd_exame_medico_ingresso;
	}

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, "ir_cadastro(".intval($cd_exame_medico_ingresso).");");
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

	
	echo aba_start( $abas );
	
	echo form_open('ecrm/exame_medico_ingresso/acompanhamentoSalvar');
	echo form_start_box( "default_box", "Cadastro" );
		echo form_default_text('cd_exame_medico_ingresso', "Código: ", $cd_exame_medico_ingresso, "style='width:100%;border: 0px;' readonly" );
		echo form_default_textarea('acompanhamento', "Acompanhamento:* ", '', "style='width:500px; height: 80px;'");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(intval($cd_exame_medico_ingresso) > 0)
		{		
			echo button_save("Salvar");
		}
	echo form_command_bar_detail_end();
		
	echo form_close();
	
	echo form_start_box( "default_box_lista", "Acompanhamentos" );
		$body=array();
		$head = array( 
			'Data',
			'Descrição',
			'Cadastrado'
		);

		foreach( $ar_acompanhamento as $item )
		{
			$body[] = array(
			$item["dt_inclusao"],
			array($item["acompanhamento"],"text-align:left;"),
			array($item["ds_usuario_inclusao"],"text-align:left;")
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();
	
	echo form_end_box("default_box_lista");	
	echo aba_end();
?>
<script>
	configure_result_table();
</script>
<?php	
	$this->load->view('footer_interna');
?>