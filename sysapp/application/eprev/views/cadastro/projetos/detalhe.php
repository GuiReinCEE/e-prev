<?php
set_title('Cadastro de Projetos');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array("codigo","nome", "fl_atividade"));
?>
    function ir_lista()
    {
        location.href='<?php echo site_url("cadastro/projeto"); ?>';
    }
    
    function filtrar()
    {
        load();
    }
    
    function excluir()
    {
        var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("cadastro/projeto/excluir/".$row['codigo']); ?>';
        }

    }
    
    function excluir_envolvido(cd_envolvido)
    {
        var confirmacao = 'Deseja excluir o envolvido?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("cadastro/projeto/excluir_envolvido/".$row['codigo']); ?>/'+ cd_envolvido;
        }

    }
    
    function adicionar_pessoas()
    {
        location.href='<?php echo site_url("cadastro/projeto/adicionar_pessoas/".$row['codigo']); ?>';
    }
    
    function acompanhamento(cd_acomp)
    {
        location.href='<?php echo site_url("atividade/acompanhamento/cadastro"); ?>/'+cd_acomp;
    }    
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
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
    
    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/cadastro/projeto/listar_envolvidos', 
        {
            codigo : $('#codigo').val()
        },
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });
    }
    <?php
    if(intval($row["codigo"]) != 0)
    {
    ?>
   
    $(function(){
        filtrar();
    })
    <?php
    }
    ?>
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

$arr_tipo[] = array('value' => 'S', 'text' => 'Sistema');
$arr_tipo[] = array('value' => 'P', 'text' => 'Projeto');

$arr_atividades[] = array('value' => 'S', 'text' => 'Sim');
$arr_atividades[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);
    echo form_open('cadastro/projeto/salvar');
        echo form_start_box("default_box", "Cadastro de Projetos");
            echo form_default_hidden('salva', '', (intval($row["codigo"]) != 0 ? 1 : 0));
            echo form_default_integer("codigo", "Codigo:", $row, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
            echo form_default_text("nome", "Projeto: *", $row, "style='width:300px;'", "50");
            echo form_default_dropdown('fl_atividade', "Permite atividade(s): *", $arr_atividades, array($row['fl_atividade']));
            echo form_default_textarea("descricao", "Descrição:", $row, "style='width:300px;height:100px;'");
            echo ((intval($row["codigo"]) != 0) 
                   ? form_default_text("tip_v", "Tipo: ", (trim($row['tipo']) == 'P' ? 'Projeto' : 'Sistema'), "style='font-weight: bold; width:100%;border: 0px;' readonly")
                    .form_default_hidden('tipo', '', (trim($row['tipo']) == 'P' ? 'P' : 'S'))
                   : form_default_dropdown('tipo', "Tipo:", $arr_tipo, array($row['tipo'])));
            
            echo form_default_dropdown('area', "Área / Gerência:".br()."<div style='font-size:90%; font-style:italic;'>Gerência Responsável pela manutenção neste projeto</div>", $arr_areas, array($row['area']));
            echo form_default_dropdown('analista_responsavel', "Analista Responsável: ", $arr_analistas, array($row['analista_responsavel']));
            echo form_default_dropdown('atendente', "Gerente Responsável: ", $arr_atendentes, array($row['atendente']));
            echo form_default_dropdown('administrador1', "Responsável 1 (sponsor): ", $arr_responsaveis, array($row['administrador1']));
            echo form_default_dropdown('administrador2', "Responsável 2 (vice-sponsor): ", $arr_responsaveis, array($row['administrador2']));
            echo form_default_dropdown('nivel', "Nível: ", $arr_niveis, array($row['nivel']));
            echo form_default_dropdown('cod_projeto_superior', "Projeto Superior: ", $arr_projetos, array($row['cod_projeto_superior']));
            echo form_default_dropdown('programa_institucional', "Programa Institucional: ", $arr_institucionais, array($row['programa_institucional']));
            echo form_default_dropdown('diretriz', "Diretriz ", $arr_diretrizes, array($row['diretriz']));
            echo form_default_date("data_implantacao", "Data Implantaçao:", $row, "");
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save();
            if(intval($row["codigo"]) != 0)
            {
                echo button_save('Adicionar Pessoas Envolvidas', 'adicionar_pessoas()', 'botao_disabled');  
                echo button_save('Excluir', 'excluir()', 'botao_vermelho');
            }
            
            if(count($acompanhamento) > 0)
            {
                echo button_save('Acompanhamento', 'acompanhamento('.intval($acompanhamento['cd_acomp']).')', 'botao_disabled');
            }
        echo form_command_bar_detail_end();
    echo form_close();
        
        if(intval($row["codigo"]) != 0)
        {
            echo '<div id="result_div"></div>';
        }
    
echo aba_end();

$this->load->view('footer_interna');
?>