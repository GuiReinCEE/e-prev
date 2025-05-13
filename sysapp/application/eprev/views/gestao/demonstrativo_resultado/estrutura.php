<?php
    set_title('Demonstrativo de Resultados');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(
        'ds_demonstrativo_resultado_estrutura', 
        'nr_ordem', 
        'cd_demonstrativo_resultado_estrutura_tipo'
    ),'valida_form(form);') ?>

    function valida_form(form)
    {
        var save = true;

        if($('#cd_demonstrativo_resultado_estrutura_tipo').val() != 2)
        {
            if($('#cd_gerencia').val() == '')
            {
               alert('Favor preencher Gerência Responsável.');

               save = false;
            }
            else if($('#cd_usuario_responsavel').val() == '')
            {
                alert('Favor preencher Responsável.');

                save = false;
            }
        }
      
        if(save)
        {
            if(confirm('Salvar?'))
            {   
                form.submit();
            }
        }
    }
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/index') ?>";
    }

    function ir_meses()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/meses/'.$row['cd_demonstrativo_resultado']) ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/estrutura/'.$row['cd_demonstrativo_resultado']) ?>";
    }

    function get_usuario(cd_gerencia)
    {
        $.post("<?= site_url('gestao/demonstrativo_resultado/get_usuarios') ?>",
        {
            cd_gerencia : cd_gerencia
        },
        function(data)
        {
            var responsavel = $("#cd_usuario_responsavel");
            var substituto  = $("#cd_usuario_substituto");
                                    
            if(responsavel.prop) 
            {
                var responsavel_opt = responsavel.prop("options");
            }
            else
            {
                var responsavel_opt = responsavel.attr("options");
            }

            if(substituto.prop) 
            {
                var substituto_opt = substituto.prop("options");
            }
            else
            {
                var substituto_opt = substituto.attr("options");
            }

            $("option", responsavel).remove();
            $("option", substituto).remove();

            responsavel_opt[responsavel_opt.length] = new Option("Selecione", "");
            substituto_opt[substituto_opt.length] = new Option("Selecione", "");

            $.each(data, function(val, text) {
                responsavel_opt[responsavel_opt.length] = new Option(text.text, text.value);
                substituto_opt[substituto_opt.length] = new Option(text.text, text.value);
            });

        }, "json", true);
    }

    function get_tipo()
    {
        if($('#cd_demonstrativo_resultado_estrutura_tipo').val() == 2 || $('#cd_demonstrativo_resultado_estrutura_tipo').val() == '')
        {  
            $('#cd_gerencia_row').hide();
            $('#cd_usuario_responsavel_row').hide();
            $('#cd_usuario_substituto_row').hide();
        }
        else
        {
            $('#cd_gerencia_row').show();
            $('#cd_usuario_responsavel_row').show();
            $('#cd_usuario_substituto_row').show();
        }
    }

    function desativar(cd_demonstrativo_resultado_estrutura)
    {
        var confirmacao = 'Deseja desativar o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_resultado/desativar_estrutura/'.$row['cd_demonstrativo_resultado']) ?>/"+cd_demonstrativo_resultado_estrutura;
        }
    }

    function ativar(cd_demonstrativo_resultado_estrutura)
    {
        var confirmacao = 'Deseja ativar o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/demonstrativo_resultado/ativar_estrutura/'.$row['cd_demonstrativo_resultado']) ?>/"+cd_demonstrativo_resultado_estrutura;
        }
    }

    function excluir_estrutura(cd_demonstrativo_resultado_estrutura)
    {
        var confirmacao = 'Deseja excluir o item?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('gestao/demonstrativo_resultado/excluir_estrutura/'.$row['cd_demonstrativo_resultado']) ?>/'+cd_demonstrativo_resultado_estrutura;
        }
    }

    $(function(){
        get_tipo();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_estrutura', 'Estrutura', TRUE, 'location.reload();');
    $abas[] = array('aba_meses', 'Meses', FALSE, 'ir_meses();');
    
    $head = array( 
        'Ordem',
        'Estrutura',
        'Tipo',
        'Gerência',
        'Responsável',
        'Substituto',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {   
        $link = '';

        if(trim($item['fl_pai']) == 'N')
        {
            if(trim($item['dt_desativado']) == '')
            {
                $link = '<a href="javascript:void(0);" onclick="desativar('.$item['cd_demonstrativo_resultado_estrutura'].');" style="color:red;">[desativar]</a> ';
            }
            else
            {
                $link = '<a href="javascript:void(0);" onclick="ativar('.$item['cd_demonstrativo_resultado_estrutura'].');" style="color:green;">[ativar]</a> ';
            }

            $link .= '<a href="javascript:void(0);" onclick="excluir_estrutura('.$item['cd_demonstrativo_resultado_estrutura'].' )">[excluir]</a>';
        }        
                
        $body[] = array(
            array($item['nr_ordem'], 'text-align:left;'),
            array(anchor('gestao/demonstrativo_resultado/estrutura/'.$item['cd_demonstrativo_resultado'].'/'.$item['cd_demonstrativo_resultado_estrutura'], $item['ds_demonstrativo_resultado_estrutura']), 'text-align:left;'),
            array($item['ds_demonstrativo_resultado_estrutura_tipo'], 'text-align:left;'),
            array($item['ds_gerencia'], 'text-align:left;'),
            array($item['ds_usuario_responsavel'], 'text-align:left;'),
            array($item['ds_usuario_substituto'], 'text-align:left;'),
            $link
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    
    echo aba_start($abas);
        echo form_open('gestao/demonstrativo_resultado/salvar_estrutura');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_demonstrativo_resultado', '', $row['cd_demonstrativo_resultado']);
                echo form_default_hidden('cd_demonstrativo_resultado_estrutura', '', $row['cd_demonstrativo_resultado_estrutura']);
                echo form_default_hidden('cd_demonstrativo_resultado_estrutura_pai', '', $row['cd_demonstrativo_resultado_estrutura_pai']);

				echo form_default_row('nr_ano', 'Ano:', '<span class="label label-inverse">'.$demonstrativo['nr_ano'].'</span>');
                echo form_default_text('ds_demonstrativo_resultado_estrutura', 'Estrutura: (*)', $row['ds_demonstrativo_resultado_estrutura'], 'style="width:300px;"');
                echo form_default_dropdown('cd_demonstrativo_resultado_estrutura_pai', 'Estrutura Pai:', $estrutura_pai, $row['cd_demonstrativo_resultado_estrutura_pai']);
                echo form_default_integer('nr_ordem', 'Ordem: (*)', $row['nr_ordem']); 
                echo form_default_dropdown('cd_demonstrativo_resultado_estrutura_tipo', 'Tipo: (*)', $tipo, $row['cd_demonstrativo_resultado_estrutura_tipo'],'onchange="get_tipo(this.value)"');
                echo form_default_dropdown('cd_gerencia', 'Gerência: (*)', $gerencia, $row['cd_gerencia'], 'onchange="get_usuario(this.value)"');
                echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuario, $row['cd_usuario_responsavel']);
                echo form_default_dropdown('cd_usuario_substituto', 'Substituto: (*)', $usuario, $row['cd_usuario_substituto']);
                
                if(intval($row['cd_demonstrativo_resultado_estrutura']) > 0)
                {
                    if(trim($row['dt_desativado']) != '')
                    {
                        echo form_default_row('', 'Dt. Desativado:', $row['dt_desativado']);
                        echo form_default_row('', 'Usuário:', $row['ds_usuario_desativado']);
                    }
                }                               
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
                if(intval($row['cd_demonstrativo_resultado_estrutura']) > 0)
                {
                    echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>