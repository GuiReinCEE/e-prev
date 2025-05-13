<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('mes_referencia', 'ano_referencia', 'cd_indicador_tabela'),'_salvar(form)') ?>

    function manutencao()
    {
        location.href = "<?= site_url('indicador/manutencao/index/13/A') ?>";
    }

	function ir_lista()
	{
		location.href = "<?= site_url('indicador_plugin/beneficio_inc_seprorgs') ?>";
	}

    function excluir()
    {
    	var confirmacao = "Excluir?\n\n"+
				    	 "[OK] para Sim\n\n"+
				    	 "[Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('indicador_plugin/beneficio_inc_seprorgs/excluir/'.$row['cd_beneficio_inc_seprorgs']) ?>";
		}
    }

	function _salvar(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	$(function(){
		$('#nr_valor_1').focus();
	});
</script>
<style>
	.aviso
	{
		width:100%; 
		text-align:center;
		font-size: 15px; 
		color:red; 
		font-weight:bold;
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', false, 'manutencao();');
	$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', true, 'location.reload();');

	echo aba_start($abas);
		echo form_open('indicador_plugin/beneficio_inc_seprorgs/salvar');
			echo form_default_hidden('cd_beneficio_inc_seprorgs', '', $row['cd_beneficio_inc_seprorgs']);
			echo form_start_box("default_box", $tabela[0]['ds_indicador']);
				echo form_default_hidden( 'cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela'] ); 
				echo form_default_row( "", "Indicador e período aberto:", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'].br(2) );
				if( count($tabela) == 0 )
				{
					echo form_default_row(  "", "", "<span class='aviso'>Nenhum período aberto para criar a tabela do indicador.</span>" );
					exit;
				}
				else if(count($tabela) > 1)
				{
					echo form_default_row( "", "", "<span class='aviso'>Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>");
					exit;
				}
				echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia']);
				echo form_default_hidden('dt_referencia', 'Mês', $row);
				echo form_default_float("nr_valor_1", $label_1.':', app_decimal_para_php($row['nr_valor_1']), "class='indicador_text'"); 
				echo form_default_float("nr_valor_2", $label_2.':', app_decimal_para_php($row['nr_valor_2']), "class='indicador_text'"); 
				echo form_default_float("nr_meta", $label_4.':', app_decimal_para_php($row['nr_meta']), "class='indicador_text'");
				echo form_default_textarea("observacao", $label_6.':', $row['observacao']);
			echo form_end_box("default_box");
			echo form_command_bar_detail_start();
				echo button_save();
				if(intval($row['cd_beneficio_inc_seprorgs']) > 0)
				{
					echo button_save('Excluir', 'excluir();', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();
	$this->load->view('footer');
?>