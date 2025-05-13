<?php
	set_title('Integração de Documento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('mes_referencia', 'ano_referencia'), 'valida_form(form)') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/documento_integra/minhas') ?>";
    }

    function valida_form(form)
    {
        if(confirm("Salvar?"))
        {
            form.submit();
        }
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'DateTimeBR', 
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
        ob_resul.sort(1, true);
    }

    function excluir_documento(cd_documento_integra_anexo)
    {
        var confirmacao = 'Deseja excluir o documento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/documento_integra/excluir_documento/'.$row['cd_documento_integra']) ?>/"+cd_documento_integra_anexo;
        }
    }

    function enviar()
    {
        var confirmacao = 'Deseja enviar os documentos e encerrar?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/documento_integra/enviar_documentacao/'.$row['cd_documento_integra']) ?>";
        }
    }

    $(function(){
        configure_result_table();
    });

</script>
<?php
    $head = array(
        'Documento',
        'Dt. Inclusão',
        'Usuário',
        ''
    );

    $body = array();

    foreach ($anexar_documento as $key => $item)
    {
        $body[] = array(
            array(anchor(base_url().'up/documento_integra/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), "text-align:left;"),
            $item['dt_inclusao'],
            $item['ds_usuario'],
            (trim($row['dt_envio']) == '' ? '<a href="javascript:void(0);" onclick="excluir_documento('.$item['cd_documento_integra_anexo'].')">[excluir]</a>' : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	$abas[] = array('aba_lista', 'Minhas', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('gestao/documento_integra/minhas_salvar');
			echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_documento_integra', '', $row['cd_documento_integra']);
				echo form_default_hidden('cd_documento_integra_doc_tipo', '', $row['cd_documento_integra_doc_tipo']);
                echo form_default_row('', 'Tipo Doc:', $row['ds_documento_integra_doc_tipo']);
                
                if(trim($row['tp_periodicidade']) == 'M')
                {
                    if(intval($row['cd_documento_integra']) == 0)
                    {
                        echo form_default_mes_ano('mes_referencia', 'ano_referencia','Mês/Ano: (*)', $row['dt_referencia']);
                    }
                    else
                    {
                        echo form_default_hidden('mes_referencia', '', $row['nr_mes']);
                        echo form_default_hidden('ano_referencia', '', $row['nr_ano']);

                        echo form_default_row('', 'Mês/Ano:', strtoupper(mes_extenso($row['nr_mes'])).'/'.$row['nr_ano']);
                    }

                     echo form_default_hidden('ds_referencia', '', '');
                }
                else if(trim($row['tp_periodicidade']) == 'E')
                {
                    echo form_default_hidden('mes_referencia', '', $row['nr_mes']);
                    echo form_default_hidden('ano_referencia', '', $row['nr_ano']);

                    echo form_default_text('ds_referencia', 'Nome Sub Pasta: (*)', $row['ds_referencia'], 'style="width:500px;"');
                }
                
                if(trim($row['dt_envio']) == '')
                {
                    echo form_default_upload_multiplo('arquivo_m', 'Documentos: (*)', 'documento_integra');
                }
                else
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio']);
                    echo form_default_row('', 'Usuário:', $row['ds_usuario_envio']);
                }
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
                if(trim($row['dt_envio']) == '')
                {
    				echo button_save('Salvar');

                    if(count($anexar_documento) > 0)
                    {
                        echo button_save('Enviar Documentos', 'enviar()', 'botao_verde');
                    }
                }
		    echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
        
        if(intval($row['cd_documento_integra']) > 0)
        {
            echo $grid->render();
        }
    echo aba_end();

    $this->load->view('footer_interna');
?>