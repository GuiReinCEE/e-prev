<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_abertas_mes", "nr_atendidas_mes"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_plugin/info_atividade"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_plugin/info_atividade/excluir/".$row["cd_info_atividade"]); ?>';
	}
	
	function getValores()
	{
		if(($("#mes_referencia").val() != "") && ($("#ano_referencia").val() != ""))
		{
			$("#msg_importar").show();	
			
			$("#command_bar").hide();
			
			$.post('<?php echo site_url("indicador_plugin/info_atividade/get_valores"); ?>', 
			{
				nr_ano : $("#ano_referencia").val(),
				nr_mes : $("#mes_referencia").val()
			},
			function(data)
			{
				if(data)
				{
					$("#nr_abertas_mes").val(data.nr_abertas_mes);
					$("#nr_atendidas_mes").val(data.nr_atendidas_mes);
				}
				
				getObservacao();
				
				$("#msg_importar").hide();	
				$("#command_bar").show();
			},
			'json');
		}
		else
		{
			alert("Informe o Mês e Ano");
		}
	}
	
	function getObservacao()
	{
		$.post('<?php echo site_url("atividade/atividade_dashboard/monitor"); ?>', 
		{},
		function(data)
		{
			if(data)
			{
				//{"ret":{"qt_backlog":125,"qt_andamento":24,"qt_teste":31,"qt_usuario":8,"qt_total":188}}
				
				var dt_hoje = new Date();
				
				var obs = "- Atividades em ANDAMENTO: " + (data.qt_backlog + data.qt_andamento) + "\n";
				    obs+= "- Atividades em TESTE: " + (data.qt_teste) + "\n";
				    obs+= "- Atividades AGUARDANDO USUÁRIO: " + (data.qt_usuario) + "\n";
				    obs+= "(" + (dt_hoje.toLocaleDateString()) + ")\n";
				
				$("#observacao").val(obs);
			}
		},
		'json');
	}	
			
	$(function() {
		$("#mes_referencia").focus();
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
	echo form_open('indicador_plugin/info_atividade/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_info_atividade', 'Código da tabela', intval($row['cd_info_atividade']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
			echo form_default_float("nr_abertas_mes", $label_1.' :', app_decimal_para_php($row['nr_abertas_mes']), "class='indicador_text'"); 
			echo form_default_float("nr_atendidas_mes", $label_2.' :', app_decimal_para_php($row['nr_atendidas_mes']), "class='indicador_text'"); 
			echo form_default_float("nr_meta", $label_7.' :', app_decimal_para_php($row['nr_meta']), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_8.' :', $row['observacao']);
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			echo button_save('Importar Valores', 'getValores();', 'botao_verde');

			if(intval($row['cd_info_atividade']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>