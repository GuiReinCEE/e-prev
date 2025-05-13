<?php
set_title('Reunião Sistema de Gestão Tipo - Indicador');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao_tipo') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao_tipo/cadastro/'.$row['cd_reuniao_sistema_gestao_tipo']) ?>";
    }

    function ir_cadastro_ordem()
    {
        location.href = "<?= site_url('gestao/reuniao_sistema_gestao_tipo/cadastro_ordem/'.$row['cd_reuniao_sistema_gestao_tipo']) ?>";
    }

    $(function(){
        $("#FilterTextBox_table").keypress(function(event){
            if (event.keyCode == 13) 
            {
                event.preventDefault();
                return false;
            }
        });  

        $("#indicador_checked_table tbody tr:has(td)").each(function(){
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
                    

                $("#indicador_checked_table tbody tr:hidden").show();
                $.each(s, function(){
                    $("#indicador_checked_table tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
                });
                
                var rowCount = $("#indicador_checked_table tbody tr:visible").length;
                $("#gridCount'.$this->id_tabela.'").html(rowCount);
            }
        });
    });
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_indicador', 'Indicador', TRUE, 'location.reload();');
$abas[] = array('aba_cadastro_ordem', 'Ordenação dos Processos', FALSE, 'ir_cadastro_ordem();');

echo aba_start( $abas );
    echo form_open('gestao/reuniao_sistema_gestao_tipo/indicador_salvar');
        echo form_start_box('default_box', 'Cadastro');			
			echo form_default_hidden('cd_reuniao_sistema_gestao_tipo', '', $row['cd_reuniao_sistema_gestao_tipo']);
            echo form_default_row('ds_reuniao_sistema_gestao_tipo', 'Tipo:', $row['ds_reuniao_sistema_gestao_tipo']);
            echo form_default_row('', '', '<div>Procurar: <input type="text" id="FilterTextBox_table" name="FilterTextBox" style="width: 400px;"></div>');
            echo form_default_checkbox_group('indicador_checked', 'Indicador:', $indicador, $indicador_check, 450, 500, "", TRUE);
        echo form_end_box('default_box');
        echo form_command_bar_detail_start();   
            echo button_save('Salvar');
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>