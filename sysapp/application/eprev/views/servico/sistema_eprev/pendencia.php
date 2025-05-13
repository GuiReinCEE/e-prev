<?php
    set_title('Sistema e-prev - Pendências');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_ordem')) ?>

    function ir_lista()
    {
        location.href = "<?= site_url("servico/sistema_eprev/index") ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url("servico/sistema_eprev/cadastro/".intval($row["cd_sistema"])) ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url("servico/sistema_eprev/acompanhamento/".intval($row["cd_sistema"]))?>";
    }

    function ir_atividade()
    {
        location.href = "<?= site_url("servico/sistema_eprev/atividade/".intval($row["cd_sistema"])) ?>";
    }
    
    function ir_anexo()
    {
        location.href = "<?= site_url("servico/sistema_eprev/anexo/".$row["cd_sistema"]) ?>";
    }

    function ir_rotina()
    {
        location.href = "<?= site_url("servico/sistema_eprev/rotina/".intval($row["cd_sistema"])) ?>";
    }

    function ir_metodo()
    {
        location.href = "<?= site_url("servico/sistema_eprev/metodo/".intval($row["cd_sistema"])) ?>";
    }

    function gerar_pdf()
    {
        window.open("<?=site_url("servico/sistema_eprev/pdf/".intval($row["cd_sistema"])) ?>");
    }

    function excluir(cd_sistema_pendencia)
    {
        var confirmacao = 'Deseja excluir o pendencia?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('servico/sistema_eprev/pendencia_excluir/'.$sistema['cd_sistema']) ?>/" + cd_sistema_pendencia;
        }
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "Number",
            "CaseInsensitiveString",
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
        ob_resul.sort(0, false);
    }

    function novo()
    {
        location.href = "<?= site_url('servico/sistema_eprev/cadastro') ?>";
    }

    $(function(){
        configure_result_table();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_metodo', 'Método', FALSE, 'ir_metodo();');
    $abas[] = array('aba_rotina', 'Rotina', FALSE, 'ir_rotina();');
    $abas[] = array('aba_pendencia', 'Pendências', TRUE, 'location.reload();');
    $abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');    
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();'); 
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();'); 

    $head = array(
        'Ordem',
        'Pendência',
        ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            array(anchor('servico/sistema_eprev/pendencia/'.$item['cd_sistema'],$item['nr_ordem']), 'text-align:left;'),            
            array(anchor('servico/sistema_eprev/pendencia/'.$item['cd_sistema'], $item['cd_pendencia_minha'].' - '.$item['ds_pendencia_minha']), 'text-align:left;'),
            '<a href="javascript:void(0)" onclick="excluir('.$item['cd_sistema_pendencia'].')">[excluir]</a>'  
        );
    }    

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    $grid->view_count = false;

    echo aba_start($abas);
     echo form_open('servico/sistema_eprev/pendencia_salvar');
         echo form_start_box('default_sistema_box', 'Sistema');
                 echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
                 echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
                 echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);     
                 echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);            
             echo form_end_box('default_sistema_box');

            echo form_start_box('default_sistema_box', ' Cadastro');
                echo form_default_hidden('cd_pendencia_minha_query', '', $row['cd_pendencia_minha_query']);
                echo form_default_integer('nr_ordem', 'Ordem: (*)', $row['nr_ordem']);                
                echo form_default_dropdown('cd_pendencia_minha_query', 'Pendêcia: (*)', $pendencia_minha, $row['cd_pendencia_minha_query']);
            echo form_end_box('default_box');   
            echo form_command_bar_detail_start();
                echo button_save('Salvar');                         
            echo form_command_bar_detail_end();

        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>