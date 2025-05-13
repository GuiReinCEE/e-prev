<?php
set_title('Reclamação Análise - Responder');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(Array('fl_acao', 'ds_retorno'), 'valida_form(form)'); ?>    
	
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/reclamacao_responder"); ?>';
    }
    
    function ir_cadastro()
    {
        location.href='<?php echo site_url("ecrm/reclamacao_responder/cadastro/".$row['cd_reclamacao_analise']); ?>';
    }
    
    function valida_form(form)
    {
        var $fl_acao = $('#fl_acao').val();
        
        if($fl_acao == 'sap')
        {
            if($('#nr_ano_sap').val() == '' || $('#nr_sap').val() == '')
            {
                alert('Informe o ano e o número da SAP.');
                return false;
            }
            else
            {
                $.post('<?php echo site_url('ecrm/reclamacao_responder/salvar_sap');?>',
                {
                    cd_reclamacao_analise_item : $('#cd_reclamacao_analise_item').val(),
                    nr_ano_sap                 : $('#nr_ano_sap').val(),
                    nr_sap                     : $('#nr_sap').val(),
                    ds_retorno                 : $('#ds_retorno').val()
                },
                function(data)
                {
                    if(data > 0)
                    {
                        ir_cadastro();
                    }
                    else
                    {
                        alert('Ação Preventiva não existe');
                    }
                });
                
                return false;
            }
        }
        else if($fl_acao == 'nc')
        {
            if($('#nr_ano_nc').val() == '' || $('#nr_nc').val() == '')
            {
                alert('Informe o ano e o número da NC.');
                return false;
            }
            else
            {
                $.post('<?php echo site_url('ecrm/reclamacao_responder/salvar_nc');?>',
                {
                    cd_reclamacao_analise_item : $('#cd_reclamacao_analise_item').val(),
                    nr_ano_nc                  : $('#nr_ano_nc').val(),
                    nr_nc                      : $('#nr_nc').val(),
                    ds_retorno                 : $('#ds_retorno').val()
                },
                function(data)
                {
                    if(data > 0)
                    {
                        ir_cadastro();
                    }
                    else
                    {
                        alert('NC não existe');
                    }
                });
                
                return false;
            }
        }
        else
        {
            $.post('<?php echo site_url('ecrm/reclamacao_responder/salvar_retorno');?>',
            {
                cd_reclamacao_analise_item : $('#cd_reclamacao_analise_item').val(),
                ds_retorno                 : $('#ds_retorno').val()
            },
            function(data)
            {
                ir_cadastro();
            });

            return false;
        }
    }
    
    $(function(){
        $('#fl_acao').change(function(){
            if($(this).val() != '')
            {
                if($(this).val() == 'sap')
                {
                    $('#nr_ano_sap_nr_sap_row').show();
                    $('#nr_ano_nc_nr_nc_row').hide();
                }
                else if($(this).val() == 'nc')
                {
                    $('#nr_ano_nc_nr_nc_row').show();
                    $('#nr_ano_sap_nr_sap_row').hide();
                }
            }
            else
            {
                $('#nr_ano_sap_nr_sap_row').hide();
                $('#nr_ano_nc_nr_nc_row').hide();
            }
       });
       
        $('#fl_acao').change();
    });

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Parecer', TRUE, 'location.reload();');
	
$arr[] = array('text' => 'Sem Ação', 'value' => 'sem');
$arr[] = array('text' => 'Ação Preventiva', 'value' => 'sap');
$arr[] = array('text' => 'Não Conformidade', 'value' => 'nc');

echo aba_start( $abas );
    echo form_open('ecrm/reclamacao_responder/salvar_parecer');
        echo form_start_box( "default_box", "Parecer" );
            echo form_default_hidden('cd_reclamacao_analise', '', $row);
            echo form_default_hidden('cd_reclamacao_analise_item', '', $row);
            echo form_default_row('cd_reclamacao', 'Número :', $row['cd_reclamacao']);
            echo form_default_row('re', 'RE :', $row["cd_empresa"]."/".$row["cd_registro_empregado"]."/".$row["seq_dependencia"]);
		    echo form_default_row('nome', 'Nome :', $row['nome']);
            echo form_default_row('descricao', 'Descrição :', nl2br($row['descricao']));
            echo form_default_dropdown('fl_acao', 'Ação :*', $arr, $row['fl_acao']);
            echo form_default_integer_ano('nr_ano_nc', 'nr_nc', "NC : (ano/número)", $row['nr_ano_nc'], $row['nr_nc']);
            echo form_default_integer_ano('nr_ano_sap', 'nr_sap', "SAP : (ano/número)", $row['nr_ano_sap'], $row['nr_sap']);
            echo form_default_textarea('ds_retorno', 'Parecer da Gerência :*', $row, "style='width:500px; height: 80px;'");
          echo form_end_box("default_box");
        echo form_command_bar_detail_start();     
			echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
	echo '<div id="result_div"></div>';
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>