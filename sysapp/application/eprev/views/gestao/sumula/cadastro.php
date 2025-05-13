<?php
set_title('Súmulas - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nr_sumula', 'dt_sumula', 'dt_divulgacao'), 'validacao(form);');
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/sumula"); ?>';
    }
    
    function ir_responsabilidade()
    {
        location.href='<?php echo site_url("gestao/sumula/responsabilidade/".$row['cd_sumula']); ?>';
    }
	
	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("gestao/sumula/acompanhamento/".$row['cd_sumula']); ?>';
	}
    
    function validacao(form)
    {
        $.post('<?php echo site_url('/gestao/sumula/validar_nr_sumula'); ?>',
        {
            nr_sumula : $('#nr_sumula').val(),
            cd_sumula : $('#cd_sumula').val()
        },
        function(data)
        {             
            if(data['valida'] == 0)
            {
                if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
                {
                    alert('Nenhum arquivo foi anexado.');
                    return false;
                }
                else
                {
                    if(confirm('Salvar?'))
                    {
                        $('form').submit();  
                    }
                }
            }
            else if(data['valida'] == 1) 
            {
                alert('Número de sumula já existe');
                return false; 
            }
            else if(data['valida'] == 2) 
            {
                alert('Pauta não existe ou não está encerrada.');
                return false; 
            }
        },'json', true);
    }
    
	function publicar()
	{
        var confirmacao = 'Confirma a publicação?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
			var cd_sumula            = $('#cd_sumula').val();
			var dt_publicacao_libera = $('#dt_publicacao_libera').val();
			
			$(".div_aba_content").html("<center><BR><BR><BR><BR><b>AGUARDE...</b><BR><BR><?php echo loader_html(); ?></center>");
			$.post('<?php echo site_url('gestao/sumula/publicar') ?>',
			{
				cd_sumula            : cd_sumula,
				dt_publicacao_libera : dt_publicacao_libera
			},
			function(data)
			{
				location.reload();
			});            
        }		
	}

	function ataArquivo()
	{
		if($('#arquivo_ata').val() == '' && $('#arquivo_ata_nome').val() == '')
		{
			alert('Nenhum arquivo foi anexado.');
			return false;
		}
		else
		{
			if(confirm('Salvar?'))
			{
				$("#form_ata_salvar").submit(); 
			}
		}
	}
	
	function getSumulaAssinatura()
	{
		$('#result_div_sumula_assinatura').html("<?php echo loader_html(); ?>");

		$.post("<?php echo site_url('gestao/sumula/getSumulaAssinatura'); ?>/",
		{
			cd_sumula : $('#cd_sumula').val()
		},
		function(data)
		{
			$('#result_div_sumula_assinatura').html(data);
		});
	}

	function getAtaAssinatura()
	{
		$('#result_div_ata_assinatura').html("<?php echo loader_html(); ?>");

		$.post("<?php echo site_url('gestao/sumula/getAtaAssinatura'); ?>/",
		{
			cd_sumula : $('#cd_sumula').val()
		},
		function(data)
		{
			$('#result_div_ata_assinatura').html(data);
		});
	}	
	
	$(function(){
		 getSumulaAssinatura();
		 getAtaAssinatura();
	})	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_sumula']) > 0)
{
    $abas[] = array('aba_lista', 'Responsabilidade', FALSE, 'ir_responsabilidade();');
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}

echo aba_start( $abas );
    echo form_open('gestao/sumula/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
             echo form_default_hidden('cd_sumula', '', $row['cd_sumula']);
             echo form_default_hidden('nr_sumula_salvo', '', $row['nr_sumula']);
             echo form_default_integer('nr_sumula', 'Número :*', $row);
             echo form_default_date('dt_sumula', 'Dt Súmula :*', $row);
             echo form_default_date('dt_divulgacao', 'Dt Divugalção :*', $row);
             echo form_default_upload_iframe('arquivo', 'sumula', 'Arquivo :*', array($row['arquivo'],$row['arquivo_nome']), 'sumula', (($fl_editar OR intval($row['cd_sumula']) == 0) ? true : false));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            if($fl_editar OR intval($row['cd_sumula']) == 0)
            {
                echo button_save("Salvar");
            } 
        echo form_command_bar_detail_end();
    echo form_close();
	
	if(intval($row['cd_sumula']) > 0 AND $fl_editar ==FALSE)
	{
		echo form_start_box("default_sumula_ass_box", "Súmula - Assinatura" );
			echo form_default_row('', '', '<div id="result_div_sumula_assinatura"></div>');
		echo form_end_box("default_sumula_ass_box");
		
		echo form_start_box("default_ata_ass_box", "Ata - Assinatura" );
			echo form_default_row('', '', '<div id="result_div_ata_assinatura"></div>');
		echo form_end_box("default_ata_ass_box");
		
		echo form_start_box("default_sumula_box", "Publicação" );
			echo form_default_date('dt_publicacao_libera', 'Dt Autoatendimento:', $row);
			if(trim($row['dt_publicacao_libera']) != "")
			{
				echo form_default_row('', 'Dt Publicação:', $row['dt_publicacao']);
				echo form_default_row('', 'Usuário Publicação:', $row['usuario_publicacao']);
				echo form_default_row('', '', "");
				echo form_default_row('link_pauta', 'Link para envio Colegiados:', 'https://www.fundacaofamiliaprevidencia.com.br/sumula/?p='.$row['cd_sumula_md5']);
			}
			echo form_default_row('', '', '');
			echo form_default_row('', '', button_save("Salvar", "publicar()", "botao_vermelho"));
		echo form_end_box("default_sumula_box");
		
		if($row['dt_publicacao'] != "")
		{
			echo form_open('gestao/sumula/salvarAta', 'name="form_ata_salvar" id="form_ata_salvar"');
				echo form_start_box("default_ata_box", "Ata" );
					echo form_default_hidden('cd_sumula_ata', '', $row['cd_sumula']);
					echo form_default_upload_iframe('arquivo_ata', 'sumula', 'Arquivo :*', array($row['arquivo_ata'],$row['arquivo_ata_nome']), 'sumula', true);
					if(trim($row['dt_arquivo_ata']) != "")
					{
						echo form_default_row('', 'Dt Inclusão:', $row['dt_arquivo_ata']);
						echo form_default_row('', 'Usuário:', $row['usuario_arquivo_ata']);
					}					
					echo form_default_row('', '', '');
					echo form_default_row('', '', button_save("Salvar", "ataArquivo()", "botao_verde"));
				echo form_end_box("default_ata_box");
			echo form_close();
		}
		
			

			
		
	} 	
	
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>