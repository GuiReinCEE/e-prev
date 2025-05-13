<?php
    set_title('Demonstrativo de Resultados');
    $this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_demonstrativo_resultado_estrutura_mes'), 'valida_arquivo(form)') ?>

    function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if(confirm('Salvar?'))
            {
                form.submit();
            }
        }
    }

    function excluir_anexo(cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        var confirmacao = 'Deseja excluir o anexo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_resultado/excluir_anexo/'.$demonstrativo['cd_demonstrativo_resultado'].'/'.$demonstrativo_mes['cd_demonstrativo_resultado_mes']) ?>/"+$("#cd_demonstrativo_resultado_estrutura_mes").val()+"/"+cd_demonstrativo_resultado_estrutura_mes_anexo;
        }
    }
    
    function validaArq(enviado, nao_enviado, arquivo)
    {
        $("form").submit();
    }   

    function editar_ordem(cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        $("#nr_ordem_valor_"+cd_demonstrativo_resultado_estrutura_mes_anexo).hide(); 
        $("#editar_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).hide(); 

        $("#salvar_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).show(); 
        $("#nr_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).show(); 
        $("#nr_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).focus();   
    }

    function set_ordem(cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        $("#ajax_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).html("<?= loader_html("P") ?>");

        $.post("<?= site_url('gestao/demonstrativo_resultado/alterar_ordem') ?>/"+cd_demonstrativo_resultado_estrutura_mes_anexo,
        {
            nr_ordem : $("#nr_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).val() 
        },
        function(data)
        {
            $("#ajax_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).empty();
            
            $("#nr_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).hide();
            $("#salvar_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).hide(); 
            
            $("#nr_ordem_valor_"+cd_demonstrativo_resultado_estrutura_mes_anexo).html($("#nr_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).val()); 
            $("#nr_ordem_valor_"+cd_demonstrativo_resultado_estrutura_mes_anexo).show(); 
            $("#editar_ordem_"+cd_demonstrativo_resultado_estrutura_mes_anexo).show(); 
        });
    }

    function fechar_estrutura_mes(cd_demonstrativo_resultado_estrutura_mes_anexo)
    {
        var confirmacao = 'Deseja fechar o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_resultado/fechar_estrutura_mes/'.$demonstrativo['cd_demonstrativo_resultado'].'/'.$demonstrativo_mes['cd_demonstrativo_resultado_mes']) ?>/"+$("#cd_demonstrativo_resultado_estrutura_mes").val();
        }
    }

    function ir_minhas()
	{
		location.href = "<?= site_url('gestao/demonstrativo_resultado/minhas') ?>";
	}

    function ir_estrutura(cd_demonstrativo_resultado_estrutura_mes)
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/anexo/'.$demonstrativo['cd_demonstrativo_resultado'].'/'.$demonstrativo_mes['cd_demonstrativo_resultado_mes']) ?>/"+cd_demonstrativo_resultado_estrutura_mes;
    }
</script>
<?php
    $abas[] = array('aba_minhas', 'Lista', FALSE, 'ir_minhas();');
    $abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

    $head = array(
        'Ordem',
        '',
        'Arquivo',
        'Dt. Inclusão',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $config = array(
            'name'   => 'nr_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'], 
            'id'     => 'nr_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'],
            'onblur' => "set_ordem(".$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].");",
            'style'  => "display:none; width:50px;"
        );

        $editar = 
            '<span id="ajax_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].'"></span> '.
            '<script> jQuery(function($){ $("#nr_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].'").numeric(); }); </script>'.
            '<a id="editar_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].');" title="Editar a ordem">[editar]</a>'.
            '<a id="salvar_ordem_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>';

        $body[] = array(
            '<span id="nr_ordem_valor_'.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].'">'.$item['nr_ordem'].'</span>'.
            form_input($config, $item['nr_ordem']),
            $editar,
            array(anchor(base_url().'up/demonstrativo_resultado/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            $item['dt_inclusao'],
            $item['ds_usuario'],
            '<a href="javascript:void(0);" onclick="excluir_anexo('.$item['cd_demonstrativo_resultado_estrutura_mes_anexo'].')">[excluir]</a>'
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_open('gestao/demonstrativo_resultado/salvar_anexo');	
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_demonstrativo_resultado', '', $demonstrativo['cd_demonstrativo_resultado']);
                echo form_default_hidden('cd_demonstrativo_resultado_mes', '', $demonstrativo_mes['cd_demonstrativo_resultado_mes']);
                echo form_default_row('', 'Ano/Mês:', $demonstrativo['nr_ano'].'/'.$demonstrativo_mes['cd_mes']);
                echo form_default_row('', 'Dt. Solicitação:', $demonstrativo_mes['dt_inclusao']);
                echo form_default_row('', 'Dt. Limite:', $demonstrativo_mes['dt_limite']);
            echo form_end_box('default_box');
            if(!$fl_fechado)
            { 
            	echo form_start_box('default_arquivo_box', 'Anexo');
                	echo form_default_dropdown('cd_demonstrativo_resultado_estrutura_mes', 'Item: (*)', $estrutura, $demonstrativo_resultado_mes['cd_demonstrativo_resultado_estrutura_mes'], 'onchange="ir_estrutura($(this).val())"');

                    if(intval($demonstrativo_resultado_mes['cd_demonstrativo_resultado_estrutura_mes']) > 0)
                    {
                        echo form_default_hidden('nr_ordem', '', $nr_ordem);
                        echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'demonstrativo_resultado', 'validaArq');
                        echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
                    }

                echo form_end_box('default_arquivo_box');
        	    echo form_command_bar_detail_start();

                    if(count($collection) > 0)
                    {
                        echo button_save('Fechar Item', 'fechar_estrutura_mes();'); 
                    }

                echo form_command_bar_detail_end();
            }
            else
            {
                echo form_start_box('default_arquivo_box', 'Anexo');
                    echo form_default_dropdown('cd_demonstrativo_resultado_estrutura_mes', 'Item:', $estrutura, $demonstrativo_resultado_mes['cd_demonstrativo_resultado_estrutura_mes'], 'onchange="ir_estrutura($(this).val())"');
                    echo form_default_row('', 'Dt. Fechamento:', $demonstrativo_resultado_mes['dt_fechamento']);
                    echo form_default_row('', 'Usuário:', $demonstrativo_resultado_mes['ds_usuario']);
                    echo form_default_row('', 'Arquivo:', anchor(base_url().'up/demonstrativo_resultado/'.$demonstrativo_resultado_mes['arquivo'], $arquivo_nome, array('target' => '_blank')));
                echo form_end_box('default_arquivo_box');
            }
        echo form_close();
	    echo br();
        if((count($collection) > 0) AND (!$fl_fechado))
        {
            echo $grid->render();
            echo br();
        }
    echo aba_end();

$this->load->view('footer_interna');
?>        