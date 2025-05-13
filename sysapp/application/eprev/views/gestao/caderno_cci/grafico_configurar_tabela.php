<?php
set_title("Caderno CCI - Configuração de Tabela");
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
	echo form_open("gestao/caderno_cci/grafico_configurar_tabela_salvar");
		
		echo form_start_box("default_box", "Gráfico");	
			echo form_default_row("", "Nome do Gráfico :", $grafico["ds_caderno_cci_grafico"]);
		echo form_end_box("default_box");

		echo form_start_box("default_box", "Campos de Exibição");	
			echo form_default_hidden("cd_caderno_cci_grafico", "", $grafico);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_checkbox_group("arr_campo", "Campos :", $arr_campo, $grafico["campo"], 130);
		echo form_end_box("default_box");
	
			if(isset($rentabilidade))
			{
				foreach ($rentabilidade as $key => $item) 
				{
					$drop = $arr_rentabilidade_drop;

					$check = array();

					if(isset($grafico["participacao"][$item["cd_caderno_cci_estrutura"]]))
					{
						$check = $grafico["participacao"][$item["cd_caderno_cci_estrutura"]];
					}

					$check2 = array();

					if(isset($grafico["participacao_m2"][$item["cd_caderno_cci_estrutura"]]))
					{
						$check2 = $grafico["participacao_m2"][$item["cd_caderno_cci_estrutura"]];
					}

					echo form_start_box("default_box", $item["ds_caderno_cci_estrutura"]);	
						echo form_default_numeric("rentabilidade_ordem[".$item["cd_caderno_cci_estrutura"]."]", "Ordem :", (isset($ordem["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? $ordem["rentabilidade"][$item["cd_caderno_cci_estrutura"]]: ""));
						echo form_default_dropdown("rentabilidade_part[".$item["cd_caderno_cci_estrutura"]."]", "Relação para Part. (%) :", $drop, $check);
						echo form_default_dropdown("rentabilidade_partm2[".$item["cd_caderno_cci_estrutura"]."]", "Relação para Part. M2 (%) :", $drop, $check2);
						echo form_default_dropdown("rentabilidade_negrito[".$item["cd_caderno_cci_estrutura"]."]", "Negrito :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($negrito["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? array($negrito["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) : ""));
						echo form_default_dropdown("rentabilidade_linha[".$item["cd_caderno_cci_estrutura"]."]", "Destacar Linha :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($linha["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? array($linha["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) : ""));
						echo form_default_numeric("rentabilidade_tab[".$item["cd_caderno_cci_estrutura"]."]", "Espaço :", (isset($tab["rentabilidade"][$item["cd_caderno_cci_estrutura"]]) ? $tab["rentabilidade"][$item["cd_caderno_cci_estrutura"]]: ""));
					echo form_end_box("default_box");
				}
			}

			if(isset($projetado))
			{
				foreach ($projetado as $key => $item) 
				{
					echo form_start_box("default_box", $item["ds_caderno_cci_projetado"]);	
						echo form_default_numeric("projetado_ordem[".$item["cd_caderno_cci_projetado"]."]", "Ordem :", (isset($ordem["projetado"][$item["cd_caderno_cci_projetado"]]) ? $ordem["projetado"][$item["cd_caderno_cci_projetado"]]: ""));
						echo form_default_dropdown("projetado_negrito[".$item["cd_caderno_cci_projetado"]."]", "Negrito :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($negrito["projetado"][$item["cd_caderno_cci_projetado"]]) ? array($negrito["projetado"][$item["cd_caderno_cci_projetado"]]) : ""));
						echo form_default_dropdown("projetado_linha[".$item["cd_caderno_cci_projetado"]."]", "Destacar Linha :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($linha["projetado"][$item["cd_caderno_cci_projetado"]]) ? array($linha["projetado"][$item["cd_caderno_cci_projetado"]]) : ""));
						echo form_default_numeric("projetado_tab[".$item["cd_caderno_cci_projetado"]."]", "Espaço :", (isset($tab["projetado"][$item["cd_caderno_cci_projetado"]]) ? $tab["projetado"][$item["cd_caderno_cci_projetado"]]: ""));
					echo form_end_box("default_box");
				}
			}

			if(isset($indice))
			{
				foreach ($indice as $key => $item) 
				{
					echo form_start_box("default_box", $item["ds_caderno_cci_indice"]);	
						echo form_default_numeric("indice_ordem[".$item["cd_caderno_cci_indice"]."]", "Ordem :", (isset($ordem["indice"][$item["cd_caderno_cci_indice"]]) ? $ordem["indice"][$item["cd_caderno_cci_indice"]] : ""));
						echo form_default_dropdown("indice_negrito[".$item["cd_caderno_cci_indice"]."]", "Negrito :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($negrito["indice"][$item["cd_caderno_cci_indice"]]) ? array($negrito["indice"][$item["cd_caderno_cci_indice"]]) : ""));
						echo form_default_dropdown("indice_linha[".$item["cd_caderno_cci_indice"]."]", "Destacar Linha :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($linha["indice"][$item["cd_caderno_cci_indice"]]) ? array($linha["indice"][$item["cd_caderno_cci_indice"]]) : ""));
						echo form_default_numeric("indice_tab[".$item["cd_caderno_cci_indice"]."]", "Espaço :", (isset($tab["indice"][$item["cd_caderno_cci_indice"]]) ? $tab["indice"][$item["cd_caderno_cci_indice"]]: ""));
					echo form_end_box("default_box");
				}
			}

			if(isset($benchmark))
			{
				foreach ($benchmark as $key => $item) 
				{
					echo form_start_box("default_box", $item["ds_caderno_cci_benchmark"]);	
						echo form_default_numeric("benchmark_ordem[".$item["cd_caderno_cci_benchmark"]."]", "Ordem :", (isset($ordem["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? $ordem["benchmark"][$item["cd_caderno_cci_benchmark"]] : "")   );
						echo form_default_dropdown("benchmark_negrito[".$item["cd_caderno_cci_benchmark"]."]", "Negrito :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($negrito["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? array($negrito["benchmark"][$item["cd_caderno_cci_benchmark"]]) : ""));
						echo form_default_dropdown("benchmark_linha[".$item["cd_caderno_cci_benchmark"]."]", "Destacar Linha :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), (isset($linha["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? array($linha["benchmark"][$item["cd_caderno_cci_benchmark"]]) : ""));
						echo form_default_numeric("benchmark_tab[".$item["cd_caderno_cci_benchmark"]."]", "Espaço :", (isset($tab["benchmark"][$item["cd_caderno_cci_benchmark"]]) ? $tab["benchmark"][$item["cd_caderno_cci_benchmark"]]: ""));
					echo form_end_box("default_box");
				}
			}
		
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>