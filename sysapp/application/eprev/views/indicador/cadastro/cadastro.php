<?php 
set_title('Indicador de Desempenho');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("cd_indicador_grupo", "cd_processo", "ds_indicador", "cd_responsavel", "cd_substituto", "nr_ordem", "fl_igp", "fl_poder"));	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador/cadastro"); ?>';
	}

    function combo_gerencia()
    {
        if($('#cd_tipo').val() == 'A')
        {
			$('#cd_gerencia_row').show();
        }
        else
        {
			$('#cd_gerencia_row').hide();
        }
    }
	
	function excluir()
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("indicador/cadastro/excluir/".intval($row['cd_indicador'])); ?>';
		}
	}
	
	function ir_rotulos()
	{
		location.href='<?php echo site_url("indicador/cadastro/rotulos/".intval($row['cd_indicador'])); ?>';
	}
	
	$(function(){
		combo_gerencia();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

if( intval($row['cd_indicador']) > 0 )
{
	$abas[] = array('aba_detalhe', 'Rótulos', false, 'ir_rotulos();');
}

$arr_tipo[] = array("text" => "Área", "value" => "A");
$arr_tipo[] = array("text" => "Gestão", "value" => "G");

$arr_periodo[] = array("text" => 'Sim', "value" => 'S');
$arr_periodo[] = array("text" => 'Não', "value" => 'N');

echo aba_start( $abas );
	echo form_open('indicador/cadastro/salvar');
		echo form_start_box( "default_box", "Indicador de Desempenho" );
			echo form_hidden( 'cd_indicador', intval($row['cd_indicador']) );
			echo form_default_dropdown_db("cd_indicador_grupo", "Grupo :*", array( "indicador.indicador_grupo", "cd_indicador_grupo", "ds_indicador_grupo" ), array( $row["cd_indicador_grupo"] ), "", "", FALSE, " dt_exclusao IS NULL AND ds_indicador_grupo <> 'IGP' AND ds_indicador_grupo <> 'PODER'"); 
			echo form_default_dropdown("cd_tipo", "Tipo :*", $arr_tipo, array($row['cd_tipo']), "onchange='combo_gerencia()'");
			echo form_default_dropdown("fl_igp", "PE :*", $arr_periodo, array($row['fl_igp']));
			echo form_default_dropdown("fl_poder", "PODER :*", $arr_periodo, array($row['fl_poder']));
			#echo form_default_dropdown_db("cd_processo", "Processo :*", array( "projetos.processos", "cd_processo", "procedimento" ), array( $row["cd_processo"] ), "", "", FALSE, ""); 
			echo form_default_processo('cd_processo', 'Processo:*', $row['cd_processo']);
			echo form_default_text("ds_indicador", "Indicador :*", $row, "style='width:500px;'", "255");
			
			echo form_default_dropdown('cd_responsavel', 'Responsável :*', $ar_usuario_responsavel, $row['cd_responsavel']);			
			echo form_default_dropdown('cd_substituto', 'Substituto :*', $ar_usuario_substituto, $row['cd_substituto']);			
			
			echo form_default_text("plugin_nome", "Plugin Nome :", $row, "style='width:500px;'", "255");
			echo form_default_text("plugin_tabela", "Plugin Tabela :", $row, "style='width:500px;'", "255");
			echo form_default_text("tp_analise", "Tipo Analise :", $row, "style='width:300px;'", "255");
			echo form_default_dropdown("cd_gerencia", "Gerência :", $divisao, array(trim($row['cd_gerencia'])));
			echo form_default_dropdown("fl_periodo", "Período :", $arr_periodo, array($row['fl_periodo']));
			echo form_default_integer("qt_periodo_anterior", "Qt Período Anterior :*", $row, ""); 
			echo form_default_row('', '', '<i>-1 : Não tem período anterior; 0 : Todos os períodos anteriores; n : Quantidade determinada de períodos anteriores</i>');
			echo form_default_text("ds_dimensao_qualidade", "Dimensão da qualidade :", $row, "style='width:300px;'", "255"); 
			echo form_default_integer("nr_ordem", "Ordem de exibição :*", $row, ""); 
			echo form_default_dropdown_db( 'cd_indicador_controle', "Controle :", array('indicador.indicador_controle','cd_indicador_controle','ds_indicador_controle'), array($row['cd_indicador_controle']), "", "", true, $where=' dt_exclusao IS NULL ' );
			echo form_default_date("dt_limite_atualizar", "Dt Ref Limite Atualizar :*", $row, "");
			echo form_default_textarea("ds_formula", "Fórmula :", $row, "style='height:100px;'");
			echo form_default_textarea("ds_missao", "Missão :", $row, "style='height:100px;'");
			echo form_default_dropdown_db( 'cd_indicador_unidade_medida', "Unidade de medida :", array('indicador.indicador_unidade_medida','cd_indicador_unidade_medida','ds_indicador_unidade_medida'), array($row['cd_indicador_unidade_medida']), "", "", true, $where=' dt_exclusao IS NULL ' );
			echo form_default_text("ds_meta", "Meta :", $row, "style='width:300px;'", "0");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			if( intval($row['cd_indicador']) > 0  )
			{
				#if(intval($row['tl_indicador_tabela']) == 0)
				#{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				#}
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view('footer_interna');
?>