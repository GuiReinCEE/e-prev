<?php
set_title('Pesquisa - Estrutura');
$this->load->view('header');
?>
<script>
    <?php 
            echo form_default_js_submit(array(
                                                'pergunta_texto',
                                                'cd_agrupamento'
                                              )); 
    ?>
    
    function agrupamentoListar()
    {
        $("#obAgrupamento").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/agrupamentoListar');?>',
        {
            cd_enquete : $('#cd_enquete').val()
        },
        function(data)
        {
            $("#obAgrupamento").html(data);
            agrupamentoListarOrdenar();
        });
    }   
    
    function agrupamentoListarOrdenar()
    {
        var ob_resul = new SortableTable(document.getElementById("tbAgrupamento"),
        [
            'Number',
			'CaseInsensitiveString',
			'Number'
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
        ob_resul.sort(2, false);
    }   
    
    function questaoListar()
    {
        $("#obQuestao").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/questaoListar');?>',
        {
            cd_enquete : $('#cd_enquete').val()
        },
        function(data)
        {
            $("#obQuestao").html(data);
            //questaoListarOrdenar();
        });
    }   
    
    function questaoListarOrdenar()
    {
        var ob_resul = new SortableTable(document.getElementById("tbQuestao"),
        [
            'Number',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
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
        ob_resul.sort(14, false);
    }   
    
    function respostaListar()
    {
        $("#obResposta").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('/ecrm/operacional_enquete/respostaListar');?>',
        {
            cd_enquete : $('#cd_enquete').val()
        },
        function(data)
        {
            $("#obResposta").html(data);
            respostaListarOrdenar();
        });
    }   
    
    function respostaListarOrdenar()
    {
        var ob_resul = new SortableTable(document.getElementById("tbResposta"),
        [
            'Number',
            'CaseInsensitiveString',
            'Number',
            'Number'
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
        ob_resul.sort(3, false);
    }   
    
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/operacional_enquete") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/cadastro/".intval($ar_cadastro['cd_enquete'])) ?>';
    }

    function ir_resultado()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/resultado/".intval($ar_cadastro['cd_enquete'])) ?>';
    }
    
    function novo_agrupamento(cd_agrupamento)
    {
        location.href='<?= site_url("ecrm/operacional_enquete/agrupamento/".intval($ar_cadastro['cd_enquete'])) ?>/' + cd_agrupamento;
    }   
    
    function nova_questao(cd_questao)
    {
        location.href='<?= site_url("ecrm/operacional_enquete/questao/".intval($ar_cadastro['cd_enquete'])) ?>/' + cd_questao;
    }

    function nova_resposta(cd_resposta)
    {
        location.href='<?= site_url("ecrm/operacional_enquete/resposta/".intval($ar_cadastro['cd_enquete'])) ?>/' + cd_resposta;
    }   

    $(function() {
        agrupamentoListar();
        questaoListar();
        respostaListar();
    }); 
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultados', FALSE, 'ir_resultado();');

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/estruturaSalvar');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_enquete', '', $ar_cadastro['cd_enquete']);
            echo form_default_hidden('cd_pergunta_texto', '', intval($ar_cadastro['cd_pergunta_texto']));
            echo form_default_row('cd_enquete_label', "Pesquisa:", '<span class="label label-success">'.$ar_cadastro["cd_enquete"].' - '.$ar_cadastro["ds_titulo"].'</span>');
        echo form_end_box("default_box");
        
        echo form_start_box("dissertativa_box", "Questão dissertativa");
            echo form_default_textarea('pergunta_texto', "Questão dissertativa:", $ar_cadastro['pergunta_texto'], "style='width:500px; height: 100px;'");
            echo form_default_dropdown('cd_agrupamento', 'Agrupamento da Questão dissertativa:', $ar_agrupamento, Array($ar_cadastro['cd_agrupamento']));
        echo form_end_box("dissertativa_box");      
        echo form_command_bar_detail_start();    
            if(trim($ar_cadastro['fl_editar']) == "S")
            {
                echo button_save("Salvar");
            }
        echo form_command_bar_detail_end();         
        
        echo form_start_box("agrupamento_box", "Agrupamento(s)",FALSE);
            if(trim($ar_cadastro['fl_editar']) == "S")
            {
                echo button_save("Novo Agrupamento","novo_agrupamento(0)").br(2);
            }		
            echo '<div id="obAgrupamento"></div>';
        echo form_end_box("agrupamento_box");           
    
        echo form_start_box("questoes_box", "Questionamento(s)",FALSE);
            if(trim($ar_cadastro['fl_editar']) == "S")
            {
                echo button_save("Nova Questão","nova_questao(0)").br(2);
            }		
            echo '<div id="obQuestao"></div>';
        echo form_end_box("questoes_box");  

        echo form_start_box("resposta_box", "Resposta Escala/Graduação",FALSE);
            if(trim($ar_cadastro['fl_editar']) == "S")
            {
                echo button_save("Nova Resposta Escala/Graduação","nova_resposta(0)").br(2);
            }		
            echo '<div id="obResposta"></div>';
        echo form_end_box("resposta_box");

    echo form_close();
    echo br(10);    
echo aba_end();

$this->load->view('footer_interna');
?>