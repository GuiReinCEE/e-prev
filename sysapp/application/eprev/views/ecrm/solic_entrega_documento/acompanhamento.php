<?php
	set_title('Solicitação Entrega Documento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_status', 'ds_descricao')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/solic_entrega_documento') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/solic_entrega_documento/cadastro/'.$row['cd_solic_entrega_documento']) ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "DateTimeBR",
            "CaseInsensitiveString",
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
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array(
        'Dt. Inclusão',
        'Usuário',
        'Status',
        'Descrição'
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array($item['ds_usuario_inclusao'], 'text-align:left'),
            '<span class="'.trim($item['ds_class_status']).'">'.$item['ds_status'].'</span>', 
            array(nl2br($item['ds_descricao']), 'text-align:justify')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Solicitação');
            echo form_default_row('', 'Tipo de Documento:', $row['ds_solic_entrega_documento_tipo']);
            echo form_default_row('', 'Data:', $row['data_ini']);
            echo form_default_row('', 'Horário Inicial:', $row['hr_ini']);
            echo form_default_row('', 'Horário Limite:', $row['hr_limite']);
            echo form_default_row('', 'Prioridade:', '<span class="'.trim($row['ds_class_prioridade']).'">'.$row['ds_prioridade'].'</span>');
            echo form_default_row('', 'Dt. Solicitação:', $row['dt_inclusao']);
			echo form_default_row('', 'Usuário:', $row['ds_usuario_inclusao']);

            if(trim($row['dt_recebido']) != '')
            {
                echo form_default_row('', 'Dt. Recebimento:', $row['dt_recebido']);
                echo form_default_row('', 'Usuário:', $row['ds_usuario_recebido']);
            }

		echo form_end_box('default_box');
        echo form_open('ecrm/solic_entrega_documento/salvar_acompanhamento');
            echo form_start_box('default_cadastro_box', 'Cadastro');
                echo form_default_hidden('cd_solic_entrega_documento', '', $row['cd_solic_entrega_documento']);
                echo form_default_hidden('cd_solic_entrega_documento_acompanhamento', '', $acompanhamento['cd_solic_entrega_documento_acompanhamento']);
                if($fl_usuario_adm AND intval($row['fl_finalizado']) == 0)
                {
                    echo form_default_dropdown('fl_status', 'Status: (*)', $status, $acompanhamento['fl_status']);
                }
                else
                {
                    echo form_default_hidden('fl_status', '', 'A');
                }
                
                echo form_default_textarea('ds_descricao', 'Descrição:', $acompanhamento['ds_descricao'], 'style="height:100px;"');    
            echo form_end_box('default_cadastro_box');
			echo form_command_bar_detail_start();
                echo button_save('Salvar');
		    echo form_command_bar_detail_end();
		echo form_close();
        echo $grid->render();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>