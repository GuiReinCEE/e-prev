<?php
	set_title('Novo Plano - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_nome_plano', 'dt_limite_aprovacao')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('planos/novo_plano/plano') ?>";
	}

	function ir_atividade()
	{
		location.href = "<?= site_url('planos/novo_plano/atividade/'.$row['cd_novo_plano']) ?>";
	}

	function iniciar()
	{
		var confirmacao = 'Deseja iniciar as atividades?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('planos/novo_plano/iniciar_atividade/'.$row['cd_novo_plano']) ?>";
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    if(trim($row['dt_inicio']) != '')
	{
		$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
	}
	
	echo aba_start($abas);
		echo form_open('planos/novo_plano/plano_salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_novo_plano', '', $row['cd_novo_plano']);	
				echo form_default_text('ds_nome_plano', 'Nome: (*)', $row['ds_nome_plano'], 'style="width:300px;"');
				echo form_default_date('dt_limite_aprovacao', 'Dt. Limite Aprovação: (*)', $row['dt_limite_aprovacao']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
                if(trim($row['dt_inicio']) != '')
                {
                    echo form_default_row('dt_inicio', 'Dt. Inicio Atividade:',$row['dt_inicio']);
                }

            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                if(trim($row['dt_inicio']) == '' )
                {
                    echo button_save('Salvar');	

                    if(intval($row['cd_novo_plano']) > 0)
                    {
                        echo button_save('Iniciar Atividade', 'iniciar();', 'botao_verde');	
                    }
                }                
            echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>