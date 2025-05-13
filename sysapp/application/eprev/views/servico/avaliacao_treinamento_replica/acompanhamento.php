<?php
	set_title('Treinamento Colaborador - Replica');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_acompanhamento')) ?>

    function ir_lista()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento_replica/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento_replica/cadastro/'.$cd_treinamento_colaborador_item) ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento_replica/acompanhamento/'.$cd_treinamento_colaborador_item.'/'.$cd_treinamento_colaborador_item_replica) ?>";
    }

    function excluir(cd_treinamento_colaborador_item_replica_acompanhamento)
    {
        confirmacao = 'Deseja excluir este item?\n\n'+
                      'Clique [Ok] para Sim\n\n'+
                      'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('servico/avaliacao_treinamento_replica/excluir/'.$cd_treinamento_colaborador_item.'/'.$cd_treinamento_colaborador_item_replica) ?>/" + cd_treinamento_colaborador_item_replica_acompanhamento;
        }
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array( 
        'Acompanhamento',
        'Dt. Inclusão',
        'Usuário'
    );

    $body = array();
    
    foreach($collection as $item)
	{
        $body[] = array(
            array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
            $item['dt_inclusao'],
            $item['ds_usuario_inclusao']
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
    $grid->body = $body;

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
            echo form_default_row('', 'Dt. Finalizado:', $row['dt_finalizado']);
            echo form_default_row('', 'Usuário:', $row['ds_usuario']);
            echo form_end_box('default_box');  
         
            echo form_open('servico/avaliacao_treinamento_replica/salvar_acompanhamento');  
                echo form_start_box('default_box', 'Cadastro');
                    echo form_default_hidden('cd_treinamento_colaborador_item_replica', '', $cd_treinamento_colaborador_item_replica);
                    echo form_default_hidden('cd_treinamento_colaborador_item_replica_acompanhamento', '', $row_acompanhamento);
                    echo form_default_hidden('cd_treinamento_colaborador_item', '', $cd_treinamento_colaborador_item);
                    echo form_default_textarea('ds_acompanhamento', 'Acompanhamento: (*)', $row_acompanhamento);
                echo form_end_box('default_box'); 
                echo form_command_bar_detail_start();
                    echo button_save('Salvar');
                    if(intval($row_acompanhamento['cd_treinamento_colaborador_item_replica_acompanhamento']) != 0)
                    {
                        echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
                    }
                echo form_command_bar_detail_end();  
            echo form_close(); 
            
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer');
    
?>
