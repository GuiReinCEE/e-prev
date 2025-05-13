<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("cd_indicador_tabela", "ds_evento", "nr_valor_1", "nr_valor_2", "nr_meta_f", "nr_meta"),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val('01/01/'+$('#nr_ano_referencia').val());

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url("indicador_plugin/controladoria_regul_solic_previc") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/controladoria_regul_solic_previc/excluir/".$row["cd_controladoria_regul_solic_previc"]) ?>';
	}

	function formatadoToValor(id)
    {
	    $('#vl_temp').val($('#' + id).val());
	    $('#vl_temp').priceFormat({prefix: '',centsSeparator: '.',thousandsSeparator: ''});   
	    
	    var vl_valor = parseFloat($('#vl_temp').val());
	    //vl_valor = roundValor(vl_valor, 2); 
	    
	    return vl_valor;
    }

	function valorToFormatado(valor)
    {
	    //valor = roundValor(valor, 2);
	    
	    if(valor.toString().indexOf(".") > 0)
	    {
           var ar_tmp = valor.toString().split(".");
           ar_tmp[1] = ar_tmp[1].substring(0, 2);
           
           var preenche = ar_tmp[1].length;
           
           for(x = 0; x < (2 - preenche); x++)
           {
               ar_tmp[1] += "0";
           }
           
           valor = ar_tmp[0] + "." + ar_tmp[1];
	    }
	    else
	    {           
           valor += ".00";
	    }
	    
	    $('#vl_temp').val(valor);
	    $('#vl_temp').priceFormat({ prefix: '',centsSeparator: ',',thousandsSeparator: '.' }); 
	    
	    return $('#vl_temp').val();
    }
		
	$(function() {
		$("#mes_referencia").focus();
		
		var nr_meta_antiga = 20.00;

		$("#nr_valor_1").keyup(function(){
			console.log("a");
			var nr_valor_1 = parseInt($(this).val());

			var nr_meta = (100 / nr_valor_1);
			
			if(nr_meta > 20)
			{
				$("#nr_meta").val(valorToFormatado(nr_meta));
			}
			else
			{
				$("#nr_meta").val(valorToFormatado(nr_meta_antiga));
			}

			var nr_meta_f = ((parseInt($("#nr_valor_1").val()) * formatadoToValor("nr_meta")) / 100);

			var nr_meta = ((Math.trunc(nr_meta_f) * 100) / parseInt($("#nr_valor_1").val()));

			$("#nr_meta").val(valorToFormatado(nr_meta));

			if(!isNaN(nr_meta_f))
			{
				$("#nr_meta_f").val(Math.trunc(nr_meta_f));
			}
			else
			{
				$("#nr_meta_f").val("");
			}
		});
		
	});
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

$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('indicador_plugin/controladoria_regul_solic_previc/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('vl_temp', "", "");
			echo form_default_hidden('nr_ano_referencia', '', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_controladoria_regul_solic_previc', 'Código da tabela', intval($row['cd_controladoria_regul_solic_previc']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_text('ds_evento', $label_0.' :*', $row['ds_evento'], "class='indicador_text'");
			echo form_default_integer("nr_valor_1", $label_1.' :', ($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_integer("nr_valor_2", $label_2.' :', ($row['nr_valor_2']), "class='indicador_text'");
			echo form_default_integer("nr_meta_f", $label_3.' :', ($row['nr_meta_f']), "class='indicador_text' readonly");
			echo form_default_textarea("observacao", $label_5." :", $row['observacao']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_controladoria_regul_solic_previc']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>