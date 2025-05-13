<?php
set_title('Plano Fiscal - Indicadores PGA - Resposta');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('fl_status', 'resultado'));
?>
    $(function(){
        $('#display_none').hide();
    })  
    
    function confirmar()
    {
        var confirmacao = 'Deseja confirmar?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
			filter_bar_form.action = '<?php echo site_url('gestao/plano_fiscal_indicador/confirmar'); ?>';
			filter_bar_form.submit();
        }
    }
    
    function encaminhar()
    {
        var confirmacao = 'Deseja encaminhar para o responsavél?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("gestao/plano_fiscal_indicador/encaminhar/".$row['cd_plano_fiscal_indicador_item']); ?>';
        }
    }
	
	function ir_lista()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_indicador/minhas"); ?>';
    }
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Resposta', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('gestao/plano_fiscal_indicador/salvar_resposta', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Resposta" );
            echo form_default_text('nr_ano_mes', 'Ano/Mês:', $row['nr_ano_mes'], "style='width:100%;border: 0px; font-weight:bold;' readonly");
            echo form_default_hidden('cd_plano_fiscal_indicador_item','', $row);
            echo form_default_text('nr_item', 'Número:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_textarea('descricao', 'Indicador/Método de Cálculo', $row, " style='border: 1px solid gray;' readonly");
			echo form_default_textarea('retorno', 'Retorno: ', $row, " style='border: 1px solid gray;' readonly");
            echo form_default_hidden('peso', 'Peso:', number_format($row["peso"], $row['qt_decimal'], ',', '.'), "style='width:100%;border: 0px;' readonly");
            echo form_default_text('meta', 'Meta:', $row["meta"] . ' ' .$row['unidade'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('ds_plano_fiscal_indicador_area', 'Gerência:', $row['ds_plano_fiscal_indicador_area'], "style='width:100%;border: 0px;' readonly");
            echo form_default_text('nome', 'Respondente:', $row['responsavel'], "style='width:100%;border: 0px;' readonly");
            echo form_default_row('dt_limite', 'Dt Limite:', '<span class="label label-important">'.$row['dt_limite'].'</span>');
            echo form_default_dropdown('fl_status', 'Status: *', $arr_status, array($row['fl_status']));
            echo form_default_textarea('resultado', 'Resultado: *', $row);
			
			if(trim($row['dt_confirmacao']) != "")
            {
                echo form_default_text('dt_confirmacao', 'Confirmação:', $row, "style='width:100%;border: 0px; font-weight:bold;' readonly");
            }
            
            if((trim($row['fl_dt_limite']) == 'S') AND (trim($row['dt_confirmacao']) == ""))
            {
                echo form_default_row('info', '', '<span class="label label-important">Para responder entre contato com a GC</span>');
            }

            if(
                (
                    ($this->session->userdata('codigo') == $row['cd_gerente']) #RESPONSAVEL
                    OR (($this->session->userdata('tipo') == "G") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE
                    OR (($this->session->userdata('indic_01') == "S") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE SUBSTITUTO      
                )
                AND 
                (trim($this->session->userdata('assinatura')) == "")  
                AND 
                (trim($row["dt_resposta"]) != '')
              ) 
            {
                echo form_default_row('info', '', '<span class="label label-important">Você não possui assinatura digital, para responder entre contato com a GC</span>');
            }

        echo form_end_box("default_box");
  
        if(trim($row['dt_encerra']) == '')
        {
        echo form_command_bar_detail_start((trim($row['dt_confirmacao']) == "" ? '' : 'display_none'));    
            if(
				(($this->session->userdata('codigo') == $row['cd_responsavel']) AND (trim($row['dt_encaminhamento']) == '')) #RESPONDENTE
				OR ($this->session->userdata('codigo') == $row['cd_gerente']) #RESPONSAVEL
			    OR (($this->session->userdata('tipo') == "G") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE
			    OR (($this->session->userdata('indic_01') == "S") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE SUBSTITUTO				
			  )
            {
                if(trim($row['fl_dt_limite']) == "N")
				{
					echo button_save("Salvar");
				}
            }
            
			if(($row['dt_resposta'] != '') AND (trim($row['fl_dt_limite']) == "N"))
            {
                if((
					($this->session->userdata('codigo') == $row['cd_gerente']) #RESPONSAVEL
					OR (($this->session->userdata('tipo') == "G") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE
					OR (($this->session->userdata('indic_01') == "S") AND ($row['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE SUBSTITUTO		
				  ) AND (trim($row['dt_encaminhamento']) != "")  AND (trim($this->session->userdata('assinatura')) != ""))
				{
					echo button_save("Assinar", 'confirmar();', 'botao_vermelho');
				}
				elseif((trim($row['dt_encaminhamento']) == "") AND (trim($row['dt_confirmacao']) == "") AND ($this->session->userdata('codigo') == $row['cd_responsavel']))
				{
					echo button_save("Encaminhar", 'encaminhar();', 'botao_vermelho');
				}
            }
        }
            
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>