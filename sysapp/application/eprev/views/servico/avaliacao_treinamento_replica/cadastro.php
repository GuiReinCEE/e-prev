<?php
	set_title('Treinamento Colaborador - Replica');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('fl_aplica_replica'), 'valida_input(form)') ?>

    function ir_lista()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento_replica/index') ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento_replica/acompanhamento/'.$cd_treinamento_colaborador_item.'/'.$row['cd_treinamento_colaborador_item_replica']) ?>";
    }

    function set_fl_aplica_replica(fl_aplica_replica)
    {
        if(fl_aplica_replica == 'S')
        {
            $("#dt_limite_row").show();
            $("#ds_justificativa_row").hide();
            $("#ds_justificativa").val("");
        }
        else if(fl_aplica_replica == 'N')
        {
            $("#ds_justificativa_row").show();
            $("#dt_limite_row").hide();
            $("#dt_limite").val("");
        }
        else
        {
            $("#ds_justificativa_row").hide();
            $("#dt_limite_row").hide();
        }

    }

    function valida_input(form)
    {
        var fl_submit = true;

        if($("#fl_aplica_replica").val() == "S" && $("#dt_limite").val() == "")
        {
            alert("Informe a Data!");

            fl_submit = false;
        }
        else if($("#fl_aplica_replica").val() == "N" && $("#ds_justificativa").val() == "")
        {
            alert("Informe a Justificativa!");

            fl_submit = false;
        }

        if(fl_submit)
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }

    function finalizar()
    {
        var confirmacao = 'Deseja finalizar este item?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/avaliacao_treinamento_replica/finalizar/'.$row['cd_treinamento_colaborador_item_replica']) ?>";
		}
    }

    $(function(){
        set_fl_aplica_replica("<?= $row['fl_aplica_replica'] ?>");
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

    $fl_aplica_replica = array(
        array('value' => 'S', 'text' => 'Sim'),
        array('value' => 'N', 'text' => 'Não')
    );

    echo aba_start($abas);
        echo form_start_box('default_box', 'Treinamento'); 
            echo form_default_row('', 'Ano/Numero:', $row['numero']);
            echo form_default_row('', 'Nome:', $row['nome']);
            echo form_default_row('', 'Promotor:', $row['promotor']);
            echo form_default_row('', 'Cidade:', $row['cidade']);
            echo form_default_row('', 'UF:', $row['uf']);
            echo form_default_row('', 'Dt. Inicio:', $row['dt_inicio']);
            echo form_default_row('', 'Dt. Final:', $row['dt_final']);
            echo form_default_row('', 'Tipo:', $row['ds_treinamento_colaborador_tipo']);
            if(trim($row['dt_concluido']) != '')
            {
                echo form_default_row('', 'Dt. Finalizado:', $row['dt_finalizado']);
                echo form_default_row('', 'Usuário:', $row['ds_usuario']);
            }
        echo form_end_box('default_box');  
        echo form_open('servico/avaliacao_treinamento_replica/salvar');  
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_treinamento_colaborador_item', '', $cd_treinamento_colaborador_item);
                echo form_default_hidden('cd_treinamento_colaborador_item_replica', '', $row);
                if(trim($row['dt_concluido']) == '')
                {
                    echo form_default_dropdown('fl_aplica_replica', 'Este treinamento pode ser replicado?', $fl_aplica_replica, $row['fl_aplica_replica'], 'onchange="set_fl_aplica_replica($(this).val())"'); 
                    echo form_default_textarea('ds_justificativa', 'Justifique:', $row);
                    echo form_default_date('dt_limite', 'Quando?', $row);
                }	
                else
                {
                    echo form_default_row('', 'Este treinamento pode ser replicado?', $row['fl_aplica']);
                    
                    if(trim($row['ds_justificativa']) != '')
                    {
                        echo form_default_textarea('', 'Justifique:', $row['ds_justificativa']);
                    }
                    else
                    {
                        echo form_default_row('', 'Quando?', $row['dt_limite']);
                    }
                }
            echo form_end_box('default_box'); 
            echo form_command_bar_detail_start();
                if(trim($row['dt_concluido']) == '')
                {
                    echo button_save('Salvar');

                    echo button_save('Finalizar', 'finalizar()', 'botao_vermelho');
                }
            echo form_command_bar_detail_end();
        echo form_close(); 
        echo br(2);
    echo aba_end();

	$this->load->view('footer');
?>