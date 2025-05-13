<?php
set_title('Não Conformidade - Acompanhamento');
$this->load->view('header');
?>
<script>
	<?php
		$ar_obrigatorio = Array('situacao');
		echo form_default_js_submit($ar_obrigatorio);		
	?>
	
	function irLista()
	{
		location.href='<?php echo site_url("gestao/nc"); ?>';
	}
	
	function irAC(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/acao_corretiva"); ?>' + "/" + cd_nao_conformidade;
	}	
	
	function irNC(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/cadastro"); ?>' + "/" + cd_nao_conformidade;
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/nc/anexo/'.$cd_nao_conformidade); ?>";
	}

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
		ob_resul.sort(0, true);
	}

	function imprimirNC(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/impressao"); ?>' + "/" + cd_nao_conformidade;
	}		
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
	$abas[] = array('aba_nc', 'Não Conformidade', FALSE, "irNC('".$cd_nao_conformidade."');");
	if($nc['fl_apresenta_ac'] == "S")
	{
		$abas[] = array('aba_ac', 'Ação Corretiva', FALSE, "irAC('".$nc['cd_nao_conformidade']."');");
	}	
	$abas[] = array('aba_acompanha', 'Acompanhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	
	echo aba_start( $abas );
	
	echo form_open('gestao/nc/acompanhaSalvar');
	echo form_start_box( "default_box", "Cadastro" );
		echo form_default_hidden('cd_nao_conformidade', "Código:", $cd_nao_conformidade, "style='width:100%;border: 0px;' readonly");
		echo form_default_hidden('cd_acompanhamento', "Código:", 0, "style='width:100%;border: 0px;' readonly" );
		echo form_default_text('numero_cad_nc', "Número:", $nc, "style='font-weight: bold;width:100%;border: 0px;' readonly" );		
		echo form_default_text('ds_processo', "Processo:", $nc, "style='width:100%;border: 0px;' readonly" );		
		echo form_default_text('ds_responsavel', "Responsável:", $nc, "style='width:100%;border: 0px;' readonly" );		
		
		echo form_default_textarea('situacao', "Situação:*", '', "style='width:500px; height: 100px;'");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		echo button_save("Salvar");
		echo button_save("Imprimir","imprimirNC(".$cd_nao_conformidade.")","botao_disabled");
	echo form_command_bar_detail_end();
	echo form_close();
	
	$body=array();
	$head = array( 
		'Data',
		'Situação',
		'Usuário'
	);

	foreach($ar_acompanha as $item )
	{
		$body[] = array(
		$item["dt_cadastro"],
		array(nl2br($item["situacao"]),"text-align:justify;"),
		array($item["registrado"],"text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();	
	
	echo "<BR><BR><BR>";	
	
	echo aba_end();
?>
<script>
$(document).ready(function() {
  configure_result_table();
});
	
</script>
<?php	
	$this->load->view('footer_interna');
?>