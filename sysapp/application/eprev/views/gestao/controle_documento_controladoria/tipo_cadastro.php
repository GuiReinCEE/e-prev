<?php
set_title('Controle de Documento - Tipo');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_controle_documento_controladoria_tipo')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/controle_documento_controladoria/tipo') ?>";
    }

    $(function(){
        $("#FilterTextBox_table").keypress(function(event){
            if (event.keyCode == 13) 
            {
                event.preventDefault();
                return false;
            }
        });  

        $("#usuario_table tbody tr:has(td)").each(function(){
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
                    

                $("#usuario_table tbody tr:hidden").show();
                $.each(s, function(){
                    $("#usuario_table tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
                });
                
                var rowCount = $("#usuario_table tbody tr:visible").length;
                $("#gridCount'.$this->id_tabela.'").html(rowCount);
            }
        });
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('gestao/controle_documento_controladoria/tipo_salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_controle_documento_controladoria_tipo', '', $row['cd_controle_documento_controladoria_tipo']);
                echo form_default_text('ds_controle_documento_controladoria_tipo', 'Tipo: (*)', $row, 'style="width:300px;"');
                echo form_default_row('', '', '<div>Procurar: <input type="text" id="FilterTextBox_table" name="FilterTextBox" style="width: 400px;"></div>');
                echo form_default_checkbox_group('usuario', 'Usuários c/ Acesso: (*)', $usuario, $usuario_com_acesso_check, 120);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>