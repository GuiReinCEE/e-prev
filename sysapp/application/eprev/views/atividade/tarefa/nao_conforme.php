<?php
set_title('Tarefa - Execução');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array('ds_obs'));
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/tarefa"); ?>';
    }
	
	function ir_atividade()
	{
		location.href='<?php echo site_url("atividade/atividade_atendimento/index/".$row['cd_atividade']); ?>';
	}
	
	function ir_execucao()
	{
		location.href='<?php echo site_url("atividade/tarefa_execucao/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
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
	
	function cancelar()
	{
		location.href='<?php echo site_url("atividade/tarefa/cadastro/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Atividade', FALSE, 'ir_atividade()');
$abas[] = array('aba_lista', 'Definição', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Execução', FALSE, 'ir_execucao();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

if(trim($row['fl_checklist']) == 'S')
{
	$abas[] = array('aba_lista', 'Checklist', FALSE, 'ir_checklist();');
}

echo aba_start($abas);
	echo form_open('atividade/tarefa/salvar_nao_conforme', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Motivo" );
			echo form_default_hidden('cd_atividade', '', $row['cd_atividade']);
			echo form_default_hidden('cd_tarefa', '', $row['cd_tarefa']);
			echo form_default_hidden('cd_recurso', '', $row['cd_recurso']);
			echo form_default_textarea('ds_obs', "Informe o motivo:* ", '', "style='width:500px; height:100px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
			echo button_save("Cancelar", 'cancelar()', 'botao_disabled');
        echo form_command_bar_detail_end();
	echo form_close();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>