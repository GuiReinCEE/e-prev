<?php
set_title('N�o Conformidade - A��o Corretiva');
$this->load->view('header');
?>
<script>
    function salvar( form )
    {
        <?php
        if(intval($row['cd_acao']) == 0)
        {	
        ?>
            if($('#ac_proposta').val() == "")
            {
                alert('Informe a A��o Corretiva Proposta (A��o proposta para eliminar as causas da N�o Conformidade)');
                $('#ac_proposta').focus()
                return false;
            }

            if($('#dt_prop_imp').val() == "")
            {
                alert('Informe a Data proposta (a data m�mima de proposta � a data de hoje + 5 dias)');
                $('#dt_prop_imp').focus()
                return false;
            }					
				
            if($('#dt_prop_imp').val() != "")
            {
                var dt_prop_imp = Date.fromString($('#dt_prop_imp').val());
                dt_prop_imp.zeroTime();
                var dt_minima = new Date();
                dt_minima.addDays(+5);
                dt_minima.zeroTime();
					
                if(dt_prop_imp < dt_minima)
                {
                    alert('A data m�mima de proposta � a data de hoje + 5 dias ('+dt_minima.asString()+')');
                    $('#dt_prop_imp').focus()
                    return false;
                }
            }				
        <?
        }
        else
        {
        ?>
            if(($('#raz_nao_imp').val() != "") && ($('#dt_proposta_prorrogacao').val() == "") && ($('#dt_prorrogada').val() == ""))
            {
                alert("Informe a Nova Data Proposta");
                $('#dt_proposta_prorrogacao').focus()
                return false;
            }
				
            if(($('#dt_proposta_prorrogacao').val() != "") && ($('#raz_nao_imp').val() == ""))
            {
                alert("Informe a Raz�o da n�o implementa��o at� a data proposta");
                $('#raz_nao_imp').focus()
                return false;
            }	

        <?php
            if($row['dt_efe_imp'] == "")
            {
        ?>
                if($('#dt_efe_imp').val() != '')
                {
                    var dt_efe_imp = Date.fromString($('#dt_efe_imp').val());
                    dt_efe_imp.zeroTime();				
                    var dt_minima = new Date();
                    dt_minima.zeroTime();
							
                    if(dt_efe_imp > dt_minima)
                    {
                        alert('Data da efetiva implementa��o deve ser igual hoje ('+dt_minima.asString()+')');
                        $('#dt_efe_imp').focus()
                        return false;
                    }
						
                    if(dt_efe_imp < dt_minima)
                    {
                        alert('Data da efetiva implementa��o deve ser igual hoje ('+dt_minima.asString()+')');
                        $('#dt_efe_imp').focus()
                        return false;
                    }	
                        
                    if($('#dt_prop_verif').val() == '')
                    {
                        alert('Data da verifica��o efic�cia deve ser maior ou igual a '+$('#quinto_dia_util').val());
                        $('#dt_efe_imp').focus()
                        return false;   
                    }
                }
                    
                if(valida_dt_prazo() == false)
                {
                    alert("Data da verifica��o efic�cia deve ser maior ou igual a "+$('#quinto_dia_util').val() );
                    $("#dt_prazo_validacao").focus();

                    return false;
                }
                    
        <?php
            }
            if(!((intval($row['cd_acao']) == 0) or ($row['fl_prorroga'] != "S")))
            {
        ?>
				
            if($('#dt_proposta_prorrogacao').val() != "")
            {
                var dt_proposta_prorrogacao = Date.fromString($('#dt_proposta_prorrogacao').val());
                dt_proposta_prorrogacao.zeroTime();
                var dt_minima = new Date();
                dt_minima.addDays(+30);
                dt_minima.zeroTime();   
                                
                if(dt_proposta_prorrogacao < dt_minima)
                {
                    alert('A data m�mima de proposta � a data de hoje + 30 dias ('+dt_minima.asString()+')');
                    $('#dt_proposta_prorrogacao').focus()
                    return false;
                }
					
                var dt_prop_imp = Date.fromString($('#dt_prop_imp').val());	
                dt_prop_imp.zeroTime();
						
                if(dt_proposta_prorrogacao <= dt_prop_imp)
                {
                    alert('Nova Data Proposta deve ser maior que a data proposta.(' + $('#dt_prop_imp').val() + ')');
                    $('#dt_prorrogada').focus()
                    return false;
                }						
            }

        <?php
            }
            if($row['dt_prorrogada'] == "")
            {
        ?>				
                if($('#dt_prorrogada').val() != "")
                {
                    var dt_prorrogada = Date.fromString($('#dt_prorrogada').val());
                    dt_prorrogada.zeroTime();
                    var dt_minima = new Date();
                    dt_minima.zeroTime();
					
                    if(dt_prorrogada < dt_minima)
                    {
                        alert('Data de prorroga��o deve ser igual ou maior a data de hoje.');
                        $('#dt_prorrogada').focus()
                        return false;
                    }
					
                    var dt_prop_imp = Date.fromString($('#dt_prop_imp').val());	
                    dt_prop_imp.zeroTime();
						
                    if(dt_prorrogada <= dt_prop_imp)
                    {
                        alert('Data de prorroga��o deve ser maior que a data de hoje e maior que a data proposta.');
                        $('#dt_prorrogada').focus()
                        return false;
                    }					
                }
        <?php
            }
        ?>
						
            if($('#dt_efe_verif').val() != "")
            {
                var dt_efe_verif = Date.fromString($('#dt_efe_verif').val());
                dt_efe_verif.zeroTime();
						
                var dt_minima = new Date();
                dt_minima.zeroTime();
						
                if(dt_efe_verif > dt_minima)
                {
                    alert('Data da efetiva verifica��o deve ser igual hoje ('+dt_minima.asString()+')');
                    $('#dt_efe_verif').focus()
                    return false;
                }
					
                if(dt_efe_verif < dt_minima)
                {
                    alert('Data da efetiva verifica��o deve ser igual hoje ('+dt_minima.asString()+')');
                    $('#dt_efe_verif').focus()
                    return false;
                }					
            }				
        <?
        }
        ?>
		
        if(confirm('Salvar?'))
        {
                form.submit();
        }		
    }
    
    function valida_dt_prazo()
    {
        var s = $('#quinto_dia_util').val();
        var parts = s.split("/");
        var d = new Date(0);
        d.setFullYear(parts[2]);
        d.setDate(parts[0]);
        d.setMonth(parts[1] - 1);

        var quinto_dia_util = d.valueOf();

        var s = $('#dt_prop_verif').val();
        var parts = s.split("/");
        var d = new Date(0);
        d.setFullYear(parts[2]);
        d.setDate(parts[0]);
        d.setMonth(parts[1] - 1);

        var dt_prop_verif = d.valueOf();

        if(quinto_dia_util > dt_prop_verif)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
	
    function irLista()
    {
        location.href='<?php echo site_url("gestao/nc"); ?>';
    }
	
    function irNC(cd_nao_conformidade)
    {
        location.href='<?php echo site_url("gestao/nc/cadastro"); ?>' + "/" + cd_nao_conformidade;
    }	

    function irAcompanha(cd_nao_conformidade)
    {
        location.href='<?php echo site_url("gestao/nc/acompanha"); ?>' + "/" + cd_nao_conformidade;
    }	

    function imprimirNC(cd_nao_conformidade)
    {
        location.href='<?php echo site_url("gestao/nc/impressao"); ?>' + "/" + cd_nao_conformidade;
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('gestao/nc/anexo/'.$cd_nao_conformidade); ?>";
    }		
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_nc', 'N�o Conformidade', FALSE, "irNC('".$cd_nao_conformidade."');");
$abas[] = array('aba_ac', 'A��o Corretiva', TRUE, 'location.reload();');
$abas[] = array('aba_acompanha', 'Acompanhamento', FALSE, "irAcompanha('".$cd_nao_conformidade."');");
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	

echo aba_start( $abas );

    echo form_open('gestao/nc/acaoCorretivaSalvar');

    if($nc['fl_apresenta_ac'] == "S")
    {
        if(intval($row['cd_acao']) == 0)
        {
            echo '<div style="font-family: calibri, arial, verdana, tahoma; font-size: 20pt; font-weight: bold; color: red; width: 90%; text-align: center; ">Ainda n�o foi apresentada a A��o Corretiva.</div>';
        }

        $ac_readonly = 'readonly';
        
        if( (intval($row['cd_acao']) == 0) 
            OR 
            (
                (trim($row['fl_limite_apres']) == "S") 
                AND 
                (
                    (intval($this->session->userdata('codigo')) == intval($nc['cd_responsavel']))
                    OR
                    (intval($this->session->userdata('codigo')) == intval($nc['cd_substituto']))
                )
            ) 
        )
        {
            $ac_readonly = '';
        }
		
        echo form_start_box("default_box", "Apresenta��o");
            echo form_default_hidden('cd_acao', "C�digo:", $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('cd_nao_conformidade', "C�digo:", $cd_nao_conformidade, "style='width:100%;border: 0px;' readonly");
            echo form_default_hidden('dt_apres', "C�digo:", $row);
            echo form_default_hidden('dt_limite_apres', "C�digo:", $row);
			echo form_default_row('numero_cad_nc_label', "N�mero:", '<span class="label label-inverse">'.$nc["numero_cad_nc"].'</span>');
            echo form_default_text('ds_processo', "Processo:", $nc, "style='width:100%;border: 0px;' readonly" );		
            echo form_default_text('ds_responsavel', "Respons�vel:", $nc, "style='width:100%;border: 0px;' readonly" );		
			echo form_default_row('dt_limite_apres_label', "Data limite para apresenta��o:", '<span class="label label-warning">'.$row["dt_limite_apres"].'</span>');
			echo form_default_row('dt_apres_label', "Data da apresenta��o:", '<span class="label label-success">'.$row["dt_apres"].'</span>');
            
			echo form_default_row('ac_proposta_label', "A��o Corretiva Proposta:", '<i>A��es que visam, eliminar as causas da N�o conformidade a fim de prevenir sua repeti��o.</i>');
			echo form_default_textarea('ac_proposta', "", $row, "style='width:600px;height: 100px;' ".$ac_readonly);
			
        echo form_end_box("default_box");
		
        echo form_start_box("proposta_box", "Implementa��o");
            if((intval($row['cd_acao']) == 0) OR ($row['fl_limite_apres'] == "S") and ($row['fl_prorroga'] == "S"))
            {
                echo form_default_date('dt_prop_imp', "Data proposta:*", $row);
            }
            else
            {
				echo form_default_row('dt_prop_imp_label', "Data proposta:", '<span class="label label-important">'.$row["dt_prop_imp"].'</span>');
                echo form_default_hidden('dt_prop_imp', "", $row);
            }
			
            if($this->session->userdata('indic_12') == "*")
            {
                if(($row['dt_efe_imp'] == "") and ($row['dt_prorrogada'] == "") and (trim($row['raz_nao_imp']) != ""))
                {
                    echo form_default_date('dt_prorrogada', "Prorrogada at�:", $row);
                }
                else
                {
					echo form_default_row('dt_prorrogada_label', "Prorrogada at�:", '<span class="label label-success">'.$row["dt_prorrogada"].'</span>');
                    echo form_default_hidden('dt_prorrogada', "", $row);
                }			
            }
            else
            {
				echo form_default_row('dt_prorrogada_label', "Prorrogada at�:", '<span class="label label-success">'.$row["dt_prorrogada"].'</span>');
                echo form_default_hidden('dt_prorrogada', "", $row);
            }
            
            if($row['dt_prop_imp'] != "")
            {
                if(
                        (($row['dt_efe_imp'] == "") and 
                        ((intval($this->session->userdata('codigo')) == intval($nc['cd_responsavel'])) OR (intval($this->session->userdata('codigo')) == intval($nc['cd_substituto']))))

                  )
                {
                    echo form_default_date('dt_efe_imp', "Data da efetiva implementa��o:", $row);
                    echo form_default_date('dt_prop_verif', "Data da verifica��o Efic�cia:", $row);
                    echo form_default_hidden('quinto_dia_util', "", $row, "style='width:500px;border: 0px;' readonly" );
                }
                else
                {
					echo form_default_row('dt_efe_imp_label', "Data da efetiva implementa��o:", '<span class="label label-info">'.$row["dt_efe_imp"].'</span>');
                    echo form_default_hidden('dt_efe_imp', "", $row);
                    echo form_default_text('dt_prop_verif', "Data da verifica��o Efic�cia:", $row, "style=' border: 0px;' readonly" );
                    echo form_default_hidden('quinto_dia_util', "", $row, "style='width:500px;border: 0px;' readonly" );
                }
            }		
        echo form_end_box("proposta_box");	
        echo form_start_box("prorrogacao_box", "Prorroga��o");
            echo form_default_textarea('raz_nao_imp', "Raz�o da n�o implementa��o at� a data proposta:", $row, "style='width:500px; height: 100px;' ".((intval($row['cd_acao']) == 0) ? "readonly" : ($row['fl_prorroga'] != "S" ? "readonly" : ""))."");
			
            if((intval($row['cd_acao']) == 0) or ($row['fl_prorroga'] != "S"))
            {
                echo form_default_text('dt_proposta_prorrogacao', "Nova Data Proposta:", $row, "style='border: 0px;' readonly" );
            }
            else
            {
                echo form_default_date('dt_proposta_prorrogacao', "Nova Data Proposta:", $row);
            }	
        echo form_end_box("prorrogacao_box");	
		
        echo form_start_box("eficacia_box", "Valida��o da Efic�cia");
			
            if(($this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26))
            {
                if(($row['dt_efe_imp'] != "") and ($row['dt_efe_verif'] == ""))
                {
                    echo form_default_date('dt_prorrogacao_verificacao_eficacia', "Prorroga��o Data da verifica��o Efic�cia:", $row);  
                    echo form_default_date('dt_efe_verif', "Data valida��o efic�cia:", $row);  
                }
                else
                {
					echo form_default_row('dt_prorrogacao_verificacao_eficacia', "Prorroga��o Data da verifica��o Efic�cia:", '<span class="label label-warning">'.$row["dt_prorrogacao_verificacao_eficacia"].'</span>');
					echo form_default_row('dt_efe_verif_label', "Data valida��o efic�cia:", '<span class="label label-info">'.$row["dt_efe_verif"].'</span>');
                    echo form_default_hidden('dt_efe_verif', "", $row);
                }			
            }
            else
            {
				echo form_default_row('dt_prorrogacao_verificacao_eficacia', "Prorroga��o Data da verifica��o Efic�cia:", '<span class="label label-warning">'.$row["dt_prorrogacao_verificacao_eficacia"].'</span>');
                echo form_default_text('dt_efe_verif', "Data valida��o efic�cia:", $row, "style='color: blue; font-weight: bold; border: 0px;' readonly" );
            }
			
        echo form_end_box("eficacia_box");		
        echo form_command_bar_detail_start();
        if(($this->session->userdata('indic_12') == "*" OR $this->session->userdata('codigo') == 26) and (intval($row['cd_acao']) > 0) and ($row['dt_efe_verif'] == ""))
        {
            echo button_save("Salvar");
        }
        else
        {
            if(!((((intval($nc['cd_responsavel']) > 0) and (intval($this->session->userdata('codigo')) != intval($nc['cd_responsavel'])))) AND ((intval($nc['cd_substituto']) > 0) and (intval($this->session->userdata('codigo')) != intval($nc['cd_substituto'])))) and ($row['dt_efe_imp'] == ""))
            {
                echo button_save("Salvar");
            }		
        }
        echo button_save("Imprimir","imprimirNC(".$cd_nao_conformidade.")","botao_disabled");
        echo form_command_bar_detail_end();
    }
    else
    {
        echo '<div style="font-family: calibri, arial, verdana, tahoma; font-size: 20pt; font-weight: bold; color: red; width: 90%; text-align: center; ">Voc� deve informar a Disposi��o e Causa da N�o Conformidade.</div>';
    }
	
    echo form_close();
    echo br(10);	
	
echo aba_end();
	
$this->load->view('footer_interna');
?>