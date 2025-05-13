<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
	function imprimir_pdf()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg/imprimir/".$cd_reuniao_sg); ?>';
    }
    
    function salva_confirmacao(fl_validacao)
    {
        var confirmacao = 'Deseja salvar a sua escolha?\n\n'+
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
		{
            location.href='<?php echo site_url("atividade/reuniao_sg/salva_confirmacao/".$cd_reuniao_sg_validacao); ?>/'+fl_validacao;
        }
    }
    
</script>
<?php
	$abas[] = array('aba_nc', 'Aprovação', TRUE, 'location.reload();');

	echo aba_start( $abas );
    echo form_start_box( "default_box", "Aprovação" );
		echo  '<center><iframe id="iframeParecer" width="600px;" style="height:300px" src="'.site_url("atividade/reuniao_sg/imprimir/".$cd_reuniao_sg).'"></iframe></center>';
    echo form_end_box("default_box");

    echo form_command_bar_detail_start();
        if($fl_validacao == '')
        {
            echo button_save("Sim Aprovo", 'salva_confirmacao("S")');
            echo button_save("Não Aprovo", 'salva_confirmacao("N")', 'botao_vermelho');
        }
	echo form_command_bar_detail_end();

    echo br(2);
	echo aba_end();
?>
<script>
	function setIframeParecer()
	{
		var nr_width = $("#default_box_title").width();
		$("#iframeParecer").width((nr_width - 16));


		var nr_height = $(window).height();
		$("#iframeParecer").height((nr_height - 250));
	}

	$(document).ready(function() {
		setIframeParecer()
		jQuery(window).resize(function() {
			setIframeParecer();
		});		
	});
</script>

<?php	
$this->load->view('footer_interna');
?>

