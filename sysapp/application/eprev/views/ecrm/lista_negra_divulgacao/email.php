<?php
    set_title('Divulgação - Lista Negra');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_lista_negra_divulgacao_email')) ?>

    function excluir_email(cd_lista_negra_divulgacao_email)
    {
        var confirmacao = 'Deseja excluir o Item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para NÃ£o\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('ecrm/lista_negra_divulgacao/excluir_email/'.$grupo['cd_lista_negra_divulgacao']) ?>/'+cd_lista_negra_divulgacao_email;
        }
    }
   
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/lista_negra_divulgacao') ?>";        
    }    

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/lista_negra_divulgacao/cadastro/'.$grupo['cd_lista_negra_divulgacao']) ?>";        
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "CaseInsensitiveString",            
            "CaseInsensitiveString",
            "DateTimeBR",
            null        
        ]);
        ob_resul.onsort = function ()
        {
            var rows = ob_resul.tBody.rows;
            var l = rows.length;
            for (var i = 0; i < l; i++)
            {
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul.sort(2, true);
    }

    $(function(){
        configure_result_table();
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_email', 'Lista de E-mail', TRUE, 'location.reload();');

    $head = array( 
        'E-mail',
        'Usuário',
        'Dt. Inclusão',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array(anchor('ecrm/lista_negra_divulgacao/email/'.$item['cd_lista_negra_divulgacao'].'/'.$item['cd_lista_negra_divulgacao_email'], $item['ds_lista_negra_divulgacao_email']), 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left;'),
            $item['dt_inclusao'],
            '<a href="javascript:void(0);" onclick="excluir_email('.$item['cd_lista_negra_divulgacao_email'].' )">[excluir]</a>'
        );        
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_open('ecrm/lista_negra_divulgacao/salvar_email');
            echo form_start_box('default_box', 'Grupo');   
                echo form_default_hidden('cd_lista_negra_divulgacao', '', $grupo['cd_lista_negra_divulgacao']);      
                echo form_default_row('ds_lista_negra_divulgacao', 'Descrição:', $grupo['ds_lista_negra_divulgacao'], 'style="width:350px;"');
            echo form_end_box('default_box');
            echo form_start_box('default_box', 'Cadastro E-mail');         
                echo form_default_text('ds_lista_negra_divulgacao_email', 'E-mail: (*)', $row['ds_lista_negra_divulgacao_email'], 'style="width:350px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar');
            echo form_command_bar_detail_end();            
        echo form_close();
        echo $grid->render();
    echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>