<?php
set_title('Pré-cadastro SINPRORS');
$this->load->view('header');
?>
<script>
    <?php
    echo form_default_js_submit(Array('cd_enviado', 'observacao'));
    ?>
    function ir_aba_lista()
    {
        location.href='<?php echo site_url("planos/sinprors_pre_cadastro"); ?>';
    }
    
    function ir_cadastro(cd)
    {
        location.href='<?php echo site_url("planos/sinprors_pre_cadastro/cadastro"); ?>/'+cd;
    }
    
</script>
<?php
$abas[] = array('aba_lista', 'Pré-cadastro', FALSE, 'ir_aba_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro('.$cd_pre_cadastro.');');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');

$arr_enviado[] = Array('value' => 'A', 'text' => 'Enviado Amauri Bueno');
$arr_enviado[] = Array('value' => 'M', 'text' => 'Enviado Mongeral');
$arr_enviado[] = Array('value' => 'O', 'text' => 'Outro');

echo aba_start($abas);
    echo form_open('planos/sinprors_pre_cadastro/salvar_acompanhamento', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
            echo form_default_hidden('cd_pre_cadastro', "Código:", $cd_pre_cadastro, "style='width:100%;border: 0px;' readonly");
            echo form_default_dropdown('cd_enviado', 'Ação:*', $arr_enviado);
            echo form_default_textarea('observacao', "Descrição:*");
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();

    echo form_close();
    
    $body=array();
    $head = array(
        'Dt Cadastro',
        'Usuário',
        'Ação',
        'Observação'
    );
    
    foreach($collection as $item )
    {
        $enviado = '';
    
        if($item["cd_enviado"] == 'A')
        {
            $enviado = 'Enviado Amauri Bueno';
        }
        else if($item["cd_enviado"] == 'M')
        {
            $enviado = 'Enviado Mongeral';
        }
        else if($item["cd_enviado"] == 'O')
        {
            $enviado = 'Outro';
        }
        
        $body[] = array(
        $item["dt_inclusao"],
          array($item["nome"],"text-align:left;"),
          $enviado,
          array($item["observacao"],"text-align:justify;")
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    echo $grid->render();

    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>