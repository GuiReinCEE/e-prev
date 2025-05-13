<?php
    set_title('FAX Recebido');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        listar();
    }
	
    function listar()
    {
        if(($("#dt_ini").val() != "") && ($("#dt_fim").val() != ""))
        {
            $("#result_div").html("<?php echo loader_html(); ?>");
            $.post('<?php echo base_url().index_page(); ?>/ecrm/fax_recebido/listar',{
                dt_ini  : $("#dt_ini").val(),
                dt_fim  : $("#dt_fim").val(),
                destino : $("#destino").val()
            },
            function(data)
            {
                    $("#result_div").html(data);
                    table_result();
            });		
        }
        else
        {
            alert("Informe o período de recebimento");
            $("#dt_ini").focus();
        }
    }
	
	function table_result()
	{
            var ob_resul = new SortableTable(document.getElementById("tabela_fax"),
            [
                null, 
                'DateTimeBR',
                'CaseInsensitiveString', 
                'CaseInsensitiveString', 
                'CaseInsensitiveString', 
                'CaseInsensitiveString', 
                'Number',
                'CaseInsensitiveString',  
                'CaseInsensitiveString',  
                'CaseInsensitiveString'
            ]);
            ob_resul.onsort = function ()
            {
                var rows = ob_resul.tBody.rows;
                var l = rows.length;
                for (var i = 0; i < l; i++)
                {
                    removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                    addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
                }
            };
            ob_resul.sort(1, true);
	}	
	
	function checkAll()
	{
            var ipts = $("#tabela_fax>tbody").find("input:checkbox");
            var check = document.getElementById("checkboxCheckAll");

            check.checked ?
            jQuery.each(ipts, function(){
                    this.checked = true;
            }) :
            jQuery.each(ipts, function(){
                    this.checked = false;
            });
	}	
	
	function getCheckDigitalizacao()
	{
            var ipts = $("#tabela_fax>tbody").find("input:checkbox:checked");

            $("#arq_selecionado").val("");
            $("#doc_selecionado").val("");
            $("#part_selecionado").val("");
            $("#fl_gerar").val("N");
            $("#fl_gerar_digitalizacao").val("S");
		
            jQuery.each(ipts, function(){
                //alert(this.name + " => " + this.value);
                if(jQuery.trim($("#arq_selecionado").val()) == "")
                {
                    $("#arq_selecionado").val(this.value);
                }
                else
                {
                    $("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
                }

                var cd_codigo = $("#" + this.value + "_id_codigo").val();
                if(cd_codigo != "")
                {
                    if(jQuery.trim($("#doc_selecionado").val()) == "")
                    {
                        $("#doc_selecionado").val(cd_codigo);
                    }
                    else
                    {
                        $("#doc_selecionado").val($("#doc_selecionado").val() + "," + cd_codigo);
                    }	
                }
                else
                {
                    alert("Informe o Documento");
                    $("#" + this.value + "_id_codigo").focus();
                    $("#fl_gerar_digitalizacao").val("N");
                    return false;
                }

                var cd_empresa            = $("#" + this.value + "_cd_empresa").val();
                var cd_registro_empregado = $("#" + this.value + "_cd_registro_empregado").val();
                var seq_dependencia       = $("#" + this.value + "_seq_dependencia").val();
                var nome_participante     = $("#" + this.value + "_nome_participante").val();
                if((cd_empresa != "") && (cd_registro_empregado != "") && (seq_dependencia != "") && (nome_participante != ""))
                {
                    if(jQuery.trim($("#part_selecionado").val()) == "")
                    {
                        $("#part_selecionado").val(cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
                    }
                    else
                    {
                        $("#part_selecionado").val($("#part_selecionado").val() + "," + cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
                    }	
                }
                else
                {
                    alert("Informe o Participante (Emp/RE/Seq)");
                    $("#" + this.value + "_nome_participante").focus();
                    $("#fl_gerar_digitalizacao").val("N");
                    return false;
                }			
            });
	}
	
	function protocoloDigitalizacao()
	{
            getCheckDigitalizacao();
            if((jQuery.trim($("#arq_selecionado").val()) != "") && (jQuery.trim($("#doc_selecionado").val()) != "") && (jQuery.trim($("#part_selecionado").val()) != ""))
            {		
                if(confirm("Deseja gerar o Protocolo Digitalização (Digital)?"))
                {
                    if($("#fl_gerar_digitalizacao").val() == "S")
                    {
                        document.getElementById('filter_bar_form').action = "fax_recebido/protocoloDigitalizacao/";
                        document.getElementById('filter_bar_form').method = "post";
                        document.getElementById('filter_bar_form').target = "_self";
                        $("#filter_bar_form").submit();		
                    }
                }
            }
            else
            {
                alert("Selecione pelo menos um arquivo");
            }
	}	


	function getCheckInterno()
	{
            var ipts = $("#tabela_fax>tbody").find("input:checkbox:checked");

            $("#arq_selecionado").val("");
            $("#doc_selecionado").val("");
            $("#part_selecionado").val("");
            $("#fl_gerar").val("S");
            $("#fl_gerar_digitalizacao").val("N");
		
            jQuery.each(ipts, function(){
                //alert(this.name + " => " + this.value);
                if(jQuery.trim($("#arq_selecionado").val()) == "")
                {
                    $("#arq_selecionado").val(this.value);
                }
                else
                {
                    $("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
                }
			
                var cd_codigo = $("#" + this.value + "_id_codigo").val();
                if(cd_codigo != "")
                {
                    if(jQuery.trim($("#doc_selecionado").val()) == "")
                    {
                        $("#doc_selecionado").val(cd_codigo);
                    }
                    else
                    {
                        $("#doc_selecionado").val($("#doc_selecionado").val() + "," + cd_codigo);
                    }	
                }
                else
                {
                    alert("Informe o Documento");
                    $("#" + this.value + "_id_codigo").focus();
                    $("#fl_gerar").val("N");
                    return false;
                }
			
                var cd_empresa            = $("#" + this.value + "_cd_empresa").val();
                var cd_registro_empregado = $("#" + this.value + "_cd_registro_empregado").val();
                var seq_dependencia       = $("#" + this.value + "_seq_dependencia").val();
                var nome_participante     = $("#" + this.value + "_nome_participante").val();
                if(nome_participante != "")
                {
                    if(jQuery.trim($("#part_selecionado").val()) == "")
                    {
                        $("#part_selecionado").val(cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
                    }
                    else
                    {
                        $("#part_selecionado").val($("#part_selecionado").val() + "," + cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
                    }	
                }
                else
                {
                    alert("Informe o Participante (Emp/RE/Seq) ou Nome");
                    $("#" + this.value + "_nome_participante").focus();
                    $("#fl_gerar").val("N");
                    return false;
                }			
            });

            return true;
	}	
	
	function protocoloInterno()
	{
            getCheckInterno();
            if((jQuery.trim($("#arq_selecionado").val()) != "") && (jQuery.trim($("#doc_selecionado").val()) != "") && (jQuery.trim($("#part_selecionado").val()) != ""))
            {		
                if(confirm("Deseja gerar o Protocolo Interno?"))
                {
                    if($("#fl_gerar").val() == "S")
                    {
                        document.getElementById('filter_bar_form').action = "fax_recebido/protocoloInterno/";
                        document.getElementById('filter_bar_form').method = "post";
                        document.getElementById('filter_bar_form').target = "_self";
                        $("#filter_bar_form").submit();	
                    }
                }
            }
            else
            {
                alert("Selecione pelo menos um arquivo");
            }	
	}	
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    echo aba_start( $abas );

    echo form_list_command_bar();	
    echo form_start_box_filter('filter_bar', 'Filtros');
        echo filter_date_interval('dt_ini', 'dt_fim', 'Dt Recebido: (*)', calcular_data('', '1 month'), date('d/m/Y'));
		echo filter_dropdown('destino', 'Destino:', $ar_destino);	
		
        echo form_hidden('fl_gerar',"Gerar:");
        echo form_hidden('fl_gerar_digitalizacao',"Gerar Dig.:");
        echo form_hidden('arq_selecionado',"Arq Sel:");
        echo form_hidden('doc_selecionado',"Doc Sel:");
        echo form_hidden('part_selecionado',"Part Sel:");		
    echo form_end_box_filter();	

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<br>
<br>
<script>
    $(document).ready(function() {
            filtrar();
    });
</script>
<?php
    echo aba_end(''); 
    $this->load->view('footer');
?>