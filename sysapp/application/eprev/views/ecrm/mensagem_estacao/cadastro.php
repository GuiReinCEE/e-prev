<?php
set_title('Mensagens nas Estações');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('nome', 'dt_inicio', 'hr_inicio', 'dt_final', 'hr_final'), 'valida(form);');
?>
	function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/mensagem_estacao"); ?>';
    }
	
	function valida(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
			var fl_marcado = false;
			$("input[type='checkbox'][id='ar_gerencia']").each( 
				function() 
				{ 
					if (this.checked) 
					{ 
						fl_marcado = true;
					} 
				}
			);				
				
			if(!fl_marcado)
			{
				alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
				return false;
			}
			else
			{
				if( confirm('Salvar?') )
				{
					form.submit();
				}
			}
        }
    }
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("ecrm/mensagem_estacao/excluir/".intval($row['cd_mensagem_estacao'])); ?>';
        }
		
	}

</script>

<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/mensagem_estacao/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_mensagem_estacao', '', $row);
			echo form_default_text('nome', 'Nome: *', $row, 'style="width:400px;"');
			echo form_default_upload_iframe('arquivo', 'mensagem_estacao', 'Arquivo :*', array($row['arquivo'],$row['arquivo_nome']), 'mensagem_estacao', true);
			if(intval($row['cd_mensagem_estacao']) > 0)
			{
				echo form_default_row('','',img(base_url().'/up/mensagem_estacao/'.$row['arquivo']));
			}
			echo form_default_row('','','<i style="font-size:90%">As dimensões ideais para mensagem são de 650x450 para exibição sem barras de rolagem, mas as imagens podem ser de qualquer dimensão.</i>');
			echo form_default_text('url', 'Url: ', $row, 'style="width:400px;"');
			echo form_default_row('','','<i style="font-size:90%">Link para clicar na mensagem exibida. Não obrigatório. Exemplo: http://www.fundacaoceee.com.br<br/>
Você pode utilizar a variável [USUARIO] para ser substituída pelo usuário.<br/></i>');
			echo form_default_date('dt_inicio', "Data início:*", $row);
			echo form_default_time('hr_inicio', "Hora início:*", $row);
			echo form_default_date('dt_final', "Data final:*", $row);
			echo form_default_time('hr_final', "Hora final:*", $row);
			echo form_default_row('','', '<i style="font-size:90%">Período que mensagem será exibida.<br/>
Lembrando que a mensagem só é exibida quando a estação de trabalho é iniciada.<br/>
É permitido somente uma mensagem por período, portanto informe um período que ainda não tenha mensagem agendada.</i>');
			echo form_default_checkbox_group('ar_gerencia', 'Gerências: *', $ar_gerencia, $ar_gerencia_checked, 200);
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();  
			echo button_save("Salvar");
			echo button_save("Excluir", 'excluir()', 'botao_vermelho');
		echo form_command_bar_detail_end();
		
	echo form_close();
	
	echo br(2);

echo aba_end();

$this->load->view('footer_interna');