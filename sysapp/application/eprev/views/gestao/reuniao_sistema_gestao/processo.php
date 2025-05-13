<?php
set_title('Reunião Sistema de Gestão - Processo');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/anexo/'.$row['cd_reuniao_sistema_gestao']) ?>";
    } 

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/indicador/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    function ir_cadastro_ordem()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao/cadastro_ordem/'.$row['cd_reuniao_sistema_gestao']) ?>";
    }

    $(function(){
        $("#FilterTextBox_table").keypress(function(event){
            if (event.keyCode == 13) 
            {
                event.preventDefault();
                return false;
            }
        });  

        $("#processo_checked_table tbody tr:has(td)").each(function(){
            var t = $(this).text().toLowerCase();
            $("<td class=\'indexColumn\' style=\'display:none;\'></td>").hide().text(removeAccents_table(t)).appendTo(this);
        });

        $("#FilterTextBox_table").keyup(function(event){
            if (event.keyCode == 27) 
            {
                $("#FilterTextBox_table").val("").keyup();
            }
            else
            {
                var s = $(this).val();
                    s = removeAccents_table(s);
                    s = s.toLowerCase().split(" ");
                    

                $("#processo_checked_table tbody tr:hidden").show();
                $.each(s, function(){
                    $("#processo_checked_table tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
                });
                
                var rowCount = $("#processo_checked_table tbody tr:visible").length;
                $("#gridCount'.$this->id_tabela.'").html(rowCount);
            }
        });
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_processo', 'Processo', TRUE, 'location.reload();');
    $abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
    $abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', FALSE, 'ir_cadastro_ordem();');
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

    echo aba_start($abas);
        echo form_open('gestao/reuniao_sistema_gestao/processo_salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_reuniao_sistema_gestao', '', $row['cd_reuniao_sistema_gestao']);
                echo form_default_row('dt_reuniao_sistema_gestao', 'Data:', $row['dt_reuniao_sistema_gestao']);
                echo form_default_row('ds_reuniao_sistema_gestao_tipo', 'Tipo:', $row['ds_reuniao_sistema_gestao_tipo']);
                echo form_default_row('', '', '<div>Procurar: <input type="text" id="FilterTextBox_table" name="FilterTextBox" style="width: 400px;"></div>');
                echo form_default_checkbox_group('processo_checked', 'Processo:', $processo, $processo_checked, 350, 600);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                if(trim($row['dt_encerramento']) == '')
                { 
                    echo button_save('Salvar');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>