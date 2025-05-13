<?php
set_title('Súmulas Interventor - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nr_sumula_interventor', 'dt_sumula_interventor', 'dt_divulgacao'), 'validacao(form);');
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/sumula_interventor"); ?>';
    }
    
    function ir_responsabilidade()
    {
        location.href='<?php echo site_url("gestao/sumula_interventor/responsabilidade/".$row['cd_sumula_interventor']); ?>';
    }
	
	function ir_acompanhamento()
	{
		location.href='<?php echo site_url("gestao/sumula_interventor/acompanhamento/".$row['cd_sumula_interventor']); ?>';
	}
    
    function validacao(form)
    {
        $.post('<?php echo site_url('/gestao/sumula_interventor/validar_nr_sumula_interventor'); ?>',
        {
            nr_sumula_interventor : $('#nr_sumula_interventor').val(),
            cd_sumula_interventor : $('#cd_sumula_interventor').val()
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
                alert('Número de súmula já existe');
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
			var cd_sumula_interventor            = $('#cd_sumula_interventor').val();
			var dt_publicacao_libera = $('#dt_publicacao_libera').val();
			
			$(".div_aba_content").html("<center><BR><BR><BR><BR><b>AGUARDE...</b><BR><BR><?php echo loader_html(); ?></center>");
			$.post('<?php echo site_url('gestao/sumula_interventor/publicar') ?>',
			{
				cd_sumula_interventor            : cd_sumula_interventor,
				dt_publicacao_libera : dt_publicacao_libera
			},
			function(data)
			{
				location.reload();
			});            
        }		
	}	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_sumula_interventor']) > 0)
{
    $abas[] = array('aba_lista', 'Responsabilidade', FALSE, 'ir_responsabilidade();');
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}

echo aba_start( $abas );
    echo form_open('gestao/sumula_interventor/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
             echo form_default_hidden('cd_sumula_interventor', '', $row['cd_sumula_interventor']);
             echo form_default_hidden('nr_sumula_interventor_salvo', '', $row['nr_sumula_interventor']);
             echo form_default_integer('nr_sumula_interventor', 'Número :*', $row);
             echo form_default_date('dt_sumula_interventor', 'Dt Súmula :*', $row);
             echo form_default_date('dt_divulgacao', 'Dt Divugalção :*', $row);
             echo form_default_upload_iframe('arquivo', 'sumula_interventor', 'Arquivo :*', array($row['arquivo'],$row['arquivo_nome']), 'sumula_interventor', (($fl_editar OR intval($row['cd_sumula_interventor']) == 0) ? true : false));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
            if($fl_editar OR intval($row['cd_sumula_interventor']) == 0)
            {
                echo button_save("Salvar");
            } 
        echo form_command_bar_detail_end();
    echo form_close();
	
	if(intval($row['cd_sumula_interventor']) > 0)
	{
		echo form_start_box("default_box", "Publicação" );
			echo form_default_date('dt_publicacao_libera', 'Dt Autoatendimento:', $row);
			if(trim($row['dt_publicacao_libera']) != "")
			{
				echo form_default_row('', 'Dt Publicação:', $row['dt_publicacao']);
				echo form_default_row('', 'Usuário Publicação:', $row['usuario_publicacao']);
			}
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar", "publicar()", "botao_vermelho", 'id="enviar_emails"');
		echo form_command_bar_detail_end();
	} 	
	
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>