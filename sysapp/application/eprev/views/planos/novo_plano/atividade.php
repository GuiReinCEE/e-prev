<?php
    set_title('Novo Plano - Estrutura de Atividades');
    $this->load->view('header');
?>
<script>			
    function ir_lista()
    {
        location.href = "<?= site_url('planos/novo_plano/plano') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/novo_plano/plano_cadastro/'.$row['cd_novo_plano']) ?>";
    }

    function concluir_atividade(cd_novo_plano_atividade)
    {
        var confirmacao = 'Deseja concluir esta atividade?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('planos/novo_plano/concluir_atividade/'.$row['cd_novo_plano']) ?>" + "/" + cd_novo_plano_atividade;
        }
    }

    function ir_acompanhamento(cd_novo_plano_atividade)
    {
        location.href = "<?= site_url('planos/novo_plano/acompanhamento/'.$row['cd_novo_plano']) ?>" + "/" + cd_novo_plano_atividade;
    }

</script>
<?php   
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');  
    $abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');

    $head = array( 
        'Nº',
        'Atividade',
        'Dt. Início',
        'Acompanhamento',
        'Dt. Encerramento',
        'Usuário',
        ''
    );

    echo aba_start($abas);
        echo form_start_box('default_box', 'Plano');
            echo form_default_hidden('cd_novo_plano', '', $row['cd_novo_plano']);   
            echo form_default_row('ds_nome_plano', 'Nome:', $row['ds_nome_plano'], 'style="width:300px;"');
            echo form_default_row('dt_limite_aprovacao', 'Dt. Limite Aprovação Previc:', $row['dt_limite_aprovacao']);
            echo form_default_row('dt_inicio', 'Dt. Inicio Atividade:',$row['dt_inicio']);                
        echo form_end_box('default_box');
        echo br();

        foreach($collection as $item)
        {
            $body = array();

            foreach ($item['atividade'] as $key => $item2) 
            {
                if(trim($item2['dt_encerramento_prazo']) == '')
                {
                    $link = '<a href="javascript:void(0);" onclick="ir_acompanhamento('.$item2['cd_novo_plano_atividade'].')">[acompanhamento]</a>   
                             <a href="javascript:void(0);" onclick="concluir_atividade('.$item2['cd_novo_plano_atividade'].')">[concluir]</a>';
                }
                else
                {
                    $link = '<a href="javascript:void(0);" onclick="ir_acompanhamento('.$item2['cd_novo_plano_atividade'].')">[acompanhamento]</a>';
                }

                $body[] = array(
                    $item2['nr_ordem'],
                    array($item2['ds_novo_plano_atividade'], 'text-align:left'),
                    $item2['dt_inicio'],
                    array(nl2br($item2['ds_acompanhamento']), 'text-align:justify'),
                    $item2['dt_encerramento_prazo'],
                    array($item2['ds_usuario_encerramento'], 'text-align:left'),
                    $link
                );
            } 

            $this->load->helper('grid');
            $grid = new grid();
            $grid->head = $head;
            $grid->body = $body;
            $grid->view_count = false;

            echo form_start_box('default_box', $item['text']);
            echo $grid->render();
            echo form_end_box('default_box');
        }
    
    echo aba_end();

    $this->load->view('footer');
?>