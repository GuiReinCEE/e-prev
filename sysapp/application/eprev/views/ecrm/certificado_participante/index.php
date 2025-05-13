<?php
set_title('Certificado Participante');
$this->load->view('header');
?>
<style>
	#protocolo_box_content, #botoes_box_content, #order_box_content{
		text-align:left;
	}
</style>
<script>
    function filtrar()
    {
        $("#part_selecionado").val("");
        listaCertificado();
    }
	
    function listaCertificado()
    {
        if(
        	(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != ""))
        	||
        	(($("#dt_inicial").val() != "") && ($("#dt_final").val() != ""))
            ||
            ($("#cd_empresa_part").val() != "") && ($("#cd_registro_empregado_part").val() != "") && ($("#seq_dependencia_part").val() != "")
    	)
        {
            $("#result_div").html("<?php echo loader_html(); ?>");

            $.post('<?php echo base_url() . index_page(); ?>/ecrm/certificado_participante/certificadoLista',
            {
                cd_empresa : $("#cd_plano_empresa").val(),
                cd_plano   : $("#cd_plano").val(),
                dt_inicial : $("#dt_inicial").val(),
                dt_final   : $("#dt_final").val(),
                cd_empresa_part            : $("#cd_empresa_part").val(),
                cd_registro_empregado_part : $("#cd_registro_empregado_part").val(),
                seq_dependencia_part       : $("#seq_dependencia_part").val()
            },
            function(data)
            {
                $("#result_div").html(data);
                table_result();
            }
        );
        }
        else
        {
            alert("INFORME OS CAMPOS:\n\nEmpresa, Plano \n\nOU\n\nDt Ingresso\n\nOU\n\nParticipante (Emp/RE/Seq)");
            $("#cd_plano_empresa").focus();
        }
    }
	
    function table_result()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_certificado"),
        [
            null,
            'RE',  
            'CaseInsensitiveString',  
            'DateBR'
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
        ob_resul.sort(2, false);
    }	

    function protocolo_result()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_protocolo"),
        [
            null,
            'RE',  
            'CaseInsensitiveString',  
            'Number',
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
        ob_resul.sort(2, false);
    }	
	
    function checkAll()
    {
        var ipts = $("#tabela_certificado>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }	
	
    function getCheck()
    {
        var ipts = $("#tabela_certificado>tbody").find("input:checkbox:checked");
		
        $("#part_selecionado").val("");
		
        jQuery.each(ipts, function(){
            //alert(this.name + " => " + this.value);
            if(jQuery.trim($("#part_selecionado").val()) == "")
            {
                $("#part_selecionado").val("'" + this.value + "'");
            }
            else
            {
                $("#part_selecionado").val($("#part_selecionado").val() + ",'" + this.value + "'");
            }
        })		
    }
	
    function imprimirCertificado(tipo)
    {
        getCheck();
        document.getElementById('filter_bar_form').action = "certificado_participante/certificado/" + tipo;
        document.getElementById('filter_bar_form').method = "post";
        document.getElementById('filter_bar_form').target = "_blank";
        $("#filter_bar_form").submit();
    }
	
    function imprimirEtiqueta()
    {
        getCheck();
        document.getElementById('filter_bar_form').action = "certificado_participante/etiqueta/";
        document.getElementById('filter_bar_form').method = "post";
        document.getElementById('filter_bar_form').target = "_blank";
        $("#filter_bar_form").submit();
    }	
	
    function protocolo()
    {
        getCheck();

        if($("#part_selecionado").val() != "")
        {
            $("#result_div").html("<?php echo loader_html(); ?>");

            $.post('<?php echo base_url() . index_page(); ?>/ecrm/certificado_participante/protocolo',
            {
                cd_empresa       : $("#cd_plano_empresa").val(),
                cd_plano         : $("#cd_plano").val(),
                dt_inicial       : $("#dt_inicial").val(),
                dt_final         : $("#dt_final").val(),
                part_selecionado : $("#part_selecionado").val(),
                cd_empresa_part   : $("#cd_empresa_part").val(),
                cd_registro_empregado_part : $("#cd_registro_empregado_part").val(),
                seq_dependencia_part   : $("#seq_dependencia_part").val()
            }
            ,
            function(data)
            {
                $("#result_div").html(data);
                protocolo_result();
            }
        );
        }
        else
        {
            alert("Selecione os participantes.");
        }	
		
    }		
	
    function protocoloGerar()
    {
        if($('#fl_ordenacao_1_f').val() != '' && $('#fl_tipo_order_1_f').val() != '' && $('#fl_ordenacao_2_f').val() != '' && $('#fl_tipo_order_2_f').val() != '')
        {
            if(confirm("Deseja GERAR Protocolo de Digitalização?"))
            {
                
                $('#fl_ingresso').val($('#fl_ingresso_f').val());
                $('#fl_ordenacao_1').val($('#fl_ordenacao_1_f').val());
                $('#fl_tipo_order_1').val($('#fl_tipo_order_1_f').val());
                $('#fl_ordenacao_2').val($('#fl_ordenacao_2_f').val());
                $('#fl_tipo_order_2').val($('#fl_tipo_order_2_f').val());
                
                getCheckProtocolo();

                document.getElementById('filter_bar_form').action = "certificado_participante/protocolo_gerar/";
                document.getElementById('filter_bar_form').method = "post";
                document.getElementById('filter_bar_form').target = "_self";
                $("#filter_bar_form").submit();

            }
        }
        else
        {
            alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)" );
        }
        
    }	
	
    function checkAllProtocolo()
    {
        var ipts = $("#tabela_protocolo>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
			clickCheck(this.id);
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
			clickCheck(this.id);
        });
    }	
	
	function check_documento(bol)
    {
		var cod_documento = $('#cod_documento').val();
		
		if(cod_documento != '')
		{
			var ipts = $("#tabela_protocolo>tbody").find("input:checkbox");
			var arr;
			
			jQuery.each(ipts, function(){
				arr = this.id.split("_");
				
				if(cod_documento == arr[4])
				{
					this.checked = bol;
					clickCheck(this.id);
				}
			});
		}
		else
		{
			alert('Informe o código do documento');
		}
    }	
	
	function checkAllProtocoloInterno()
    {
        var ipts = $("#tabela_protocolo_interno>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAllProtocoloInterno");
	 
        check.checked ?
            jQuery.each(ipts, function(){
            this.checked = true;
        }) :
            jQuery.each(ipts, function(){
            this.checked = false;
        });
    }	
	
    function getCheckProtocolo()
    {
        var ipts = $("#tabela_protocolo>tbody").find("input:checkbox:checked");
		
        $("#part_selecionado").val("");
		$("#prot_selecionado").val("");
		
        jQuery.each(ipts, function(){
            //alert(this.name + " => " + this.value);
			
			if(this.name.indexOf("prot_") > -1)
			{
				if(jQuery.trim($("#prot_selecionado").val()) == "")
				{
					$("#prot_selecionado").val("'" + this.value + "'");
				}
				else
				{
					$("#prot_selecionado").val($("#prot_selecionado").val() + ",'" + this.value + "'");
				}
			}
			else
			{
				if(jQuery.trim($("#part_selecionado").val()) == "")
				{
					$("#part_selecionado").val("'" + this.value + "'");
				}
				else
				{
					$("#part_selecionado").val($("#part_selecionado").val() + ",'" + this.value + "'");
				}
			}
        })		
    }	
	
	function carrega_protocolo_interno()
	{
		$("#add_protocolo_interno").hide();
	
		$.post('<?php echo base_url() . index_page(); ?>/ecrm/certificado_participante/lista_protocolo_interno',
		{
			nr_ano      : $("#nr_ano").val(),
			nr_contador : $("#nr_contador").val(),
			cd_empresa  : $("#cd_plano_empresa").val()
		},
		function(data)
		{
			$("#result_protocolo_interno").html(data);
			if(data != '')
			{
				$("#add_protocolo_interno").show();
			}
		});
	}
	
	function adcionar_protocolo_interno()
    {
		var ipts = $("#tabela_protocolo_interno>tbody").find("input:checkbox:checked");
		var tr = '';
		
        jQuery.each(ipts, function(){
			var dateObject = new Date;
			var id_unico = dateObject.getFullYear() + "" + dateObject.getMonth() + "" + dateObject.getDate() + "" + dateObject.getTime() + "" + Math.floor(Math.random() * 1000);
			
			var name = $(this).attr('name');
			var attr = ' name="'+name+'" ';
				attr+= ' id="'+name+'"' ;
				attr+= ' idunico="'+id_unico+'" ';
				attr+= ' fl_verificar="N" ';
				attr+= ' tipo="'+$(this).attr('tipo')+'" ';
				attr+= ' arquivo_nome="'+$(this).attr('arquivo_nome')+'" ';
				attr+= ' arquivo="'+$(this).attr('arquivo')+'" ';
				attr+= ' re_cripto="'+$(this).attr('re_cripto')+'" ';
				attr+= ' cd_empresa="'+$(this).attr('cd_empresa')+'" ';
				attr+= ' cd_registro_empregado="'+$(this).attr('cd_registro_empregado')+'" ';
				attr+= ' seq_dependencia="'+$(this).attr('seq_dependencia')+'" ';
				attr+= ' cd_documento_recebido_item="'+$(this).attr('cd_documento_recebido_item')+'" ';
				attr+= ' cd_documento_recebido="'+$(this).attr('cd_documento_recebido')+'" ';
				attr+= ' cd_tipo_doc="'+$(this).attr('cd_tipo_doc')+'" ';
				attr+= ' value="'+$(this).val()+'" ';
				attr+= ' onclick="clickCheck(\'' + name + '\');" ';
			
			var input = '<input type="checkbox" checked="checked" '+attr+'>'
			
			tr = tr + '<tr class="sort-impar" onmouseout="sortSetClassOut(this);" onmouseover="sortSetClassOver(this);">';
			tr = tr + '<td valign="top" style="text-align:center;">'+input+'</td>';
			tr = tr + '<td valign="top" style="text-align:center;">'+$(this).attr('cd_empresa')+'/'+$(this).attr('cd_registro_empregado')+'/'+$(this).attr('seq_dependencia')+'</td>';
			tr = tr + '<td>'+$(this).attr('nome')+'</td>';
			tr = tr + '<td valign="top" style="text-align:center;">'+$(this).attr('cd_tipo_doc')+'</td>';
			tr = tr + '<td>'+$(this).attr('nome_documento')+'</td>';
			
			if($(this).attr('arquivo_nome') != '')
			{
				tr = tr + '<td valign="top" style="text-align:center;">D</td>';
			}
			else
			{
				tr = tr + '<td valign="top" style="text-align:center;">P</td>';
			}
			tr = tr + '</tr>';
			
			var ar_tmp = new Array();
			    ar_tmp["id"]                         = id_unico;
				ar_tmp["cd_tipo_doc"]                = $(this).attr('cd_tipo_doc');
				ar_tmp["cd_documento_recebido"]      = $(this).attr('cd_documento_recebido');
				ar_tmp["cd_documento_recebido_item"] = $(this).attr('cd_documento_recebido_item');
				ar_tmp["cd_empresa"]                 = $(this).attr('cd_empresa');
				ar_tmp["cd_registro_empregado"]      = $(this).attr('cd_registro_empregado');
				ar_tmp["seq_dependencia"]            = $(this).attr('seq_dependencia');
				ar_tmp["re_cripto"]                  = $(this).attr('re_cripto');
				ar_tmp["arquivo"]                    = $(this).attr('arquivo');
				ar_tmp["arquivo_nome"]               = $(this).attr('arquivo_nome');
				ar_tmp["tipo"]                       = $(this).attr('tipo');
				ar_tmp["fl_verificar"]               = "N";
			certificadoTMPAdd(ar_tmp);
        });		

		if(ipts.length > 0 && tr != '')
		{
			$("#tabela_protocolo").append(tr);
			$("#result_protocolo_interno").html('');
			$("#add_protocolo_interno").hide();
			
			alert("Documento(s) adicionado(s).");
		}
    }	
	
	function certificadoTMPAdd(param)
	{
		$.post('<?php echo base_url().index_page(); ?>/ecrm/certificado_participante/certificadoTMPAdd',
		{
			id                         : param["id"],
			cd_tipo_doc                : param["cd_tipo_doc"],
			cd_documento_recebido      : param["cd_documento_recebido"],
			cd_documento_recebido_item : param["cd_documento_recebido_item"],
			cd_empresa                 : param["cd_empresa"],
			cd_registro_empregado      : param["cd_registro_empregado"],
			seq_dependencia            : param["seq_dependencia"],
			re_cripto                  : param["re_cripto"],
			arquivo                    : param["arquivo"],
			arquivo_nome               : param["arquivo_nome"],
			tipo                       : param["tipo"],
			fl_verificar               : param["fl_verificar"]
		},
		function(data)
		{
			//alert(data);
		});		
	}

	function certificadoTMPDel(idunico)
	{
		$.post('<?php echo base_url().index_page(); ?>/ecrm/certificado_participante/certificadoTMPDel',
		{
			id : idunico
		},
		function(data)
		{
			//alert(data);
		});	
	}
	
	function clickCheck(campo)
	{
		var id_campo = "#" + campo;
		
		//alert(campo);
		
		if($(id_campo).attr('checked'))
		{
			var ar_tmp = new Array();
				ar_tmp["id"]                         = $(id_campo).attr('idunico');
				ar_tmp["cd_tipo_doc"]                = $(id_campo).attr('cd_tipo_doc');
				ar_tmp["cd_documento_recebido"]      = $(id_campo).attr('cd_documento_recebido');
				ar_tmp["cd_documento_recebido_item"] = $(id_campo).attr('cd_documento_recebido_item');
				ar_tmp["cd_empresa"]                 = $(id_campo).attr('cd_empresa');
				ar_tmp["cd_registro_empregado"]      = $(id_campo).attr('cd_registro_empregado');
				ar_tmp["seq_dependencia"]            = $(id_campo).attr('seq_dependencia');
				ar_tmp["re_cripto"]                  = $(id_campo).attr('re_cripto');
				ar_tmp["arquivo"]                    = $(id_campo).attr('arquivo');
				ar_tmp["arquivo_nome"]               = $(id_campo).attr('arquivo_nome');
				ar_tmp["tipo"]                       = $(id_campo).attr('tipo');
				ar_tmp["fl_verificar"]               = $(id_campo).attr('fl_verificar');
			certificadoTMPAdd(ar_tmp);	
		}
		else		
		{
			certificadoTMPDel($(id_campo).attr('idunico'));
		}
	}
	
	function certificadoPadrao()
	{
		if (($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != ""))
		{
			location.href='<?php echo site_url("ecrm/certificado_participante/certificadoPadrao"); ?>/' + $("#cd_plano").val() + "/" + $("#cd_plano_empresa").val();
		}
		else
		{
			alert("Informe a Empresa e o Plano");
		}
	}
	
    $(document).ready(function() {
        filtrar();
    });	
</script>
<?php
$config['button'][]=array('Certificado Padrão', 'certificadoPadrao()');

$abas[] = array('aba_lista', 'Certificado', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_plano_ajax('cd_plano', '', '', 'Empresa: ', 'Plano: ','',' AND cd_empresa NOT IN (4,5)');
			echo filter_date_interval('dt_inicial', 'dt_final', 'Data Ingresso: ');
			echo form_default_hidden('fl_ordenacao_1');
			echo form_default_hidden('fl_tipo_order_1');
			echo form_default_hidden('fl_ordenacao_2');
			echo form_default_hidden('fl_tipo_order_2');
			echo form_default_hidden('fl_ingresso');
			echo filter_participante(array('cd_empresa_part', 'cd_registro_empregado_part', 'seq_dependencia_part', 'nome_participante'), 'Participante (Emp/RE/Seq): ', false, TRUE, FALSE);
			echo form_default_hidden('part_selecionado', "Selecionados:");
			echo form_default_hidden('prot_selecionado', "Selecionados:");
		echo form_end_box_filter();
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer'); 
?>
