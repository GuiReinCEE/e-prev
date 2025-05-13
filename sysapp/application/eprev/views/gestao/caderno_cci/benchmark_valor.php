<?php
set_title("Caderno CCI - Benchmark Valor");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("mes"));
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

	function ir_benchmark()
	{
		location.href = "<?= site_url("gestao/caderno_cci/benchmark/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_relatorio(mes)
	{
		location.href = "<?= site_url("gestao/caderno_cci_relatorio/index/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_grafico()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos_segmentos/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade_planos()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos/".$row["cd_caderno_cci"]) ?>";
	}

	function ir_rentabilidade_aberto()
	{
		location.href = "<?= site_url("gestao/caderno_cci/rentabilidade_planos_aberto/".$row["cd_caderno_cci"]) ?>";
	}

	function carrega_mes(mes)
	{
		location.href = "<?= site_url("gestao/caderno_cci/benchmark_valor/".$row["cd_caderno_cci"]) ?>/"+mes;
	}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", TRUE, "location.reload();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");

$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

$abas2[] = array("aba_indice2", "Índice", FALSE, "ir_indice();");
$abas2[] = array("aba_benchmark", "Benchmark", FALSE, "ir_benchmark();");
$abas2[] = array("aba_cadastro", "Valores", TRUE, "location.reload();");

echo aba_start($abas);
echo aba_start($abas2);
	echo form_open("gestao/caderno_cci/benchmark_valor_salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_hidden("nr_ano", "", $row);	
			echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			echo form_default_dropdown("mes", "Mês :*", $mes, $row["mes"], 'onchange="carrega_mes($(this).val());"');

			if(count($collection) > 0 AND trim($row["mes"]) != "")
			{
				foreach($collection as $item)
				{
					echo form_default_hidden("caderno_cci_benchmark[".intval($item["cd_caderno_cci_benchmark"])."]", "", intval($item["cd_caderno_cci_benchmark_valor"]));
					echo form_default_numeric("caderno_cci_benchmark_".intval($item["cd_caderno_cci_benchmark"]), (trim($item["nr_ordem"]) != "" ? $item["nr_ordem"]." - " : "").$item["ds_caderno_cci_benchmark"]." :" , $item["nr_benchmark"], "", array("centsLimit" => 4));
				}
			}

		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if(count($collection) > 0 AND trim($row["mes"]) != "")
			{
				echo button_save("Salvar");	
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>