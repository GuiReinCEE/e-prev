<?php 
	set_title('Sócio Instituidor - Cadastro');
	$this->load->view('header'); 
?>
<script>
    <?= form_default_js_submit(array('cd_empresa', 'nome', 'fl_indicacao'), 'valida_empresa(form)') ?>

    function valida_empresa(form)
    {
        if($("#fl_indicacao").val() == 'S' && $("#cd_gerencia_indicacao").val() == '')
        {
            alert('Informe a Gerência que fez a indicação.');

            return false;
        }
        else
        {
            if((($("#cd_empresa").val() == 24)  || ($("#cd_empresa").val() == 29)) && $("#cpf_participante").val() == '')
            {
                alert('Necessário informar o CPF do PARTICIPANTE.');

                return false;
            }
            else
            {
                verifica_cpf(form);
            }
        }
    }

    function valida_responsavel(form)
    {
        var fl_marcado = false;
        
        $("input[type='checkbox'][id='responsavel']").each( 
            function() 
            { 
                if (this.checked) 
                { 
                    fl_marcado = true;
                } 
            }
        );

        if(($('#cd_usuario_indicacao').val() == '') && (!fl_marcado))
        {
            
            alert("Informe ao menos um Responsável!");
            return false;
        }
        else
        {   
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }               
    }

    function get_usuarios(cd_gerencia, campo)
    {
        $.post("<?= site_url('ecrm/socio_instituidor/get_usuarios') ?>",
        {
            cd_gerencia : cd_gerencia
        },
        function(data)
        {
            if(campo == 0)
            {
                var select = $('#cd_usuario_indicacao'); 
            }
            
            if(select.prop) 
            {
                var options = select.prop('options');
            }
            else
            {
                var options = select.attr('options');
            }
            
            $('option', select).remove();
            
            options[options.length] = new Option('Selecione', '');
            
            $.each(data, function(val, text) {
                options[options.length] = new Option(text.text, text.value);
            });
            
        }, 'json', true);
    }

    function verifica_cpf(form)
    {
        var confirmacao;

        $.post("<?= site_url('ecrm/socio_instituidor/verifica_cpf') ?>",
        {
            cd_empresa : $("#cd_empresa").val(),
            cpf        : $("#cpf").val()
        },
        function(data)
        {
            if(data && (typeof(data.ds_socio) != "undefined"))
            {
                confirmacao = "Esse CPF já foi cadastrado para esse Instituidor\n\n"+
                    "Dt. Inclusão: "+data.dt_inclusao+"\n"+
                    "Dt. Validação: "+data.dt_validacao+"\n"+
                    "Status: "+data.ds_socio+"\n\n"+
                    "Deseja Salvar mesmo assim?"
            }
            else
            {   
                confirmacao = "Salvar?";
            }

            if(confirm(confirmacao))
            {
                form.submit();
            }
        }, "json", false);

    }

    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/socio_instituidor') ?>";
    }

    function ir_email()
    {
        location.href = "<?= site_url('ecrm/socio_instituidor/email') ?>";
    }	

    function excluir(cd_socio_instituidor)
    {
        var confirmacao = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja EXCLUIR?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/socio_instituidor/excluir/'.$row['cd_socio_instituidor_pacote']); ?>/"+cd_socio_instituidor;
        }
    }
  
    function excluir_pacote()
    {
        var confirmacao = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nDeseja EXCLUIR o PACOTE?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/socio_instituidor/excluir_pacote/'.$row['cd_socio_instituidor_pacote']); ?>";
        }
    }

    function enviar()
    {
        var confirmacao = "ATENÇÃO\n\nEsta ação é IRREVERSÍVEL.\n\nConfirma o envio do email:\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/socio_instituidor/enviar/'.$row['cd_socio_instituidor_pacote']) ?>";
        }
    }
	
	function cancelar()
	{
		location.href = "<?= site_url('ecrm/socio_instituidor/cadastro/'.$row['cd_socio_instituidor_pacote']) ?>";
	}

    function set_indicacao()
    {
        if($("#fl_indicacao").val() == 'S')
        {   
            $("#cd_gerencia_indicacao_row").show();
            $("#cd_usuario_indicacao_row").show();
        }
        else
        {
            $("#cd_gerencia_indicacao_row").hide();
            $("#cd_usuario_indicacao_row").hide();
        }
    }
	
    $(function(){
        $("#cd_empresa").change(function(){
            if($(this).val() == 24 || $(this).val() == 29)
            {
                $("#cpf_participante_row").show();
            }
            else
            {
                $("#cpf_participante_row").hide();
                $("#cpf_participante").val('');
            }
        });

        $("#cd_empresa").change();
        
        set_indicacao();
        
        $("#cd_gerencia_indicacao_row").hide();
        $("#cd_usuario_indicacao_row").hide();
    })

</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_detalhe', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_email', 'Email', FALSE, 'ir_email();');
 
    $this->load->helper('grid');

    $head = array( 
        'Código', 
        'Instituidor',
        'CPF Participante',
        'Nome', 
        'CPF',
        'Situação',
        'Categoria',
        'Validação Anterior',
        ''
    );

    $head_anterior = array(
        'Cód. Pacote',
        'Código',
        'Dt. Inclusão',
        'Dt. Validação',
        'Situação'
    );

    $body = array();

    $body_anterior = array();

    $grid_anterior = new grid();

    $grid_anterior->view_count = false;
    $grid_anterior->view_data = false;
    
    foreach($collection as $item)
    {
        $grid_anterior->id_tabela = md5(uniqid(''));

        $body_anterior = array();

        foreach($item['anterior'] as $item2)
        {
            $body_anterior[] = array(
                anchor('ecrm/socio_instituidor/cadastro/'.$item2['cd_socio_instituidor_pacote'].'/'.$item2['cd_empresa'].'/'.$item2['cd_socio_instituidor'], $item2['cd_socio_instituidor_pacote']),
                anchor('ecrm/socio_instituidor/cadastro/'.$item2['cd_socio_instituidor_pacote'].'/'.$item2['cd_empresa'].'/'.$item2['cd_socio_instituidor'], $item2['cd_socio_instituidor']),
                $item2['dt_inclusao'],
                $item2['dt_validacao'],
                '<span class="label '.trim($item2['class_socio']).'">'.trim($item2['ds_socio']).'</span>'
            );
        }

        $grid_anterior->head = $head_anterior;
        $grid_anterior->body = $body_anterior;

        $body[] = array(
            (trim($row['dt_envio']) == '' ? anchor('ecrm/socio_instituidor/cadastro/'.$item['cd_socio_instituidor_pacote'].'/'.$item['cd_empresa'].'/'.$item['cd_socio_instituidor'], $item['cd_socio_instituidor']) : $item['cd_socio_instituidor']),
            array($item['ds_empresa'], 'text-align:left;'),
            $item['cpf_participante'],
            array($item['nome'],'text-align:left;'),
            $item['cpf'],
            '<span class="label '.trim($item['class_socio']).'">'.trim($item['ds_socio']).'</span>',
            $item['ds_socio_instituidor_categoria'],
            (count($item['anterior']) > 0 ? $grid_anterior->render() : ''),
            (trim($row['dt_envio']) == '' ? 
                anchor('ecrm/socio_instituidor/cadastro/'.$item['cd_socio_instituidor_pacote'].'/'.$item['cd_empresa'].'/'.$item['cd_socio_instituidor'], '[editar]') : '').' '.
			($fl_permissao_exclusao ? ' <a href="javascript:void(0);" onclick="excluir('.$item['cd_socio_instituidor'].')">[excluir]</a>'  : '')
            
        );

    }
    
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    $indicacao = array(
        array('value' => 'N', 'text' => 'Não'),
        array('value' => 'S', 'text' => 'Sim')
    );

    
    echo aba_start($abas);
        echo form_open('ecrm/socio_instituidor/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_socio_instituidor', '', $row['cd_socio_instituidor']);
                echo form_default_hidden('cd_socio_instituidor_pacote', '', $row['cd_socio_instituidor_pacote']);

                if(intval($row['cd_socio_instituidor']) > 0)
                {
                    echo form_default_row('cd_socio_instituidor_row', 'Código:', '<span class="label label-inverse">'.$row['cd_socio_instituidor'].'</span>');
                }

                echo form_default_row('cd_socio_instituidor_pacote_row', 'Código do Pacote:', '<span class="label label-inverse">'.$row['cd_socio_instituidor_pacote'].'</span>');

                if(trim($row['dt_envio']) == '')
                {
                    echo form_default_dropdown('cd_empresa', 'Instituidor: (*)', $empresa, $row['cd_empresa']);
                    echo form_default_cpf('cpf_participante', 'CPF Participante: (*)', $row);
                    echo form_default_text('nome', 'Nome: (*)', $row, 'style="width:400px;"');
                    echo form_default_cpf('cpf', 'CPF: (*)', $row);
                    echo form_default_dropdown('fl_indicacao', 'Indicação Interna: (*)', $indicacao, '', 'onchange="set_indicacao();"');
                    echo form_default_dropdown('cd_gerencia_indicacao', 'Gerência: (*)', $gerencia, $row['cd_gerencia_indicacao'],'onchange="get_usuarios(this.value, 0)"');
                    echo form_default_dropdown('cd_usuario_indicacao', 'Usuário Indicação: ', $usuarios, $row['cd_usuario_indicacao'] );
                }
                else
                {
                    echo form_default_row('dt_envio', 'Dt. Envio:', $row['dt_envio']);
                }

            echo form_end_box('default_box');

            
            echo form_command_bar_detail_start();
                if(trim($row['dt_envio']) == '')
                {
                    if(intval($row['cd_socio_instituidor']) > 0)
    				{
    					echo button_save('Salvar');
    					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
    				}
    				else
    				{
    					echo button_save('Adicionar');
                    }
    				
                    if(count($collection) > 0)
                    {
                        echo button_save('Enviar Email e Validar', 'enviar()', 'botao_verde');
                    }
                }

                if(intval($row['cd_socio_instituidor_pacote']) > 0 AND $fl_permissao_exclusao)
                {
                    echo button_save('Excluir Pacote', 'excluir_pacote()', 'botao_vermelho');
                }
        
            echo form_command_bar_detail_end();

        echo form_close();
        
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>