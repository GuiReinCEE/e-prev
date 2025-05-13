<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header');
?>
<script>
	<?php 
		echo form_default_js_submit(
			array(
					"cd_indicador_tabela",
					"mes_referencia",
		            "ano_referencia",
		            "nr_hora",
		            "nr_homem",
		            "nr_meta",
		            "nr_meta_ano",
		            "nr_peso"
	              ),
				  '_salvar(form);'
				);	
	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("igp/treinamento"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }	
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum período aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lanca', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('igp/treinamento/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('cd_treinamento', 'Código da tabela', intval($row['cd_treinamento']));

		echo form_default_text("", "Indicador:", $tabela[0]['ds_indicador'],'style="border: 0px; width: 500px; font-weight:bold;"'); 
		echo form_default_text("", "Período aberto:", $tabela[0]['ds_periodo'],'style="border: 0px; width: 500px; color:red; font-weight:bold;"'); 

		echo form_default_row("","","");

		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 

		echo form_default_integer("nr_hora", $label_1.": (*)", number_format($row['nr_hora'],0,',','.'), "class='indicador_text'");
		echo form_default_integer("nr_homem", $label_2.": (*)", number_format($row['nr_homem'],0,'',''), "class='indicador_text'");
		echo form_default_numeric("nr_meta", $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta_ano", $label_15.": (*)", number_format($row['nr_meta_ano'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_peso", $label_8.": (*)", number_format($row['nr_peso'],2,',','.'), "class='indicador_text'");


	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
	
		echo button_save();

		if(intval($row['cd_treinamento']) > 0)
		{
			echo button_delete("igp/treinamento/excluir",$row["cd_treinamento"]);
		}
	echo form_command_bar_detail_end();
echo form_close();
?>
<script>
	$(document).ready(function() {
		$("#mes_referencia").focus();
	});	
</script>
<?php
echo aba_end();
$this->load->view('footer_interna');
?>