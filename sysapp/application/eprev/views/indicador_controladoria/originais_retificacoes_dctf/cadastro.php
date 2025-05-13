<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php 
	echo form_default_js_submit(array("mes_referencia", "ano_referencia", "cd_indicador_tabela", "nr_original", "nr_retificacao_1", "nr_retificacao_2", "nr_retificacao_3", "nr_retificacao_4", "nr_retificacao_5", "nr_meta"),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_controladoria/originais_retificacoes_dctf"); ?>';
	}
	
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/index/14/A/"); ?>';
    }
	
	function excluir()
	{
		location.href = '<?php echo site_url("indicador_controladoria/originais_retificacoes_dctf/excluir/".$row["cd_originais_retificacoes_dctf"]); ?>';
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

$arr_value[] = array('value' => '1', 'text' => 'Sim');
$arr_value[] = array('value' => '0', 'text' => 'Não');

echo aba_start($abas);
	echo form_open('indicador_controladoria/originais_retificacoes_dctf/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('cd_originais_retificacoes_dctf', 'Código da tabela', intval($row['cd_originais_retificacoes_dctf']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' :*', $row['dt_referencia']);
            echo form_default_dropdown('nr_original', $label_1.' :*', $arr_value, $row['nr_original']);
            echo form_default_dropdown('nr_retificacao_1', $label_2.' :*', $arr_value, $row['nr_retificacao_1']);
            echo form_default_dropdown('nr_retificacao_2', $label_3.' :*', $arr_value, $row['nr_retificacao_2']);
            echo form_default_dropdown('nr_retificacao_3', $label_4.' :*', $arr_value, $row['nr_retificacao_3']);
            echo form_default_dropdown('nr_retificacao_4', $label_5.' :*', $arr_value, $row['nr_retificacao_4']);
            echo form_default_dropdown('nr_retificacao_5', $label_6.' :*', $arr_value, $row['nr_retificacao_5']);
			echo form_default_integer("nr_meta", $label_9.' :*', intval($row['nr_meta']), 'class="indicador_text"');
			echo form_default_textarea("observacao", $label_8.":", $row['observacao']);
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_originais_retificacoes_dctf']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>