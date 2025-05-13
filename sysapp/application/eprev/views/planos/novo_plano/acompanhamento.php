<?php
    set_title('Novo Plano - Acompanhamento de Atividades');
    $this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_acompanhamento')) ?>
					
    function ir_lista()
    {
        location.href = "<?= site_url('planos/novo_plano/plano') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/novo_plano/plano_cadastro/'.$row['cd_novo_plano']) ?>";
    }

    function ir_atividade()
    {
        location.href = "<?= site_url('planos/novo_plano/atividade/'.$row['cd_novo_plano']) ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('planos/novo_plano/acompanhamento/'.$row['cd_novo_plano'].'/'.$atividade['cd_novo_plano_atividade']) ?>";
    }
</script>
<?php   
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');  
    $abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array( 
        'Descrição',
        'Dt. Inclusao',
        'Usuário'
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array(nl2br($item['ds_acompanhamento']), 'text-align:justify'),
            $item['dt_inclusao'],
            $item['ds_usuario']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;


    echo aba_start($abas);
        echo form_start_box('default_box', 'Plano');
            echo form_default_hidden('cd_novo_plano', '', $row['cd_novo_plano']);   
            echo form_default_row('ds_nome_plano', 'Nome:', $row['ds_nome_plano'], 'style="width:300px;"');
            echo form_default_row('dt_limite_aprovacao', 'Dt. Limite Aprovação Previc:', $row['dt_limite_aprovacao']);
            echo form_default_row('dt_inicio', 'Dt. Inicio Atividade:',$row['dt_inicio']);                
        echo form_end_box('default_box');
        echo form_start_box('default_box', 'Atividade');  
            echo form_default_row('', 'Subprocesso:', $atividade['ds_novo_plano_subprocesso']);
            echo form_default_row('', 'Nome:', $atividade['ds_novo_plano_atividade']);    
        echo form_end_box('default_box');
        if(trim($atividade['dt_encerramento']) == '')
        {
            echo form_open('planos/novo_plano/salvar_acompanhamento');
                echo form_start_box('default_box', 'Acompanhamento'); 
                    echo form_default_hidden('cd_novo_plano', '', $row['cd_novo_plano']);
                    echo form_default_hidden('cd_novo_plano_atividade', '', $atividade['cd_novo_plano_atividade']);
                    echo form_default_hidden('cd_novo_plano_atividade_acompanhamento', '', $acompanhamento);
                    echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', $acompanhamento); 
                echo form_end_box('default_box');
                echo form_command_bar_detail_start();
                    echo button_save('Salvar');      
                    if($acompanhamento['cd_novo_plano_atividade_acompanhamento'] != '')
                    {
                        echo button_save('Cancelar', 'cancelar()', 'botao_disabled');  
                    }      
                echo form_command_bar_detail_end();
            echo form_close();   
        }
        echo br();
    echo $grid->render();
    echo aba_end();

    $this->load->view('footer');
?>