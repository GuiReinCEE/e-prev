<?php
set_title("Caderno CCI - Configuração de Texto Livre");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array());
	?>

	function ir_lista()
	{
		location.href = "<?= site_url("gestao/caderno_cci") ?>";
	}

	function ir_projetado()
	{
		location.href = "<?= site_url("gestao/caderno_cci/projetado/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_estrutura()
	{
		location.href = "<?= site_url("gestao/caderno_cci/estrutura/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_indice()
	{
		location.href = "<?= site_url("gestao/caderno_cci/indice/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_relatorio()
	{
		location.href = "<?= site_url("gestao/caderno_cci_relatorio/index/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_grafico()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_indice", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_grafico", "Configuração de Tabela", TRUE, "location.reload();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/grafico_configurar_texto_salvar");
		
		echo form_start_box("default_box", "Gráfico");	
			echo form_default_row("", "Nome do Gráfico :", $grafico["ds_caderno_cci_grafico"]);
		echo form_end_box("default_box");

		echo form_start_box("default_box", "Cadastro");	
			echo form_default_hidden("cd_caderno_cci_grafico", "", $grafico);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_editor_html("ds_html", "Texto :", $grafico["ds_html"],'style="height: 400px;"');
		echo form_end_box("default_box");
		
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>