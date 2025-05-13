<?php 
set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("mes_referencia","ano_referencia","cd_indicador_tabela"),'_salvar(form)'); ?>

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
		location.href='<?php echo site_url("indicador_plugin/juridico_evo_acoes_jud"); ?>';
	}
	
	function excluir()
	{
		location.href='<?php echo site_url("indicador_plugin/juridico_evo_acoes_jud/excluir/".intval($row["cd_juridico_evo_acoes_jud"])); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lancamento', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('indicador_plugin/juridico_evo_acoes_jud/salvar');
		echo form_start_box("default_box", 'Cadastro');
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);
			echo form_default_hidden('nr_ano_periodo', 'Ano referência período', $tabela[0]['nr_ano_referencia']);
			echo form_default_hidden('cd_juridico_evo_acoes_jud', 'Código da tabela', intval($row['cd_juridico_evo_acoes_jud']));
			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', "dt_referencia: (*)", $row); 
			
			echo form_default_integer('ano_referencia', $label_0.': (*)', (intval($row['ano_referencia']) == 0 ? intval($tabela[0]['nr_ano_referencia']) : intval($row['ano_referencia'])));
			echo form_default_hidden('mes_referencia', "mes_referencia", 1);
			
			echo form_default_integer("nr_valor_1", $label_1.': (*)', intval($row['nr_valor_1']), "class='indicador_text'"); 
			echo form_default_textarea("observacao", $label_5.':', $row['observacao'], 'style="height: 80px;"');
			echo form_default_row("", "",'<span style="font-size: 130%; font-weight: bold; color:red; display:none;" id="msg_importar">Aguarde, importando os dados.</span>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_juridico_evo_acoes_jud']) > 0)
			{
				echo button_save('Excluir', 'excluir();', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>