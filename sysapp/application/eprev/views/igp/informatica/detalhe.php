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
					"nr_expediente",
					"nr_bco_fora",
					"nr_meta",
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
		location.href='<?php echo site_url("igp/informatica"); ?>';
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

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('igp/informatica/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('cd_informatica', 'Código da tabela', intval($row['cd_informatica']));

		echo form_default_text("", "Indicador:", $tabela[0]['ds_indicador'],'style="border: 0px; width: 500px; font-weight:bold;"'); 
		echo form_default_text("", "Período aberto:", $tabela[0]['ds_periodo'],'style="border: 0px; width: 500px; color:red; font-weight:bold;"'); 

		echo form_default_row("","","");
		
		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		echo form_default_integer("nr_expediente", $label_1.": (*)", intval($row['nr_expediente']), "class='indicador_text'");
		echo form_default_integer("nr_bco_fora", $label_2.": (*)", intval($row['nr_bco_fora']), "class='indicador_text'");
		echo form_default_numeric("nr_tempo_perc",  $label_3.": (*)", number_format($row['nr_tempo_perc'],2,',','.'), "class='indicador_text'"); 
		echo form_default_numeric("nr_meta", $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_peso", $label_8.": (*)", number_format($row['nr_peso'],2,',','.'), "class='indicador_text'");
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

		echo button_save();

		if(intval($row['cd_informatica']) > 0)
		{
			echo button_delete("igp/informatica/excluir", $row["cd_informatica"]);
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