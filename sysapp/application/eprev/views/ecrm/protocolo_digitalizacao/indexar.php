<?php
set_title('Protocolo de Digitalização');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href ="<?= site_url('ecrm/protocolo_digitalizacao') ?>";
    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "DateTimeBR",
            null,
            "CaseInsensitiveString",
            "DateBR",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "RE",
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "Number"
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
        ob_resul.sort(0, false);
    }
    
    function carregar_tl_indexados()
    {
        if($("#dt_indexacao").val() == "")
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
    
    function marcar(o,v)
    {
        if(o.checked)
        {
            if( $("#total_indexados").html() == "" )
            {
                alert( "Informe a data de indexação." );
                $("dt_indexacao").focus();
                o.checked = false;
                return false;
            }
            else
            {
                data_index = $("#dt_indexacao_"+v).val();
                if(data_index != $('#dt_indexacao').val())
                {
                    $("#dt_indexacao_"+v).val($('#dt_indexacao').val());
                    $("#total_indexados").html(parseInt($("#total_indexados").html())+1);
                    $("#valor").val(parseInt($("#valor").val())+1)
                }
            }
        }
        else
        {
            if($("#dt_indexacao_"+v).val() == $("#dt_indexacao").val() )
            {
                $("#total_indexados").html(parseInt($("#total_indexados").html())-1);
                $("#valor").val(parseInt($("#valor").val())-1)
            }
            
            $("#dt_indexacao_"+v).val('');
        }
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
            if(this.checked && $(this).attr('id') != 'checkboxCheckAll')
            {
                tl++;
            }
           
        });
        
        if(tl == total)
        {
            $('#return').val(1); 
            if(confirm("Deseja salvar e confirmar?"))
            {
                $("form").submit();
            }
        }
        else
        {
            alert("Atenção:\n\nAlgum ítem não está marcado.\nAntes de 'Salvar e Confirmar', todos os ítens devem estar marcados.\n\nPara salvar sem confirmar, clique no botão 'Salvar'.");
        }           
    }

    function download(cd_protocolo)
    {
        window.open("<?= base_url(); ?>/index.php/ecrm/protocolo_digitalizacao/zip_docs/"+cd_protocolo, "_blank", "width=100,height=100,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0");
    }

    function checkAll()
    {
        var ipts = $("#table-1>tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");
     
        if(check.checked)
        {
            if($("#total_indexados").html() == '')
            {
                alert("Informe a data de indexação.");
                $("dt_indexacao").focus();
                check.checked = false;
            }
            else
            {
                jQuery.each(ipts, function(){
                    name = this.name;

                    retorno = name.split("_");

                    this.checked = true;

                    marcar(this, retorno[2]);
                }) 
            }
        }
        else
        {
            jQuery.each(ipts, function(){
                name = this.name;

                retorno = name.split("_");

                this.checked = false;

                marcar(this, retorno[2]);
            });
        }            
    }

    $(function(){
        $.post("<?= site_url('ecrm/protocolo_digitalizacao/lista_documento_indexar/'.$cd_documento_protocolo) ?>", 
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });   
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
    $abas[] = array('aba_detalhe', 'Documentos Protocolados', true, 'location.reload();');
    
    echo aba_start($abas);
        echo '<div id="result_div"></div>';
        echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>