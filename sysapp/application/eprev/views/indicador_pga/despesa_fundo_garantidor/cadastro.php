<?php
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php 
		echo form_default_js_submit(array("cd_indicador_tabela", "mes_referencia", "ano_referencia", "nr_valor_1", "nr_valor_2", "nr_meta"),'_salvar(form);');	
	?>
	
	function _salvar(form)
	{
		
		if($('#nr_ano_periodo').val() != $('#ano_referencia').val())
		{
			alert("ERRO\n\nANO ("+$('#ano_referencia').val()+") do lan�amento diferente do ANO ("+$('#nr_ano_periodo').val()+") do per�odo\n\n");
			$('#ano_referencia').focus();
		}
		else
		{
			$('#dt_referencia').val('01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val());

			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}
	}	

    function ir_lista()
	{
		location.href='<?php echo site_url("indicador_pga/despesa_fundo_garantidor"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador_pga/despesa_fundo_garantidor"); ?>';
    }	
	
	function excluir()
	{
		if(confirm('Deseja Excluir?'))
		{
			location.href='<?php echo site_url("indicador_pga/despesa_fundo_garantidor/excluir/".$row["cd_despesa_fundo_garantidor"]); ?>';
		}
	}
	
	$(function() {
		$("#mes_referencia").focus();
	});	
</script>
<?php

if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum per�odo aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um per�odo aberto, no entanto s� ser� poss�vel incluir valores para o novo per�odo depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lan�amento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );

echo form_open('indicador_pga/despesa_fundo_garantidor/salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden('cd_indicador_tabela', 'C�digo indicador tabela', $tabela[0]['cd_indicador_tabela']);
		echo form_default_hidden('nr_ano_periodo', 'Ano refer�ncia per�odo', $tabela[0]['nr_ano_referencia']);
		echo form_default_hidden('cd_despesa_fundo_garantidor', 'C�digo da tabela', intval($row['cd_despesa_fundo_garantidor']));
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 		
		echo form_default_row("","","");
		echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.": (*)", $row['dt_referencia']);
		echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
		echo form_default_numeric("nr_valor_1", $label_1.": (*)", number_format($row['nr_valor_1'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_valor_2", $label_2.": (*)", number_format($row['nr_valor_2'],2,',','.'), "class='indicador_text'");
		echo form_default_numeric("nr_meta", $label_4.": (*)", number_format($row['nr_meta'],2,',','.'), "class='indicador_text'");
		echo form_default_textarea("observacao", $label_5.":", $row['observacao']);
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();

		echo button_save();

		if(intval($row['cd_despesa_fundo_garantidor']) > 0)
		{
			echo button_save('Excluir', 'excluir('.$row["cd_despesa_fundo_garantidor"].');', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
echo form_close();

echo aba_end();
$this->load->view('footer_interna');
?>
