<?php
set_title('Eleições - Kit');
$this->load->view('header');
?>
<script>
	<?php
	echo form_default_js_submit(Array('nr_kit'));
	?>
	
	function imprimirKit(form)
	{
		form.target = "_blank";
	}
	
    function lista()
    {
        location.href='<?php echo site_url("gestao/eleicoes_cadastro"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Kit', TRUE, 'location.reload();');

echo aba_start($abas);

echo form_open('gestao/eleicoes_cadastro/imprimirKit');
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
		
		
		echo form_default_integer('nr_kit', 'Nr kit:*');
		
		
	echo form_end_box("default_box");
	
	echo form_command_bar_detail_start();
		if(($row["situacao"] != "") and ($row["fl_codigo_barra"] == "S") and ($row["fl_atualiza_oracle"] == "S"))
		{
			echo button_save("Imprimir","imprimirKit(this.form);");
		}
    echo form_command_bar_detail_end();
	
	echo br(2);
echo form_close();
echo aba_end();