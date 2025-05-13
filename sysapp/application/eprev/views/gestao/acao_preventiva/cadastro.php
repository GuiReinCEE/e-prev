<?php
set_title('Ação Preventiva');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(Array('usuario', 'cd_substituto', 'usuario_gerencia','processo','potencial_nc', 'causa_nc', 'fonte_info', 'acao_proposta', 'dt_proposta'), 'valida_dt_prazo(form)');
?>
    function valida_dt_prazo(form)
    {
        if($('#dt_implementacao').val() != '')
        {
            if($('#dt_prazo_validacao').val() == '')
            {
                alert( "Data prazo validação eficácia deve ser maior ou igual a "+$('#quinto_dia_util').val() );
                $("#dt_prazo_validacao").focus();
                return false;
            }
        }
        
        if($('#dt_prazo_validacao').val() != '')
        {
            if($('#dt_implementacao').val() == '')
            {
                alert( "Data de implementação não pode ser nulo " );
                $("#dt_implementacao").focus();
                return false;
            }
        }
		
		var s = $('#dt_proposta').val();
		var parts = s.split("/");
		var d = new Date(0);
		d.setFullYear(parts[2]);
		d.setDate(parts[0]);
		d.setMonth(parts[1] - 1);
		d.zeroTime();
		var dt_proposta = d.valueOf();
		
		var dt_proposta_minima = new Date();
		dt_proposta_minima.addDays(+3);
		dt_proposta_minima.zeroTime();

		if (($('#cd_acao_preventiva').val() == 0) && (dt_proposta < dt_proposta_minima))
		{
			alert("Data proposta deve ser maior ou igual a data de hoje mais 3 dias." );
			$("#dt_implementacao").focus();
			return false;
		}
		
		if($('#cd_acao_preventiva').val() > 0 && $('#dt_implementacao').val() != '')
        {
			var s = $('#dt_implementacao').val();
			var parts = s.split("/");
			var d = new Date(0);
			d.setFullYear(parts[2]);
			d.setDate(parts[0]);
			d.setMonth(parts[1] - 1);
			d.zeroTime();
			var dt_implementa = d.valueOf();
			
			var dt_hoje = new Date();
			dt_hoje.zeroTime();
			dt_hoje = dt_hoje.valueOf();
			
			if(dt_implementa != dt_hoje)
			{
				alert("Data da implemenentação deve ser igual a data de hoje." );
                $("#dt_implementacao").focus();
                return false;
			}
		}
		
        if($('#cd_acao_preventiva').val() > 0 && $('#dt_prazo_validacao').val() != '' && $('#dt_implementacao').val() != '')
        {
            var s = $('#quinto_dia_util').val();
            var parts = s.split("/");
            var d = new Date(0);
            d.setFullYear(parts[2]);
            d.setDate(parts[0]);
            d.setMonth(parts[1] - 1);
            d.zeroTime();
            var quinto_dia_util = d.valueOf();
            
            var s = $('#dt_prazo_validacao').val();
            var parts = s.split("/");
            var d = new Date(0);
            d.setFullYear(parts[2]);
            d.setDate(parts[0]);
            d.setMonth(parts[1] - 1);
            d.zeroTime();
            var dt_prazo_validacao = d.valueOf();
            
            if(quinto_dia_util <= dt_prazo_validacao)
            {
                if( confirm('Salvar?') )
				{
					form.submit();
				}
            }
            else
            {
                alert( "Data prazo validação eficácia deve ser maior ou igual a "+$('#quinto_dia_util').val() );
                $("#dt_prazo_validacao").focus();
                return false;
            }
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
            
    }
    
    function ir_lista()
	{
		location.href='<?php echo site_url("gestao/acao_preventiva"); ?>';
	}

    function ir_acompanhamento(nr_ano, nr_ap)
	{
		location.href='<?php echo site_url("gestao/acao_preventiva/acompanhamento/"); ?>' + "/" + nr_ano + "/" + nr_ap;
	}

    function ir_prorrogacao(nr_ano, nr_ap)
	{
		location.href='<?php echo site_url("gestao/acao_preventiva/prorrogacao/"); ?>' + "/" + nr_ano + "/" + nr_ap;
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/acao_preventiva/anexo/'.$row['nr_ano'].'/'.$row['nr_ap']); ?>";
	}

    function validar(cd_acao_preventiva, numero_cad_ap)
    {
        if(confirm("ATENÇÃO\n\nDeseja validar?\n\n"))
		{
			location.href='<?php echo site_url("gestao/acao_preventiva/validar/"); ?>' + "/" + cd_acao_preventiva + "/" + numero_cad_ap;
		}
    }

    function cancelar(cd_acao_preventiva, numero_cad_ap)
    {
        if(confirm("ATENÇÃO\n\nDeseja cancelar?\n\n"))
		{
			location.href='<?php echo site_url("gestao/acao_preventiva/cancelar/"); ?>' + "/" + cd_acao_preventiva + "/" + numero_cad_ap;
		}
    }

    function gerar_pdf()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?php echo site_url("/gestao/acao_preventiva/gerar_pdf"); ?>';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}
	
	function prorrogar_validacao()
	{
		if(confirm("ATENÇÃO\n\nDeseja salvar a Dt Prorroga Validação?\n\n"))
		{
			if($('#dt_prazo_validacao_prorroga').val() != '')
			{
				$.post('<?php echo site_url("gestao/acao_preventiva/prorrogar_validacao") ?>',
				{
					cd_acao_preventiva          : $('#cd_acao_preventiva').val(),
					dt_prazo_validacao_prorroga : $('#dt_prazo_validacao_prorroga').val()
				},
				function(data)
				{
					location.reload();
				});
			}
			else
			{
				alert('Informe a Dt Prorroga Validação Eficácia.')
			}
		}
	}
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Ação Preventiva', TRUE, 'location.reload();');

if($row['nr_ano'] > 0 AND $row['nr_ap'] > 0)
{
    $abas[] = array('aba_lista', 'Acompanhamento', FALSE, "ir_acompanhamento('".$row['nr_ano']."', '".$row['nr_ap']."');");
    $abas[] = array('aba_lista', 'Prorrogação', FALSE, "ir_prorrogacao('".$row['nr_ano']."', '".$row['nr_ap']."');");
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
}

$readonly = false;
$disabled = '';

if($row['dt_validacao'] == '' AND ($row['dt_implementacao'] != '' AND ($this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26)))
{
    $readonly = true;
    $disabled = ' disabled';
}

echo aba_start( $abas );
    echo form_open('gestao/acao_preventiva/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_acao_preventiva', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_hidden('nr_ano', "nr_ano", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_hidden('nr_ap', "nr_ap:", $row, "style='width:100%;border: 0px;' readonly" );

			if($row['dt_cancelado'] != '')
			{
				echo form_default_text('dt_cancelado', "Dt Cancelado:", $row, "style='font-weight: bold; width:100%;border: 0px; color:red;' readonly" );
				echo form_default_text('usuario_cancelado', "Cancelado por:", $row, "style='font-weight: bold; width:500px;border: 0px; color:red;' readonly" );
			}

			if($row['cd_acao_preventiva'] > 0)
			{
				echo form_default_text('numero_cad_ap', "Número:", $row, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
				echo form_default_text('dt_inclusao', "Dt Cadastro:", $row, "style='width:100%;border: 0px;' readonly" );
				echo form_default_text('usuario_cadastro', "Cadastro por:", $row, "style='width:100%;border: 0px;' readonly" );
			}

			#echo form_default_dropdown('processo', 'Processo:*', $processo_dd, array($row['cd_processo']), $disabled);
			echo form_default_processo('processo', 'Processo:*', $row['cd_processo'], $disabled);

			if($row['cd_acao_preventiva'] > 0)
			{
				echo form_default_text('gerencia', "Gerência:", $row, "style=' width:500px;border: 0px;' readonly" );
				echo form_default_text('nome_usuario', "Responsável:", $row, "style='width:500px;border: 0px;' readonly" );
				echo form_default_hidden('usuario', "Usuário:", $row, "style='width:500px;border: 0px;' readonly" );
				echo form_default_text('nome_substituto', "Substituto:", $row, "style='width:500px;border: 0px;' readonly" );
			}
			else
			{
				echo filter_usuario_ajax('usuario','','','Responsável:*','Gerência:*');
				echo filter_usuario_ajax('cd_substituto', '', '', 'Substituto:*','Gerência:*');
			}

			echo form_default_textarea('potencial_nc', "Potencial Não Conformidade (Risco):*", $row, "style='width:500px;'". $disabled);
			echo form_default_textarea('causa_nc', "Causas Potencial Não Conformidade:*", $row, "style='width:500px;'". $disabled);
			echo form_default_textarea('fonte_info', "Fonte de Informação:*", $row, "style='width:500px;'". $disabled);
			echo form_default_textarea('acao_proposta', "Ação Preventiva Proposta:*", $row, "style='width:500px;'". $disabled);
			echo form_default_date('dt_proposta', "Dt Proposta:*", $row, '', $readonly);

			if($row['cd_acao_preventiva'] > 0)
			{
				echo form_default_date('dt_implementacao', "Dt Implementação:", $row, '', $readonly);
				echo form_default_hidden('quinto_dia_util', "", $row, "style='width:500px;border: 0px;' readonly" );
				echo form_default_date('dt_prazo_validacao', "Dt Validação Eficácia:", $row, '', $readonly );   
				echo form_default_text('dt_prorrogacao', "Dt Prorrogação:", $row, "style='font-weight: bold; width:100%;border: 0px;' readonly" );
				
				if(trim($row['dt_implementacao']))
				{
					if(($this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26))
					{
						echo form_default_date('dt_prazo_validacao_prorroga', "Dt Prorroga Validação Eficácia:", $row);
					}
					else
					{
						echo form_default_date('dt_prazo_validacao_prorroga', "Dt Prorroga Validação Eficácia:", $row,  '', "disabled");
					}
				}
			}

		echo form_end_box("default_box");

		if($row['dt_implementacao'] != '')
		{

			echo form_start_box( "default_box", "Validação Comitê Qualidade" );
				echo form_default_text('dt_validacao', "Data:", $row, "style='width:500px;border: 0px;' readonly" );
				echo form_default_text('validado', "Por:", $row, "style='width:500px;border: 0px;' readonly" );
			echo form_end_box("default_box");

		}

		echo form_command_bar_detail_start();
		
			if($row['dt_cancelado'] == '' AND $row['dt_validacao'] == '' AND (intval($row['cd_acao_preventiva']) == 0 OR ( (intval($row['cd_responsavel']) == intval($this->session->userdata('codigo')) OR intval($row['cd_substituto']) == intval($this->session->userdata('codigo'))) AND $row['dt_implementacao'] == '')))
			{
			   echo button_save("Salvar");
			}

			if($row['dt_validacao'] == '' AND ($row['dt_implementacao'] != '' AND ($this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26)))
			{
				echo button_save("Prorrogar Validação", "prorrogar_validacao();", "botao_verde");
				echo button_save("Validar","validar(\"".$row['cd_acao_preventiva']."\", \"".$row['numero_cad_ap']."\")");
			}

			if($row['cd_acao_preventiva'] > 0 AND $row['dt_implementacao'] == '' AND $row['dt_cancelado'] == '' AND (intval($row['cd_responsavel']) == intval($this->session->userdata('codigo')) OR $this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26 OR intval($row['cd_substituto']) == intval($this->session->userdata('codigo'))) )   
			{
				echo button_save("Cancelar", "cancelar(\"".$row['cd_acao_preventiva']."\", \"".$row['numero_cad_ap']."\")", "botao_vermelho");
			}

			if($row['cd_acao_preventiva'] > 0)
			{
				echo button_save("Imprimir","gerar_pdf()","botao_disabled");
			}

        echo form_command_bar_detail_end();

	echo form_close();
	echo br();	

echo aba_end();

$this->load->view('footer_interna');
?>