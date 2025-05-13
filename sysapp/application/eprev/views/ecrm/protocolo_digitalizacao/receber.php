<?php
set_title('Protocolo de Digitalização');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/protocolo_digitalizacao"); ?>';
    }
    
    function load()
    {
        $.post('<?php echo  site_url("ecrm/protocolo_digitalizacao/lista_documento_receber/".$cd_documento_protocolo); ?>','', 
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });   
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tbDocReceber"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			null,
            null,
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'RE',
			'CaseInsensitiveString',
            'CaseInsensitiveString',			
            'CaseInsensitiveString',			
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
        ob_resul.sort(2, true);
    }
    
    function visto(fl_checked, v)
    {
		if(fl_checked)
        {
            $("#devolver_check_"+v).removeAttr('checked');
            if( $("#dt_indexacao").val() != "" && $("#total_indexados").html() != "")
            {
                $("#dt_indexacao_"+v).val($("#dt_indexacao").val());
                $("#total_indexados").html(parseInt($("#total_indexados").html())+1);
                $("#valor").val(parseInt($("#valor").val())+1)
            }
        }
        else
        {
            if(parseInt($("#valor").val()) > 0)
            {
                $("#total_indexados").html(parseInt($("#total_indexados").html())-1);
                $("#valor").val(parseInt($("#valor").val())-1)
                $("#dt_indexacao_"+v).val("");
            }
        }
    }
    
    function devolver(o, v)
    {
        if(o.checked)
        {   
            $("#visto_check_"+v).removeAttr('checked');
            if($("#dt_indexacao").val() != "" && $("#total_indexados").html() != "" && parseInt($("#total_indexados").html()) > 0)
            {
                if( $("#dt_indexacao_"+v).val() == $("#dt_indexacao").val() )
                {
                    $("#total_indexados").html(parseInt($("#total_indexados").html())-1);
                    $("#valor").val(parseInt($("#valor").val())-1)
                    $("#dt_indexacao_"+v).val("");
                }
            }
        }
    }
    
	function devolverTudo()
    {
        if(confirm("Deseja devolver o protocolo?") )
        {
            location.href='<?php echo site_url("ecrm/protocolo_digitalizacao/devolver_protocolo/".$cd_documento_protocolo); ?>';
        }
    }	
	
    function carregar_tl_indexados()
    {
        if( $("#dt_indexacao").val() == "" )
        {
            $("#total_indexados").html(0);
        }
        else
        {
            $.post('<?php echo  site_url("ecrm/protocolo_digitalizacao/total_indexados_data/"); ?>',
            {
                 dt_indexacao: $("#dt_indexacao").val()
            }, 
            function(data)
            {
                $('#total_indexados').html(data);
                $('#valor').val(data);
            }); 
        }
    }
    
    function download(cd_protocolo)
    {
            window.open('<?php echo base_url(); ?>/index.php/ecrm/protocolo_digitalizacao/zip_docs/'+cd_protocolo, '_blank', 'width=100,height=100,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0');
    }
    
    function salvar()
    {
        if(confirm("Deseja salvar?"))
        {
            $("form").submit();
        }
    }
	
    function salvar_confirmar()
    {
        var total = parseInt($("#total").html());
		
        var tl = 0;
        $('input:checkbox').each(function(){
            if((this.value == "receber") || (this.value == "devolver"))
			{
				if(this.checked)
				{
					tl++;
				}
			}
        });
		
        if(tl == total)
        {
            $('#return').val(1); 
            if( confirm("Deseja salvar e CONFIRMAR?") )
            {
                $("form").submit();
            }
        }
        else
        {
            alert("Atenção:\n\nAlgum ítem não está marcado.\nAntes de 'Salvar e Confirmar', todos os ítens devem estar marcados com 'Visto' ou 'Devolução'.\n\nPara salvar sem confirmar, clique no botão 'Salvar'.");
        }           
    }
	
	function checkAllVisto()
    {
        var ipts = $("#tbDocReceber>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAllVisto");
	 
        check.checked 
		?
            jQuery.each(ipts, function()
			{
				if(this.value == "receber")
				{ 
					var item = this.name;
					var item_cd = item.replace("marcar_check_", "cd_documento_protocolo_item_");
					visto(true, $("#"+item_cd).val());
					this.checked = true; 
				}
			}) 
		:
            jQuery.each(ipts, function()
			{
				if(this.value == "receber")
				{ 
					var item = this.name;
					var item_cd = item.replace("marcar_check_", "cd_documento_protocolo_item_");
					visto(false, $("#"+item_cd).val());
					this.checked = false; 
				}
			})
		;
			
    }	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Documentos Protocolados', true, 'location.reload();');
echo aba_start($abas);

?>
<div id="result_div"><?php echo loader_html(); ?></div>
<script type="text/javascript">
    load();
</script>
<?php
	echo br(10);
echo aba_end();
$this->load->view('footer');
?>