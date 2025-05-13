<?php
set_title('Pré-cadastro SINPRORS');
$this->load->view('header');
?>
<script>
    <?php
    echo form_default_js_submit(Array('cd_pre_cadastro', 'ds_nome', 'nr_cpf', 'ds_duvida'));
    ?>
    function ir_aba_lista()
    {
        location.href='<?php echo site_url("planos/sinprors_pre_cadastro"); ?>';
    }
    
    function ir_acomp(cd)
        {
            location.href='<?php echo site_url("planos/sinprors_pre_cadastro/acompanhamento"); ?>/'+cd;
        }
    
    $(document).ready(function ()
    {
        jQuery(function($){
	   $("#nr_telefone").mask("(99) 9999-9999");
	});	
    })
</script>
<?php
$abas[] = array('aba_lista', 'Pré-cadastro', FALSE, 'ir_aba_lista();');
$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

if ($row['cd_pre_cadastro'] > 0)
{
    $abas[] = array('aba_nc', 'Acompanhamento', FALSE, 'ir_acomp(' . $row['cd_pre_cadastro'] . ');');
}

echo aba_start($abas);
    echo form_open('planos/sinprors_pre_cadastro/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
             echo form_default_hidden('cd_pre_cadastro', "Código:", $row, "style='width:100%;border: 0px;' readonly");
             echo form_default_text('ds_nome', 'Nome :*', $row, "style='width:300px'");
             echo form_default_text('ds_email', 'Email :', $row, "style='width:300px'");
             echo form_default_integer("nr_telefone", "Telefone :", $row); 
             echo form_default_text("nr_matricula", "Matrícula :", $row); 
             echo form_default_cpf("nr_cpf", "CPF :*", $row);
             echo form_default_date('dt_nascimento', "Dt Nascimento :", $row);
             echo form_default_textarea('ds_duvida', "Duvida :*", $row, "style='width:500px;'");
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();

    echo form_close();

    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>