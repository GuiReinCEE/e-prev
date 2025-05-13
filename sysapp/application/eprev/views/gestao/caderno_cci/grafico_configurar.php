<?php
set_title("Caderno CCI - Configuração de Gráfico");
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
$abas[] = array("aba_apresentacao", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_grafico", "Configuração de Gráfico", TRUE, "location.reload();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/grafico_configurar_salvar");
		echo form_default_hidden("cd_caderno_cci_grafico", "", $grafico);	
		echo form_default_hidden("cd_caderno_cci", "", $row);	

		echo form_start_box("default_box", "Gráfico");	
			echo form_default_row("", "Nome do Gráfico :", $grafico["ds_caderno_cci_grafico"]);
		echo form_end_box("default_box");

		if(isset($rentabilidade))
		{
			foreach ($rentabilidade as $key => $item) 
			{
				echo form_start_box("default_box", $item["ds_caderno_cci_estrutura"]);	
					echo form_default_numeric("rentabilidade_ordem[".$item["cd_caderno_cci_estrutura"]."]", "Ordem :", (isset($ordem["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? $ordem["rentabilidade"][$item["cd_caderno_cci_estrutura"]]: ""));
					echo form_default_color("rentabilidade_cor[".$item["cd_caderno_cci_estrutura"]."]", "Cor : ", (isset($cor["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? $cor["rentabilidade"][$item["cd_caderno_cci_estrutura"]] : ""));
				echo form_end_box("default_box");
			}
		}

		if(isset($projetado))
		{
			foreach ($projetado as $key => $item) 
			{
				echo form_start_box("default_box", $item["ds_caderno_cci_projetado"]);	
					echo form_default_numeric("projetado_ordem[".$item["cd_caderno_cci_projetado"]."]", "Ordem :", (isset($ordem["projetado"][$item["cd_caderno_cci_projetado"]]) ? $ordem["projetado"][$item["cd_caderno_cci_projetado"]]: ""));
					echo form_default_color("projetado_cor[".$item["cd_caderno_cci_projetado"]."]", "Cor : ", (isset($cor["projetado"][$item["cd_caderno_cci_projetado"]]) ? $cor["projetado"][$item["cd_caderno_cci_projetado"]] : ""));
				echo form_end_box("default_box");
			}
		}

		if(isset($indice))
		{
			foreach ($indice as $key => $item) 
			{
				echo form_start_box("default_box", $item["ds_caderno_cci_indice"]);	
					echo form_default_numeric("indice_ordem[".$item["cd_caderno_cci_indice"]."]", "Ordem :", (isset($ordem["indice"][$item["cd_caderno_cci_indice"]]) ? $ordem["indice"][$item["cd_caderno_cci_indice"]] : ""));
					echo form_default_color("indice_cor[".$item["cd_caderno_cci_indice"]."]", "Cor : ", (isset($cor["indice"][$item["cd_caderno_cci_indice"]]) ? $cor["indice"][$item["cd_caderno_cci_indice"]] : ""));
				echo form_end_box("default_box");
			}
		}

		if(isset($benchmark))
		{
			foreach ($benchmark as $key => $item) 
			{
				echo form_start_box("default_box", $item["ds_caderno_cci_benchmark"]);	
					echo form_default_numeric("benchmark_ordem[".$item["cd_caderno_cci_benchmark"]."]", "Ordem :", (isset($ordem["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? $ordem["benchmark"][$item["cd_caderno_cci_benchmark"]] : "")   );
					echo form_default_color("benchmark_cor[".$item["cd_caderno_cci_benchmark"]."]", "Cor : ", (isset($cor["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? $cor["benchmark"][$item["cd_caderno_cci_benchmark"]] : ""));
				echo form_end_box("default_box");
			}
		}
		/*
		foreach($projetado as $key => $item)
		{
			echo form_default_color("projetado_".$key, $item." :* ", (isset($cor["projetado"][$key]) ? $cor["projetado"][$key] : ""));
		}

		foreach($indice as $key => $item)
		{
			echo form_default_color("indice_".$key, $item." :* ", (isset($cor["indice"][$key]) ? $cor["indice"][$key] : ""));
		}

		foreach($rentabilidade as $key => $item)
		{
			echo form_default_color("rentabilidade_".$key, $item." :* ", (isset($cor["rentabilidade"][$key]) ? $cor["rentabilidade"][$key] : ""));
		}

		foreach($benchmark as $key => $item)
		{
			echo form_default_color("benchmark_".$key, $item." :* ", (isset($cor["benchmark"][$key]) ? $cor["benchmark"][$key] : ""));
		}
		*/

		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();

$this->load->view("footer_interna");
?>