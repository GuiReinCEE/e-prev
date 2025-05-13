<?php
set_title("Caderno CCI - Estrutura Valor");
$this->load->view("header");
?>
<script>
	<?php
		echo form_default_js_submit(array("mes", "arquivo"));
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

	function ir_valores()
	{
		location.href = "<?= site_url('gestao/caderno_cci/estrutura_valor/'.$row['cd_caderno_cci'].'/'.$row['mes']) ?>";
	}

	function importa_valores(cd_caderno_cci_estrutura_pai)
	{
		location.href = "<?= site_url("gestao/caderno_cci/importa_valores/".$row["cd_caderno_cci"]."/".$row["mes"]) ?>";
	}

</script>
<?php
$abas[] = array("aba_lista", "Lista", FALSE, "ir_lista();");
$abas[] = array("aba_projetado", "Projetado", FALSE, "ir_projetado();");
$abas[] = array("aba_estrutura", "Estrutura", FALSE, "ir_estrutura();");
$abas[] = array("aba_cadastro", "Valores", FALSE, "ir_valores();");
$abas[] = array("aba_importar", "Importar", TRUE, "location.reload();");
$abas[] = array("aba_indice", "Índices", FALSE, "ir_indice();");
$abas[] = array("aba_grafico", "Configuração de Apresentação", FALSE, "ir_grafico();");
$abas[] = array("aba_relatorio", "Relatório", FALSE, "ir_relatorio();");

echo aba_start($abas);
	echo form_open("gestao/caderno_cci/salvar_anexo_importar");
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden("cd_caderno_cci", "", $row);
			echo form_default_hidden("cd_caderno_cci_estrutura_arquivo", "", $row);
			echo form_default_hidden("nr_ano", "", $row);	
			echo form_default_hidden("mes", "", $row);	
			echo form_default_row("", "Ano :", '<label class="label label-inverse">'.$row["nr_ano"]."</label>");
			echo form_default_row("", "Mês :", $row["ds_mes"]);
			echo form_default_upload_iframe('arquivo', 'caderno_cci_importe', 'Anexo:', array($row['arquivo'], $row['arquivo_nome']), 'caderno_cci_importe');
		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
			echo button_save("Salvar");	
			if(isset($importacao['collection']) AND count($importacao['collection']) > 0)
			{
				echo button_save("Importar", "importa_valores()", 'botao_verde');	
			}
		echo form_command_bar_detail_end();
	echo form_close();

	if(isset($importacao['collection']) AND count($importacao['collection']) > 0)
	{
		echo form_start_box("default_csv_box", "Informações do CSV");

			echo form_default_row("dt_inclusao_arquivo", "Dt. Inclusão Arquivo :", $row['dt_inclusao_arquivo']);
			echo form_default_row("qt_cci", "Qt. Estrutura CCI :", '<span class="label label-inverse">'.$importacao['qt_cci'].'</span>');
			echo form_default_row("qt_csv", "Qt. Estrutura CSV :", '<span class="label label-inverse">'.$importacao['qt_csv'].'</span>');
			echo form_default_row("qt_erro", "Qt. Erro :", '<span class="label label-important">'.$importacao['qt_erro'].'</span>');

		echo form_end_box("default_csv_box");

		$body = array();
		$head = array( 
			'Descrição CCI',
			'Descrição CSV',
			'Status',
			'Valor Atual',
			'Realizado',
			'Rentabilidade (%)'
		);

		foreach($importacao['collection'] as $item)
		{
			$body[] = array(
				array($item["ds_cci"], 'text-align:left'),
				array($item["ds_csv"], 'text-align:left'),
				'<span class="label label-'.(trim($item["fl_ok"]) == 'S' ? 'success' : 'important').'">'.(trim($item["fl_ok"]) == 'S' ? 'OK' : 'ERRO').'</span>',
				$item['nr_valor_atual'],
				$item['nr_realizado'],
				$item['nr_rentabilidade']
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();

	}
	echo br(5);
echo aba_end();
$this->load->view("footer_interna");
?>