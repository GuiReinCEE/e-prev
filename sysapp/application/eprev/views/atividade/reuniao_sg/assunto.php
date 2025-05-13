<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
    <?php
        echo form_default_js_submit(Array('cd_reuniao_sg_assunto', 'complemento'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/reuniao_sg"); ?>';
	}
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/detalhe/".$cd_reuniao_sg); ?>';
    }
    
    function ir_parecer()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/parecer/".$cd_reuniao_sg); ?>';
    }	
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/anexo/".$cd_reuniao_sg); ?>';
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista', 'Agendamento', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
    $abas[] = array('aba_lista', 'Parecer', FALSE, 'ir_parecer();');
    $abas[] = array('aba_nc', 'Assunto', TRUE, 'location.reload();');
    
    $body = array();
    $head = array( 
        'Assunto',
        'Complemento'
    );
    
    foreach( $assuntos as $item )
    {	
        $body[] = array(
			array( anchor("atividade/reuniao_sg/assunto/".$cd_reuniao_sg.'/'.$item['cd_reuniao_sg_assunto_parecer'],  $item['ds_reuniao_sg_assunto']),"text-align:left;"),
			array($item['complemento'],"text-align:justify;")
        );
    }
    
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    
    echo aba_start( $abas );
        echo form_open('atividade/reuniao_sg/salvar_assunto');
            echo form_start_box( "default_box", "Assunto" );
                echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
                echo form_default_hidden("cd_reuniao_sg_assunto_parecer", "", $row);
                echo form_default_dropdown("cd_reuniao_sg_assunto", "Assunto:* ", $arr_assunto, array($row['cd_reuniao_sg_assunto']));	
                echo form_default_textarea('complemento', "Complemento:* ", $row);
            echo form_end_box("default_box");
            
            echo form_command_bar_detail_start();
                if($this->session->userdata('divisao') != 'SG')
                    echo button_save("Salvar");
                
            echo form_command_bar_detail_end();
        echo form_close();
        echo form_start_box( "default_box", "Assunto" );
            
            echo $grid->render();
        echo form_end_box("default_box");
    echo br(2);
	
	echo aba_end();
	$this->load->view('footer_interna');
    
?>