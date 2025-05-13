<?php 
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("mes_referencia", "ano_referencia", "nr_participante", "nr_satisfeito", "nr_avaliacao"));?>

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/atend_satisfacao_seminario_seguridade"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("indicador_plugin/atend_satisfacao_seminario_seguridade/excluir/".$row['cd_atend_satisfacao_seminario_seguridade']); ?>';
		}
	}
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open( 'indicador_plugin/atend_satisfacao_seminario_seguridade/salvar' );
		echo form_start_box( "default_box", $tabela[0]['ds_indicador'] );
			echo form_hidden('cd_atend_satisfacao_seminario_seguridade', intval($row['cd_atend_satisfacao_seminario_seguridade']));
			echo form_hidden('cd_indicador_tabela', $tabela[0]['cd_indicador_tabela']); 
			
			if( sizeof($tabela)==1 )
			{
				echo form_default_row("", "Indicador e período aberto", $tabela[0]['ds_indicador'].' - '.$tabela[0]['ds_periodo'].br(2));
			}
			elseif( sizeof($tabela)>1 )
			{
				echo form_default_row("", "Indicador e período aberto", $tabela[0]['ds_indicador'].' - '.$tabela[0]['ds_periodo']);
				echo form_default_row("", "", "<span style='font-size:12;'>Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>".br(2));
			}
			else
			{
				echo form_default_row("", "Indicador e período aberto", "Nenhum período aberto para criar a tabela do indicador.");
			}
			
			echo form_default_integer('ano_referencia', $label_0.':*', $row, "class='indicador_text'"); 
			echo form_default_integer("nr_participante", $label_1.':*', $row, "class='indicador_text'"); 
			echo form_default_integer("nr_satisfeito", $label_2.':*', $row, "class='indicador_text'"); 
			echo form_default_integer("nr_avaliacao", $label_3.':*', $row, "class='indicador_text'");
			echo form_default_textarea("observacao", $label_5.':', $row);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();

			if(intval($row['cd_atend_satisfacao_seminario_seguridade']) > 0)
			{
				echo button_save('Excluir', 'excluir()', 'botao_vermelho');
			}

		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>