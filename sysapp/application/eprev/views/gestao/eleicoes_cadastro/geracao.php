<?php
set_title('Eleições - Geração');
$this->load->view('header');
?>
<script>
	function importaOracle(form)
	{
		if(confirm("Importar CADASTRO ORACLE.\n\nConfirma?"))
		{		
			form.action = form.action + "/importaOracle";
			form.submit();
		}
	}

	function geraNumeroControle(form)
	{
		if(confirm("Gerar NÚMERO DE CONTROLE.\n\nConfirma?"))
		{
			form.action = form.action + "/geraNumeroControle";
			form.submit();
		}
	}

	function geraCodigoBarra(form)
	{
		if(confirm("Gerar CÓDIGO DE BARRAS.\n\nConfirma?"))
		{
			form.action = form.action + "/geraCodigoBarra";
			form.submit();
		}
	}	
	
	function atualizaOracle(form)
	{
		if(confirm("ATUALIZAR Oracle.\n\nConfirma?"))
		{
			form.action = form.action + "/atualizaOracle";
			form.submit();
		}
	}
	
	
    function lista()
    {
        location.href='<?php echo site_url("gestao/eleicoes_cadastro"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Geração', TRUE, 'location.reload();');

echo aba_start($abas);

echo form_open('gestao/eleicoes_cadastro/');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('id_eleicao', '', $row['id_eleicao']);
		echo form_default_text("nome", "Descrição:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("status", "Status:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("qt_cadastro", "Qt Cadastro:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("nr_controle", "Nr Controle:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("fl_codigo_barra", "Gerou Código Barra:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("fl_atualiza_oracle", "Atualizou Oracle:", $row, 'style="width:500px; border:0" readonly=""');

		echo form_default_text("num_votos", "Kits recebidos:", number_format($row["num_votos"],0,",","."), 'style="width:500px; border:0" readonly=""');
		echo form_default_text("invalidados", "Kits inválidos:", number_format($row["invalidados"],0,",","."), 'style="width:500px; border:0" readonly=""');
		echo form_default_text("votos_apurados", "Votos válidos:", number_format($row["votos_apurados"],0,",","."), 'style="width:500px; border:0" readonly=""');
		echo form_default_text("dt_hr_abertura", "Dt Abertura:", $row, 'style="width:500px; border:0" readonly=""');
		echo form_default_text("dt_hr_fechamento", "Dt Encerramento:", $row, 'style="width:500px; border:0" readonly=""');

	echo form_end_box("default_box");
	
	echo form_command_bar_detail_start();
		if(($row["situacao"] == "") or ($row["situacao"] == "G"))
		{
			echo button_save("Importar Cadastro Oracle","importaOracle(this.form);","botao_vermelho");
			
			/*
			if((intval($row['qt_cadastro']) > 0) and ($row["fl_codigo_barra"] == "N"))
			{
				echo button_save("Gerar Número de Controle","geraNumeroControle(this.form);");
			}
			
			if((intval($row['nr_controle']) > 0) and ($row["fl_codigo_barra"] == "N"))
			{
				echo button_save("Gerar Código de Barra","geraCodigoBarra(this.form);");
			}
			
			if((intval($row['nr_controle']) > 0) and (intval($row['qt_cadastro']) > 0) and ($row["fl_codigo_barra"] == "S") and ($row["fl_atualiza_oracle"] == "N"))
			{		
				echo button_save("Atualizar Cadastro Oracle","atualizaOracle(this.form);","botao_vermelho");
			}
			*/
		}
		
    echo form_command_bar_detail_end();
	
	echo br(2);
echo form_close();
echo aba_end();