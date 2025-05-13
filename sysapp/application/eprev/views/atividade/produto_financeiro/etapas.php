<?php
set_title('Acompanhamento de Produtos');
$this->load->view('header');
?>

<script>
<?php
echo form_default_js_submit(array());
?>

    function calculaPercentual(cd_produto_financeiro_etapa_status)
    {
        var peso = new Array();
		var peso_total = 0;
		var conc_total = 0;
		var obj_peso = $("#nr_peso_" + cd_produto_financeiro_etapa_status);
		var obj_conc = $("#nr_concluido_" + cd_produto_financeiro_etapa_status);
		
		
		
		obj_peso.val((obj_peso.val() == "" ? 0 : obj_peso.val()));
		obj_conc.val((obj_conc.val() == "" ? 0 : obj_conc.val()));
		
		
		$("#pb_" + cd_produto_financeiro_etapa_status).progressBar(obj_conc.val());
		
		$('.produto_financeiro_etapa_status_peso').each(function(i) {
			peso_total = peso_total + parseInt($(this).val());
			peso[i] = parseInt($(this).val());
		});
		
		$("#peso_total").val(peso_total);
		
		$('.produto_financeiro_etapa_status_concluido').each(function(i) {
			conc_total = conc_total + ((parseInt($(this).val()) * parseInt(peso[i])) / 100);
		});		
		
		$("#conc_total").val(parseInt(conc_total));
		$("#pb_conc_total").progressBar(parseInt(conc_total));
    }	
	
	function adicionar()
	{
		if( $("#cd_produto_financeiro_etapa").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[cd_produto_financeiro_etapa]" );
			$("#cd_produto_financeiro_etapa").focus();
			return false;
		}
		else
		{
			if( confirm('Salvar?') )
			{
				$.post( '<?php echo base_url() . index_page(); ?>/atividade/produto_financeiro/salvar_etapas',
				{
					cd_produto_financeiro : $('#cd_produto_financeiro').val(),
					cd_produto_financeiro_etapa : $('#cd_produto_financeiro_etapa').val()
				},
				function(data)
				{
					$("#cd_produto_financeiro_etapa option[value='"+$('#cd_produto_financeiro_etapa').val()+"']").remove();
					$('#cd_produto_financeiro_etapa').val('');
					load();
				});
			}
		}
	}
	
	function adicionar_todas()
	{
		if( confirm('Adicionar Todas?') )
		{
			 location.href='<?php echo site_url("atividade/produto_financeiro/salvar_todas_etapas"); ?>/' +  $('#cd_produto_financeiro').val();
		}
	}
	
	function excluir(cd_produto_financeiro_etapa_status)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/produto_financeiro/excluir_etapa/".$row['cd_produto_financeiro']); ?>' + "/" + cd_produto_financeiro_etapa_status;
		}
	}
	
    function ir_lista()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/cadastro/".intval($row['cd_produto_financeiro'])); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/anexo/".intval($row['cd_produto_financeiro'])); ?>';
    }
	
	function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/produto_financeiro/listar_etapas',
		{
			cd_produto_financeiro : $('#cd_produto_financeiro').val()
		},
        function(data)
        {
			$('#result_div').html(data);
            //configure_result_table();
        });
    }
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
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
        ob_resul.sort(0, false);
    }
	
	function salva_etapas()
	{
		var soma = 0;
		var bol = true;
		
		$('.produto_financeiro_etapa_status_peso').each(function() {
			soma = soma + parseInt($(this).val());
			
			if($(this).val() > 100)
			{
				$(this).val(0); 
				bol = false;
			}			
		});		
		
		$('.produto_financeiro_etapa_status_concluido').each(function() {
			if($(this).val() > 100)
			{
				$(this).val(0); 
				bol = false;
			}
		});		
		
		
		if(soma == 100 && bol)
		{  
			$('form').submit();
		}
		else
		{	
			if(bol)
			{
				alert( "O total do peso deve fechar em 100%.\n\nO total está em "+soma+"%." );
			}
			else
			{
				alert("Valor deve ser menor que 100");
			}
		}
		
	}
	
	$(function(){
		load();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
$abas[] = array('aba_lista', 'Etapas', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');

echo aba_start($abas);

if(((intval($row['cd_produto_financeiro']) > 0) AND (($this->session->userdata('codigo') == $row['cd_usuario_inclusao']) OR ($this->session->userdata('codigo') == $row['cd_usuario_responsavel']) 
	OR ($this->session->userdata('codigo') == $row['cd_usuario_revisor'])  OR (($this->session->userdata('divisao') == 'GIN') AND ($this->session->userdata('tipo') == 'G')))) OR (intval($row['cd_produto_financeiro']) == 0))
{
	$bool = true;
}
else
{
	$bool = false;
}


echo form_start_box("default_box", "Cadastro");
	echo form_default_hidden("cd_produto_financeiro", "", $row['cd_produto_financeiro']);
	echo form_default_dropdown_db("cd_produto_financeiro_etapa", "Etapa :* ", Array('projetos.produto_financeiro_etapa', 'cd_produto_financeiro_etapa', 'ds_produto_financeiro_etapa'), Array(), "", "", TRUE, "dropdown_db.dt_exclusao IS NULL AND 0 = (SELECT COUNT(*) FROM projetos.produto_financeiro_etapa_status pfes WHERE pfes.cd_produto_financeiro = ".intval($row['cd_produto_financeiro'])." AND pfes.dt_exclusao IS NULL AND pfes.cd_produto_financeiro_etapa = dropdown_db.cd_produto_financeiro_etapa)","","Nova etapa");
echo form_end_box("default_box");

echo form_command_bar_detail_start("comand_bar",'style="text-align:left;"');
	 echo ($bool ?  button_save("Adicionar Etapa", "adicionar();") : '');
	 echo ($bool ?  button_save("Adicionar Todas Etapas", "adicionar_todas();") : '');
echo form_command_bar_detail_end();

echo br(2);

echo'<div id="result_div"></div>';

echo br(4);

echo aba_end();
$this->load->view('footer_interna');
?>