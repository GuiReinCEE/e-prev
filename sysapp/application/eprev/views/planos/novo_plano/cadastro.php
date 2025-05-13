<?php
	set_title('Novo Plano - Cadastro de Atividade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
        'cd_novo_plano_subprocesso',
        'nr_ordem'
	)) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('planos/novo_plano') ?>";
	}

    function set_ordem_subprocesso(cd_novo_plano_subprocesso)
    {
        var cd_novo_plano_subprocesso = $("#cd_novo_plano_subprocesso").val();

        if(cd_novo_plano_subprocesso != '')
        {
            $.post("<?= site_url('planos/novo_plano/set_ordem_subprocesso') ?>",
        {
            cd_novo_plano_subprocesso : cd_novo_plano_subprocesso
        },
        function(data)
        {
            $("#nr_ordem").val(data.nr_ordem);

            $("#ds_novo_plano_estrutura").focus();
        }, 'json'); 
        }
        else
        {
            alert("Informe o Subprocesso");
        }
    }

	function desativar()
	{
		var confirmacao = 'Deseja Desativar a Atividade?\n\n'+
	        'Clique [Ok] para Sim\n\n'+
	        'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('planos/novo_plano/desativar/'.$row['cd_novo_plano_estrutura']) ?>';
        }
	}

	function ativar()
	{
		var confirmacao = 'Deseja Ativar a Atividade?\n\n'+
	        'Clique [Ok] para Sim\n\n'+
	        'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('planos/novo_plano/ativar/'.$row['cd_novo_plano_estrutura']) ?>';
        }
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('planos/novo_plano/salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_novo_plano_estrutura', '', $row['cd_novo_plano_estrutura']);	
                echo form_default_dropdown('cd_novo_plano_subprocesso', 'Subprocesso: (*)', $subprocesso, $row['cd_novo_plano_subprocesso'], 'onchange="set_ordem_subprocesso($(this).val())"');
				echo form_default_integer('nr_ordem', 'Nº Ordem: (*)', $row['nr_ordem']);
				echo form_default_textarea('ds_novo_plano_estrutura', 'Descrição:', $row, 'style="height:100px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
            if(trim($row['dt_encerramento']) == '')
            {
                echo button_save('Salvar');	
            }
            
            if(intval($row['cd_novo_plano_estrutura']) > 0)
            {
                if(trim($row['dt_encerramento']) == '')
                {
                    echo button_save('Desativar', 'desativar();', 'botao_vermelho');	
                }
                else
                {
                    echo button_save('Ativar', 'ativar();', 'botao_verde');	
                }
            }	
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>