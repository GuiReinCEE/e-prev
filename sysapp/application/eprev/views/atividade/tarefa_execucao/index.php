<?php
set_title('Tarefa - Execução');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array('cd_classificacao'));
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/tarefa"); ?>';
    }
	
	function ir_atividade()
	{
		location.href='<?php echo site_url("atividade/atividade_atendimento/index/".$row['cd_atividade']); ?>';
	}
	
	function ir_definicao()
	{
		location.href='<?php echo site_url("atividade/tarefa/cadastro/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("atividade/tarefa_anexo/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_checklist()
	{
		location.href='<?php echo site_url("atividade/tarefa_checklist/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("atividade/tarefa_historico/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function play()
	{
		if($('#cd_classificacao_s').val() != '')
		{
			location.href='<?php echo site_url("atividade/tarefa_execucao/play/".$row['cd_atividade']."/".$row['cd_tarefa']."/".$row["cd_recurso"]); ?>';
		}
		else
		{
			alert('Informe a Classificação da Tarefa e click no botão salvar.')
		}
	}
	
	function pause()
	{
		location.href='<?php echo site_url("atividade/tarefa_execucao/confirmacao_pause/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function stop()
	{
		if($('#fl_checklist').val() == 'S' && $('#fl_resposta_checklist').val() == 'N')
		{
			alert('Preencha antes o checklist!');
			ir_checklist();
		}
		else
		{
			location.href='<?php echo site_url("atividade/tarefa_execucao/confirmacao_stop/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
		}
	}
	
	function imprimir()
	{
		location.href='<?php echo site_url("atividade/tarefa/imprimir/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Atividade', FALSE, 'ir_atividade()');
$abas[] = array('aba_lista', 'Definição', FALSE, 'ir_definicao();');
$abas[] = array('aba_lista', 'Execução', TRUE, 'location.reload();');
if(trim($row['fl_checklist']) == 'S')
{
	$abas[] = array('aba_lista', 'Checklist', FALSE, 'ir_checklist();');
}
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

if(trim($row['fl_status']) == "AMAN") 
{
	$fl_play   = true;
	$fl_pause  = false;
	$fl_stop   = false;
	$fl_save   = true;
}	
else if(trim($row['fl_status']) == "EMAN") 
{
	$fl_play  = false;
	$fl_pause = true;
	$fl_stop  = true;	
    $fl_save  = true;	
}
else if(trim($row['fl_status'])== "SUSP") 
{
	$fl_play  = true;
	$fl_pause = false;
	$fl_stop  = true;
	$fl_save  = true;
}
else if(trim($row['fl_status']) == "LIBE" OR trim($row['fl_status']) == "CONC") 
{
	$fl_play  = false;
	$fl_pause = false;
	$fl_stop  = false;
	$fl_save  = false;
}
else
{
	$fl_play  = true;
	$fl_pause = false;
	$fl_stop  = false;	
	$fl_save  = true;
}

echo aba_start($abas);
	echo form_open('atividade/tarefa_execucao/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atividade', '', $row['cd_atividade']);
			echo form_default_hidden('cd_tarefa', '', $row['cd_tarefa']);
			echo form_default_hidden('cd_classificacao_s', '', $row['cd_classificacao']);
			echo form_default_hidden('fl_resposta_checklist', '', $row['fl_resposta_checklist']);
			echo form_default_hidden('fl_checklist', '', $row['fl_checklist']);
			echo form_default_text("atividade_os", "Atividade/Tarefa:", $row['cd_atividade'].' / '.$row['cd_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("status", "Status:", $row['status_atual'], 'style="width: 500px; border: 0px; font-weight:bold; color:'.trim($row['status_cor']).'" readonly');
			if(intval($row['tl_anexos']) > 0)
			{
				echo form_default_row('','','<i>Esta tarefa possui anexo(s)</i>');
			}
			echo form_default_text("dt_inicio_prog", "Data de Início da tarefa:", $row['dt_inicio_prog'], 'style="width: 500px; border: 0px; color:gray; font-weight:bold;" readonly');
			echo form_default_text("dt_fim_prog", "Data de fim da tarefa:", $row['dt_fim_prog'], 'style="width: 500px; border: 0px; color:green; font-weight:bold;" readonly');
			echo form_default_text("dt_ok_anal", "Data de Acordo:", $row['dt_ok_anal'], 'style="width: 500px; border: 0px; font-weight: bold; color:blue;" readonly');
			echo form_default_text("nome_tarefa", "Tipo da tarefa:", $row['nome_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("analista", "Analista:", $row['analista'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("programador", "Programador:", $row['programador'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("programa", "Nome do programa:", $row['programa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_dropdown('cd_classificacao', 'Classificação da Tarefa:', $arr_classificacao, array($row['cd_classificacao']));
			echo form_default_text("dt_inicio_prev", "Data de Início Prevista:", $row['dt_inicio_prev'], 'style="width: 500px; border: 0px; font-weight: bold;" readonly');
			echo form_default_text("dt_fim_prev", "Data de Término Prevista:", $row['dt_fim_prev'], 'style="width: 500px; border: 0px; font-weight: bold;" readonly');
			echo form_default_textarea('observacoes', "Considerações gerais e complementos: ", $row['observacoes'], "style='width:500px; height:100px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();  
			if($this->session->userdata('codigo') == intval($row['cd_recurso']))
			{
				echo ($fl_save ? button_save("Salvar") : '');
				echo ($fl_play ? button_save("Iniciar", 'play()', 'botao_verde') : '');
				echo ($fl_pause ? button_save("Pausar", 'pause()', 'botao_amarelo') : '');
				echo ($fl_stop ? button_save("Concluir", 'stop()', 'botao_vermelho') : '');
			}
			echo button_save("Imprimir", 'imprimir()', 'botao_disabled');
        echo form_command_bar_detail_end();
	echo form_close();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>