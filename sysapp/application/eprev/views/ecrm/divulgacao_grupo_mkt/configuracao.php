<?php
    set_title('Email Divulgação - Grupo');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_divulgacao_grupo','tp_grupo')) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/cadastro/'.$row['cd_divulgacao_grupo']) ?>";
    }

    function atualiza_registro()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/atualiza_registro/'.intval($row['cd_divulgacao_grupo']).'/C') ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "CaseInsensitiveString",
            "RE",
            "CaseInsensitiveString",
            "CaseInsensitiveString"
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
        ob_resul.sort(0, true);
    }

    $(function(){
        configure_result_table()
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_configuracao', 'Configuração', TRUE, 'location.reload();');

    $head = array(
        'Nome',
        'RE',
        'Email',
        'Email Profissional'
    );

    $body = array();

    foreach ($collection as $key => $item)
    {
        $body[] = array(
            array($item['nome'], 'text-align:left;'),
            $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
            $item['email'],
            $item['email_profissional']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_open('ecrm/divulgacao_grupo_mkt/salvar_configuracao');
            echo form_start_box('default_box', 'Cadastro');
            	echo form_default_hidden('cd_divulgacao_grupo', '', $row['cd_divulgacao_grupo']);
            	echo form_default_row('', 'Descrição:', $row['ds_divulgacao_grupo']);
                echo form_default_row('', 'Qt Total Registro(s):', '<span class="label label-success">'.$row['qt_registro'].'</span>');
            	echo form_default_row('', '', '');
              	echo form_default_checkbox_group('cd_empresa', 'Empresa:', $empresa, $row['cd_empresa'], 308);
				echo form_default_checkbox_group('cd_plano', 'Plano:', $plano, $row['cd_plano'], 155);
				echo form_default_checkbox_group('ds_tipo', 'Tipo:', $tipo, $row['ds_tipo'], 87);
				echo form_default_textarea('ds_cidade', 'Cidade:', $row['ds_cidade'],'style="height:80px;width:504px;"');
				echo form_default_row('', '', '<span style="color:red">Informar os nomes das cidades separados por ponto e vírgula (;)</span>');
			echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');

                if(intval($row['cd_divulgacao_grupo']) > 0)
                {
                    echo button_save('Atualizar Total Registro(s)', 'atualiza_registro();', 'botao_amarelo');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>