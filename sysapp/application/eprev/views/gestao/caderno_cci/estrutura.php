<?php
set_title("Caderno CCI - Estrutura");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("ds_caderno_cci_estrutura"));
	?>

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		 	"CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "Number",
		    "Number",
		    "Number",
		    "Number",
		    "Number",
		    "Number",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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

	function cancelar()
	{
		location.href = "<?= site_url("gestao/caderno_cci/estrutura/".$row["cd_caderno_cci"]) ?>";
	}

	function valores()
	{
		location.href = "<?= site_url("gestao/caderno_cci/estrutura_valor/".$row["cd_caderno_cci"]) ?>";
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

	function excluir(cd_caderno_cci_estrutura)
	{	
		var confirmacao = "Deseja excluir a estrutura?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url("gestao/caderno_cci/estrutura_excluir/".$row["cd_caderno_cci"]) ?>/" + cd_caderno_cci_estrutura;
		}
	}

	$(function(){
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url("gestao/caderno_cci/estrutura_listar") ?>",
		{
			cd_caderno_cci : "<?= $row["cd_caderno_cci"] ?>"
		},
		function(data)
		{
			$("#result_div").html(data);
			//configure_result_table();
		});	
	});
</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", TRUE, "location.reload();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");
$abas[] = array("aba_rentabiliade", "Rentabilidade - Planos e Segmentos", FALSE, "ir_rentabilidade();");

$abas[] = array("aba_rentabiliade_planos", "Rentabilidade - Planos", FALSE, "ir_rentabilidade_planos();");
$abas[] = array("aba_rentabiliade_planos_aberto", "Rentabilidade - Planos (Aberto)", FALSE, "ir_rentabilidade_aberto();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/estrutura_salvar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci_estrutura", "", $estrutura);	
			echo form_default_hidden("cd_caderno_cci", "", $row);	
			echo form_default_row("nr_ano", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			echo form_default_text("ds_caderno_cci_estrutura", "Estrutura :*", $estrutura, 'style="width:300px;"');
			echo form_default_dropdown("cd_caderno_cci_estrutura_pai", "Estrutura Pai :", $estrutura_pai, $estrutura["cd_caderno_cci_estrutura_pai"]);
			echo form_default_integer("nr_ordem", "Ordem :", $estrutura);
			echo form_default_numeric("nr_rentabilidade", "Rentabilidade Esperada PI (%) :", number_format($estrutura['nr_rentabilidade'], 2, ",", "."));
			echo form_default_numeric("nr_politica_min", "Limite Política Mín. (%) :", number_format($estrutura['nr_politica_min'], 2, ",", "."));
			echo form_default_numeric("nr_politica_max", "Limite Política Máx. (%) :", number_format($estrutura['nr_politica_max'], 2, ",", "."));
			echo form_default_numeric("nr_legal_min", "Limite Legal Min. (%) :", number_format($estrutura['nr_legal_min'], 2, ",", "."));
			echo form_default_numeric("nr_legal_max", "Limite Legal Máx. (%) :", number_format($estrutura['nr_legal_max'], 2, ",", "."));
			echo form_default_numeric("nr_alocacao_estrategica", "Alocação Estratégica (%) :", number_format($estrutura['nr_alocacao_estrategica'], 2, ",", "."));
			echo form_default_dropdown("fl_grupo", "Grupo :", array(array("text" => "Não", "value" => "N"), array("text" => "Sim", "value" => "S")), $estrutura["fl_grupo"]);
			echo form_default_dropdown("fl_agrupar", "Agrupar :", array(array("text" => "Não", "value" => "N"), array("text" => "Sim", "value" => "S")), $estrutura["fl_agrupar"]);
			echo form_default_dropdown("fl_fundo", "Fundo de Investimento :", array(array("text" => "Não", "value" => "N"), array("text" => "Sim", "value" => "S")), $estrutura["fl_fundo"]);
			echo form_default_dropdown("fl_campo_metro", "Habilitar Campo M² :", array(array("text" => "Não", "value" => "N"), array("text" => "Sim", "value" => "S")), $estrutura["fl_campo_metro"]);
			echo form_default_dropdown("fl_campo_quantidade", "Habilitar Campo Quantidade :", array(array("text" => "Não", "value" => "N"), array("text" => "Sim", "value" => "S")), $estrutura["fl_campo_quantidade"]);

			echo form_default_dropdown("seq_estrutura", "Estrutura Oracle :", $estrutura_oracle, $estrutura["seq_estrutura"]);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
			
			if(intval($estrutura["cd_caderno_cci_estrutura"]) > 0)
			{
				echo button_save("Cancelar", "cancelar();", "botao_disabled");	
			}

			echo button_save("Valores", "valores()", "botao_verde");
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>