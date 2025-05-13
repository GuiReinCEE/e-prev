<?php
set_title('Desenquadramento - Cadastro');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array(
		'cd_desenquadramento_cci_fundo', 
		'cd_desenquadramento_cci_administrador',
		'cd_desenquadramento_cci_gestor',
		'regra',
		'ds_desenquadramento_cci',
		'providencias_adotadas',
		'fl_status'), 'valida_dt_regularizado(form);');
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/desenquadramento_cci"); ?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url("gestao/desenquadramento_cci/acompanhamento/".intval($row['cd_desenquadramento_cci'])); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("gestao/desenquadramento_cci/anexo/".intval($row['cd_desenquadramento_cci'])); ?>';
    }	
	
	function encaminhar()
    {
		if(confirm('Deseja encaminhar?'))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/encaminhar/".intval($row['cd_desenquadramento_cci'])); ?>';
		}
    }
	
	function confirmar()
    {
		if(confirm('Deseja confirmar?'))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/confirmar/".intval($row['cd_desenquadramento_cci'])); ?>';
		}
    }
	
	function devolver()
    {
		if(confirm('Deseja devolver?'))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/devolver/".intval($row['cd_desenquadramento_cci'])); ?>';
		}
    }
	
	function regularizar()
	{
		if(confirm('Deseja informar a Regularização do Desenquadramento?'))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/cadastro/0/".intval($row['cd_desenquadramento_cci'])); ?>';
		}	
	}

	function excluir()
	{
		if(confirm('Deseja excluir o Desenquadramento?'))
		{
			location.href='<?php echo site_url("gestao/desenquadramento_cci/excluir/".intval($row['cd_desenquadramento_cci'])); ?>';
		}
	}
    
	function imprimirPDF()
	{
		location.href='<?php echo site_url("gestao/desenquadramento_cci/pdf/".intval($row['cd_desenquadramento_cci'])); ?>';
	}	
	
    function valida_dt_regularizado(form)
    {
		var fl_status       = $('#fl_status').val();
		var dt_regularizado = $('#dt_regularizado').val();
		
		if(fl_status == 'R')
		{
			if(dt_regularizado == '')
			{
				alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[dt_regularizado]" );
				$("#dt_regularizado").focus();
				return false;
			}
			else
			{
				$(form).submit();
				return true;
			}
		}
		else
		{
			$(form).submit();
			return true;
		}
	}
	
	function regularizado()
	{
		var fl_status = $('#fl_status').val();
		
		if(fl_status == 'R')
		{
			$('#dt_regularizado_row').show();
		}
		else
		{
			$('#dt_regularizado_row').hide();
		}
	}
	
	$(function(){
		regularizado();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_desenquadramento_cci']) > 0)
{
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
}


if(intval($cd_desenquadramento_cci_pai) == 0)
{
	$arr_status[] = Array('value' => 'P', 'text' => 'Desenquadrado');
}
$arr_status[] = Array('value' => 'D', 'text' => 'Desenquadramento Passivo');
$arr_status[] = Array('value' => 'R', 'text' => 'Regularizado');

$config_fundo         = array('gestao.desenquadramento_cci_fundo', 'cd_desenquadramento_cci_fundo', 'ds_desenquadramento_cci_fundo');
$config_administrador = array('gestao.desenquadramento_cci_administrador', 'cd_desenquadramento_cci_administrador', 'ds_desenquadramento_cci_administrador');
$config_gestor        = array('gestao.desenquadramento_cci_gestor', 'cd_desenquadramento_cci_gestor', 'ds_desenquadramento_cci_gestor');

echo aba_start( $abas );
    echo form_open('gestao/desenquadramento_cci/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
			if(trim($row['ano_numero_pai']) != "")
			{
				echo form_default_row('ano_numero_pai', 'Desenquadramento Origem:', '<span class="label label-warning">'.$row['dt_desenquadramento_cci_pai'].'</span>');
				echo form_default_row('', '','');
			}
			
			if(trim($row['ano_numero_filho']) != "")
			{
				echo form_default_row('ano_numero_filho', 'Desenquadramento Regularizado em:', '<span class="label label-info">'.$row['dt_desenquadramento_cci_filho'].'</span>');
				echo form_default_row('', '','');
			}		
		
			if(intval($row['cd_desenquadramento_cci']) > 0)
			{
				echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']);
				echo form_default_row('usuario_inclusao', 'Usuário Inclusão :', $row['usuario_inclusao']);
				
				if(trim($row['dt_enviado']) != '')
				{
					echo form_default_row('usuario_enviado', 'Usuário Confirmação :', $row['usuario_enviado']);
				}
			}
			
			echo form_default_hidden('cd_desenquadramento_cci', '', $row['cd_desenquadramento_cci']);
			echo form_default_hidden('cd_desenquadramento_cci_pai', '', intval($cd_desenquadramento_cci_pai));
			
			echo form_default_date('dt_desenquadramento_cci', 'Dt Cadastro :', $row);
			echo form_default_dropdown_db('cd_desenquadramento_cci_fundo', 'Fundo/Carteira :*', $config_fundo, $row['cd_desenquadramento_cci_fundo'], '', '', TRUE);		 
			echo form_default_dropdown_db('cd_desenquadramento_cci_administrador', 'Administrador :*', $config_administrador, $row['cd_desenquadramento_cci_administrador'], '', '', TRUE);		 
			echo form_default_dropdown_db('cd_desenquadramento_cci_gestor', 'Gestor :*', $config_gestor, $row['cd_desenquadramento_cci_gestor'], '', '', TRUE);		 
			echo form_default_textarea('regra', 'Regra :*', $row, 'style="height:120px;"');
			echo form_default_textarea('ds_desenquadramento_cci', 'Descrição do Desenquadramento :*', $row, 'style="height:120px;"');
			echo form_default_textarea('providencias_adotadas', 'Providências Adotadas :*', $row, 'style="height:120px;"');
			echo form_default_dropdown('fl_status', 'Status :*', $arr_status, array($row['fl_status']), 'onchange="regularizado();"');
			echo form_default_date('dt_regularizado', 'Dt Regularizado :*', $row);
			echo form_default_textarea('observacao', 'Observações :*', $row, 'style="height:120px;"');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
		
            if((trim($row['dt_encaminhado']) == '') OR ((trim($this->session->userdata('tipo')) == 'G') AND (trim($this->session->userdata('divisao')) == 'GC') AND (trim($row['dt_enviado']) == '')) OR ((trim($this->session->userdata('indic_01')) == 'S') AND (trim($this->session->userdata('divisao')) == 'GC') AND (trim($row['dt_enviado']) == '')))
            {
                echo button_save("Salvar");
            } 
			
			if((intval($row['cd_desenquadramento_cci']) > 0) AND (trim($row['dt_encaminhado']) == ''))
			{
				echo button_save("Encaminhar", 'encaminhar();', "botao_verde");
				echo button_save("Excluir", "excluir()", "botao_vermelho");
			}
			
			if((trim($row['dt_encaminhado']) != '') AND (trim($row['dt_enviado']) == '') AND (trim($this->session->userdata('divisao')) == 'GC') AND (((trim($this->session->userdata('tipo')) == 'G') OR (trim($this->session->userdata('indic_01')) == 'S'))))
			{
				echo button_save("Confirmar o Registro", 'confirmar();', "botao_verde");
				echo button_save("Devolver ", 'devolver();', "botao_vermelho");
			}
			
			if((intval($row['cd_desenquadramento_cci']) > 0) AND (trim($row['dt_encaminhado']) != '') AND (trim($row['fl_status']) != "R") AND (trim($row['ano_numero_filho']) == ""))
			{
				echo button_save("Regularizar", 'regularizar();', "botao_amarelo");
			}

			if(intval($row['cd_desenquadramento_cci']) > 0)
			{
				echo button_save("Imprimir (PDF)", 'imprimirPDF();', "botao_disabled");
			}			
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>