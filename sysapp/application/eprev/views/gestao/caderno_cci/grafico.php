<?php
set_title("Caderno CCI - Configuração de Gráfico");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("ds_caderno_cci_garfico", "nr_ordem", "tp_grafico", "grafico"));
	?>

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			null,
		    "CaseInsensitiveString",
			null
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
		ob_resul.sort(0, false);
	}

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

	function cancelar()
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico/".$row["cd_caderno_cci"]) ?>";
	}

	function configurar(cd_caderno_cci_grafico)
	{
		location.href = "<?= site_url("gestao/caderno_cci/grafico_configurar/".$row["cd_caderno_cci"]) ?>/"+ cd_caderno_cci_grafico;
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

	function excluir(cd_caderno_cci_grafico)
	{	
		var confirmacao = "Deseja excluir o gráfico?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url("gestao/caderno_cci/grafico_excluir/".$row["cd_caderno_cci"]) ?>/" + cd_caderno_cci_grafico;
		}
	}

	function editar_ordem(cd_caderno_cci_grafico)
	{
		$("#valor_ordem_" + cd_caderno_cci_grafico).hide(); 
		$("#editar_ordem_" + cd_caderno_cci_grafico).hide(); 

		$("#salvar_ordem_" + cd_caderno_cci_grafico).show(); 
		$("#nr_ordem_" + cd_caderno_cci_grafico).show(); 
		$("#nr_ordem_" + cd_caderno_cci_grafico).focus();	
	}

	function set_ordem(cd_caderno_cci_grafico)
    {
        $("#ajax_ordem_valor_"+cd_caderno_cci_grafico).html("<?= loader_html("P") ?>");
        
        $.post("<?= site_url("gestao/caderno_cci/grafico_salvar_ordem") ?>",
        {
            cd_caderno_cci_grafico : cd_caderno_cci_grafico,
            nr_ordem               : $("#nr_ordem_" + cd_caderno_cci_grafico).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_caderno_cci_grafico).empty();
			
			$("#nr_ordem_" + cd_caderno_cci_grafico).hide();
			
			$("#salvar_ordem_" + cd_caderno_cci_grafico).hide(); 
			
            $("#valor_ordem_" + cd_caderno_cci_grafico).html($("#nr_ordem_" + cd_caderno_cci_grafico).val()); 
			$("#valor_ordem_" + cd_caderno_cci_grafico).show(); 

			$("#editar_ordem_" + cd_caderno_cci_grafico).show(); 
        });
    }	

	$(function(){
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url("gestao/caderno_cci/grafico_listar") ?>",
		{
			cd_caderno_cci : "<?= $row["cd_caderno_cci"] ?>"
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
	});

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", TRUE, "location.reload();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");
$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

$tp_grafico = array(
	array("value" => "E", "text" => "Texto"), 
	array("value" => "B", "text" => "Barra"), 
	array("value" => "A", "text" => "Barra Agrupada"),
	array("value" => "L", "text" => "Linha"), 
	array("value" => "P", "text" => "Pizza"), 
	array("value" => "T", "text" => "Tabela"), 
	array("value" => "R", "text" => "Rentabilidade Histórica")
);

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/grafico_salvar");
		
		echo form_start_box("default_box", "Cadastro");	
			echo form_default_hidden("cd_caderno_cci_grafico", "", $grafico);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_text("ds_caderno_cci_grafico", "Nome do Gráfico :*", $grafico, 'style="width:300px;"');
			echo form_default_integer("nr_ordem", "Ordem :*", $grafico);
			echo form_default_dropdown("tp_grafico", "Tipo de Gráfico :*", $tp_grafico, $grafico["tp_grafico"]);
			echo form_default_dropdown("fl_mes", "Mês Referência :", array(array("value" => "C", "text" => "Corrente"), array("value" => "P", "text" => "Próximo Mês"), array("value" => "D", "text" => "Mais Dois Meses")), $grafico["fl_mes"]);
			echo form_default_dropdown("fl_ano", "Comparar com Ano Anterior :", array(array("value" => "N", "text" => "Não"), array("value" => "S", "text" => "Sim")), $grafico["fl_ano"]);
			echo form_default_checkbox_group("arr_projetado", "Projetado :", $projetado, $grafico["projetado"], 120);
			echo form_default_checkbox_group("arr_rentabilidade", "Rentabilidade :", $rentabilidade, $grafico["rentabilidade"], 120);
			echo form_default_checkbox_group("arr_indice", "Índice :", $indice, $grafico["indice"], 120);
			echo form_default_checkbox_group("arr_benchmark", "Benchmark :", $benchmark, $grafico["benchmark"], 120);
			echo form_default_textarea("nota_rodape", "Nota de Rodapé :", $grafico);
		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			echo button_save("Salvar");	

			if(intval($grafico["cd_caderno_cci_grafico"]) > 0)
			{
				echo button_save("Cancelar", "cancelar();", "botao_disabled");	
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>