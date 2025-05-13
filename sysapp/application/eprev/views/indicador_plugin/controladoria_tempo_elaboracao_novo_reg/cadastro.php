<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("cd_indicador_tabela", "ds_evento", "nr_valor_1", "nr_meta"),'_salvar(form)');	?>

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
		location.href = '<?= site_url("indicador_plugin/controladoria_tempo_elaboracao_novo_reg") ?>';
	}
	
    function manutencao()
    {
        location.href = '<?= site_url("indicador/manutencao") ?>';
    }
	
	function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/controladoria_tempo_elaboracao_novo_reg/excluir/".$row["cd_controladoria_tempo_elaboracao_novo_reg"]) ?>';
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
	echo form_open('indicador_plugin/controladoria_tempo_elaboracao_novo_reg/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('nr_ano_referencia', '', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_controladoria_tempo_elaboracao_novo_reg', 'Código da tabela', intval($row['cd_controladoria_tempo_elaboracao_novo_reg']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_text('ds_evento', $label_0.' :*', $row['ds_evento'], "class='indicador_text'");
			echo form_default_integer("nr_valor_1", $label_1.' :', $row['nr_valor_1'], "class='indicador_text'"); 
			echo form_default_integer("nr_meta", $label_2.' :', $row['nr_meta'], "class='indicador_text'");
			echo form_default_textarea("observacao", $label_3.":", $row['observacao']);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_controladoria_tempo_elaboracao_novo_reg']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>