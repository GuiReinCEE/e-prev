<?php
set_title("Caderno CCI - Estrutura Valor");
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

	function ir_relatorio()
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
		location.href = "<?= site_url("gestao/caderno_cci/estrutura_valor/".$row["cd_caderno_cci"]) ?>/"+mes;
	}

	function ir_estrutura_pai(cd_caderno_cci_estrutura_pai)
	{
		location.href = "<?= site_url("gestao/caderno_cci/estrutura_valor/".$row["cd_caderno_cci"]."/".$row["mes"]) ?>/"+cd_caderno_cci_estrutura_pai;
	}

	function importar()
	{
		location.href = "<?= site_url('gestao/caderno_cci/importar/'.$row['cd_caderno_cci'].'/'.$row['mes']) ?>";
	}

	function carrega_valores()
	{
		$("#msg_importar").show();	
			
		$("#command_bar").hide();

		$.post('<?= site_url("gestao/caderno_cci/get_valores_oracle") ?>', 
		{
			nr_ano                       : "<?= $row["nr_ano"] ?>",
			nr_mes                       : "<?= $row["mes"] ?>",
			cd_caderno_cci_estrutura_pai : <?= $cd_caderno_cci_estrutura_pai ?>,
			cd_caderno_cci               : <?= $row["cd_caderno_cci"] ?>
		},
		function(data)
		{
			if(data)
			{
				$.each(data, function(i, item) {
				    $("#"+item.campo).val(item.valor);
				});
			}

			$("#msg_importar").hide();	
			$("#command_bar").show();
		
		},'json');
	}
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_cadastro", "Valores", TRUE, "location.reload();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");
$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/estrutura_valor_salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_hidden("nr_ano", "", $row);	
			echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			echo form_default_dropdown("mes", "Mês :*", $mes, $row["mes"], 'onchange="carrega_mes($(this).val());"');
		echo form_end_box("default_box");

		if(count($collection) > 0 AND trim($row["mes"]) != "")
		{

			if(count($collection) > 0 AND trim($row["mes"]) != "")
			{
				echo form_command_bar_detail_start();
					echo button_save("Importar CSV", "importar()");	
				echo form_command_bar_detail_end();
			}

			echo aba_start($estrutura_aba);
			$i = 0;

			foreach($collection as $item)
			{
				echo form_start_box("default_box_".$i, trim($item["ds_caderno_cci_estrutura"]));
					echo form_default_hidden("caderno_cci_estrutura[".intval($item["cd_caderno_cci_estrutura"])."]", "", intval($item["cd_caderno_cci_estrutura_valor"]));
					echo form_default_hidden("caderno_cci_estrutura_nivel[".intval($item["cd_caderno_cci_estrutura"])."]", "", intval($item["nivel"]));
					
					echo form_default_numeric("nr_valor_atual_".intval($item["cd_caderno_cci_estrutura"]), "Valor Atual :" , number_format($item["nr_valor_atual"], 2, ",", "."));
					echo form_default_numeric("nr_realizado_".intval($item["cd_caderno_cci_estrutura"]), "Realizado :", number_format($item["nr_realizado"], 2, ",", "."));
					echo form_default_numeric("nr_rentabilidade_".intval($item["cd_caderno_cci_estrutura"]), "Rentabilidade (%):", number_format($item["nr_rentabilidade"], 4, ",", "."), "", array("centsLimit" => 4));
					echo form_default_hidden("fl_pai_".intval($item["cd_caderno_cci_estrutura"]), "", "N");

					/*
					if($item["total_filho"] == 0)
					{
						echo form_default_numeric("nr_valor_atual_".intval($item["cd_caderno_cci_estrutura"]), "Valor Atual :" , $item["nr_valor_atual"]);
						echo form_default_numeric("nr_realizado_".intval($item["cd_caderno_cci_estrutura"]), "Realizado :", $item["nr_realizado"]);
						echo form_default_numeric("nr_rentabilidade_".intval($item["cd_caderno_cci_estrutura"]), "Rentabilidade (%):", $item["nr_rentabilidade"], "", array("centsLimit" => 4));
						echo form_default_hidden("fl_pai_".intval($item["cd_caderno_cci_estrutura"]), "", "N");
					}
					else
					{
						echo form_default_row("", "Valor Atual :", number_format($item["nr_valor_atual"], 2, ",", "."));
						echo form_default_row("", "Realizado :", number_format($item["nr_realizado"], 2, ",", "."));
						echo form_default_row("", "Rentabilidade (%) :", number_format($item["nr_rentabilidade"], 4, ",", "."));
						echo form_default_hidden("fl_pai_".intval($item["cd_caderno_cci_estrutura"]), "", "S");
					}
					*/
					#echo form_default_numeric("nr_fluxo_".intval($item["cd_caderno_cci_estrutura"]), "Fluxo de Caixa :", $item["nr_fluxo"]);
					
					if(trim($item["fl_fundo"]) == "S")
					{
						echo form_default_numeric("nr_valor_integralizar_".intval($item["cd_caderno_cci_estrutura"]), "Valor a Integralizar :", $item["nr_valor_integralizar"]);
						echo form_default_numeric("nr_taxa_adm_".intval($item["cd_caderno_cci_estrutura"]), "Taxa de Adm. (%) :", $item["nr_taxa_adm"]);
						echo form_default_integer("nr_ano_vencimento_".intval($item["cd_caderno_cci_estrutura"]), "Vencimento :", $item["nr_ano_vencimento"]);
						echo form_default_numeric("nr_participacao_fundo_".intval($item["cd_caderno_cci_estrutura"]), "Part. no Fundo (%) :", $item["nr_participacao_fundo"]);
					}

					if(trim($item["fl_campo_metro"]) == "S")
					{
						echo form_default_integer("nr_metro_".intval($item["cd_caderno_cci_estrutura"]), "M² :", intval($item["nr_metro"]));
						/*
						if($item["total_filho"] == 0)
						{
							echo form_default_integer("nr_metro_".intval($item["cd_caderno_cci_estrutura"]), "M² :", intval($item["nr_metro"]));
						}
						else
						{
							echo form_default_row("", "M² :", intval($item["nr_metro"]));
						}
						*/
					}

					if(trim($item["fl_campo_quantidade"]) == "S")
					{
						echo form_default_numeric("nr_quantidade_".intval($item["cd_caderno_cci_estrutura"]), "Quantidade :", $item["nr_quantidade"]);
					}

				echo form_end_box("default_box_".$i);

				$i++;
			}
		}
		echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_command_bar_detail_start();
			if(count($collection) > 0 AND trim($row["mes"]) != "")
			{
				echo button_save("Salvar");	
				echo button_save("Carregar Valores", "carrega_valores()", "botao_verde");	
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>