<?php
    set_title('FAX Recebido');
    $this->load->view('header');
?>

<script>
    <?php
        echo form_default_js_submit(Array('descricao'));
    ?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/fax_recebido"); ?>';
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_lista', 'Acompanhamento', TRUE, 'location.reload();');
    
    $body=array();
    $head = array( 
        'Dt Cadastro',
        'Usuário',
        'Descrição'
    );
    
    foreach($collection as $item)
    {
        $body[] = array(
          $item['dt_cadastro'],
          $item['nome'],
          array(nl2br($item['descricao']), 'text-align:justify')
          );
    }
    
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head       = $head;
    $grid->body       = $body;
    
    echo aba_start( $abas );
        echo form_open('ecrm/fax_recebido/salvar_acompanhamento', 'name="filter_bar_form_cadastro"');
            echo form_start_box( "default_box", "Cadastro" );
                echo form_default_hidden('cd_fax', '', $cd_fax);
                echo form_default_textarea('descricao', 'Acompanhamento :*', '', "style='height:100px;'");
            echo form_end_box("default_box");
            echo form_command_bar_detail_start();   
                echo button_save("Salvar");
            echo form_command_bar_detail_end();
        echo form_close();
        echo $grid->render();
    echo aba_end(); 

    $this->load->view('footer');
?>