<?php
	set_title('Gerência - Unidade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_gerencia_unidade', 'ds_descricao', 'dt_vigencia_ini')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/gerencia_unidade') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('servico/gerencia_unidade/unidade/'.$gerencia['codigo']) ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById('table-1'),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateBr',
            'CaseInsensitiveString'
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

    $(function(){
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_unidade', 'Unidade', TRUE, 'location.reload();');

    $head = array(
        'Código',
        'Descrição',
        'Usuários',
        'Vigência',
        'E-mail'
    );

    $body = array();

    foreach ($collection as $item)
    {   
        $body[] = array(
            anchor('servico/gerencia_unidade/unidade/'.$gerencia['codigo'].'/'.$item['cd_gerencia_unidade'], $item['cd_gerencia_unidade']),
            array(anchor('servico/gerencia_unidade/unidade/'.$gerencia['codigo'].'/'.$item['cd_gerencia_unidade'], $item['ds_descricao']), 'text-align:left;'),
            array(implode(br(), $item['usuario']), 'text-align:left;'),
            $item['dt_vigencia_ini'],
            array($item['ds_email'], 'text-align:left;')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    $grid->view_count = false;

    echo aba_start($abas);
        echo form_open('servico/gerencia_unidade/salvar_unidade');
            echo form_start_box('default_gerencia_box', 'Gerência'); 
                echo form_default_row('', 'Código:', '<label class="label label-inverse">'.$gerencia['codigo'].'</label>');
                echo form_default_row('', 'Gerência:', $gerencia['codigo'].' - '.$gerencia['nome']);
            echo form_end_box('default_gerencia_box');

            echo form_start_box('default_box', 'Cadastro');		
                echo form_default_hidden('codigo', '', $gerencia['codigo']);   
                echo form_default_hidden('cd_gerencia_unidade_h', '', $row['cd_gerencia_unidade']);   

                if(trim($row['cd_gerencia_unidade']) == '')
                {
                    echo form_default_text('cd_gerencia_unidade', 'Código: (*)', $row);
                }
                else
                {
                    echo form_default_row('', 'Código:', '<label class="label label-inverse">'.$row['cd_gerencia_unidade'].'</label>');
                }

                echo form_default_text('ds_descricao', 'Descrição: (*)', $row, 'style="width:300px;"');
                echo form_default_date('dt_vigencia_ini', 'Vigência: (*)', $row);
                echo form_default_text('ds_email', 'E-mail: ', $row['ds_email'], 'style="width:300px;"');
                
                if(trim($row['cd_gerencia_unidade']) != '')
                {
                    echo form_default_date('dt_vigencia_fim', 'Vigência Fim: ', $row);
                }

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