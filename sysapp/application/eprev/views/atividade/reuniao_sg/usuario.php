<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
    <?php
        echo form_default_js_submit(Array('cd_usuario_validacao'));
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
        location.href='<?php echo site_url("atividade/reuniao_sg/anexo/" . $cd_reuniao_sg); ?>';
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista', 'Agendamento', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
    $abas[] = array('aba_lista', 'Parecer', FALSE, 'ir_parecer();');
    $abas[] = array('aba_nc', 'Aprovação', TRUE, 'location.reload();');
    
    $this->load->helper('grid');
    $grid = new grid();
    
    $body=array();
    
    $head = array( 
        'Usuário',
        'Confirmação',
        'Data de Aprovação',
        'Data do Envio'
    );
    
    foreach( $usuarios as $item )
    {	
        switch ($item['fl_validacao'])
        {
            case 'S':
                $validacao = '<label style="font-weight:bold">Sim</label>';
                break;
            case 'N':
                $validacao = '<label style="font-weight:bold; color:red">Não</label>';
                break;
            default :
                $validacao = 'Não Informado';
                break;
        }
        
        $body[] = array(
                    array($item['nome'],"text-align:left;"),
                    $validacao,
                    $item['dt_validacao'],
                    $item['dt_envio']
        );
    }
    
    $grid->head = $head;
    $grid->body = $body;
    
        echo aba_start( $abas );
        echo form_open('atividade/reuniao_sg/salvar_usuario');
            echo form_start_box( "default_box", "Cadastro" );
                echo form_default_hidden("cd_reuniao_sg", "", $cd_reuniao_sg);
                echo form_default_usuario_ajax("cd_usuario_validacao", 'GIN', '', "Usuário:* ");	
            echo form_end_box("default_box");
            
            echo form_command_bar_detail_start();
                if($this->session->userdata('divisao') != 'SG')
                    echo button_save("Salvar");
            echo form_command_bar_detail_end();
        echo form_close();
        echo form_start_box( "default_box", "Lista" );
            
            echo $grid->render();
        echo form_end_box("default_box");
    echo br(2);
	
	echo aba_end();
$this->load->view('footer_interna');
?>