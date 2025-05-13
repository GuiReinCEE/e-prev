<?php
set_title('Relatório Auditoria');
$this->load->view('header');
?>
<script>
    <?php
		echo form_default_js_submit(Array('nr_mes', 'nr_ano', 'fl_tipo', 'representante', 'conclusao'), 'valida_tipo(form)');
    ?>

    function valida_tipo(form)
    {
        var validacao = true;

        if($("#fl_tipo").val() == 'E')
        {
            if( $("#ds_empresa").val()=="" )
            {
                alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[ds_empresa]" );
                $("#ds_empresa").focus();
                validacao = false;
            }
        }
        
        if(validacao)
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }

    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria"); ?>';
    }
    
    function ir_equipe(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/equipe"); ?>/'+cd_relatorio_auditoria;
    }
    
    function ir_constatacao(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/constatacao"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_acompanhamento(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/acompanhamento"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_anexo(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/anexo"); ?>/'+cd_relatorio_auditoria;
    }
    
    function gera_pdf(cd_relatorio_auditoria)
	{
        location.href='<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/gera_pdf/'+cd_relatorio_auditoria;
	}

    $(function(){
        seleciona_tipo($('#fl_tipo').val());
    });

    function seleciona_tipo(fl_tipo)
    {
        if(fl_tipo == 'E')
        {
            $("#ds_empresa_row").show();
           // $("#cd_auditor_lider_row").hide();

           // $("#cd_auditor_lider").val('');
        }
        else
        {
            $("#ds_empresa_row").hide();
          //  $("#cd_auditor_lider_row").hide();

            $("#ds_empresa").val('');
          //  $("#cd_auditor_lider").val('');
        }
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
if($row['cd_relatorio_auditoria'] > 0)
{
    $abas[] = array('aba_lista', 'Equipe', FALSE, 'ir_equipe('.$row['cd_relatorio_auditoria'].');');
    $abas[] = array('aba_lista', 'Constatação', FALSE, 'ir_constatacao('.$row['cd_relatorio_auditoria'].');');
    $abas[] = array('aba_lista', 'Registros Gerais', FALSE, 'ir_acompanhamento('.$row['cd_relatorio_auditoria'].');');
    $abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo('.$row['cd_relatorio_auditoria'].');');
}
if(count($collection) > 0)
{
	$body=array();
	$head = array(
		'Constatação', 
		'Quantidade'
	);

	$body[] = array(
		array('Não Conformidade', 'text-align:left'),
		$collection['tl_nao_conformidade'],
	);

	$body[] = array(
		array('Oportunidade de Melhoria', 'text-align:left'),
		$collection['tl_melhoria'],
	);	
	
	$body[] = array(
		array('Observação', 'text-align:left'),
		$collection['tl_observacao'],
	);

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
}

$auditoria[] = array('value' => 'I', 'text' => 'Interna');
$auditoria[] = array('value' => 'E', 'text' => 'Externa');

echo aba_start( $abas );
    echo form_open('gestao/relatorio_auditoria/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_relatorio_auditoria', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
            echo form_default_mes_ano('nr_mes','nr_ano','Mês/Ano:*', '01/'.$row['mes_ano']);
            echo form_default_dropdown('fl_tipo', 'Auditoria:*', $auditoria, $row['fl_tipo'], 'onchange="seleciona_tipo($(this).val())"');
            echo form_default_hidden('escopo', "Escopo:", 'Oferta, desenvolvimento e administração de planos previdenciários.', "style='width:500px;'");
            echo form_default_checkbox_group('ar_processos', 'Processos Auditados:', $ar_processos, $ar_processos_checked, 280);
            echo form_default_textarea('representante', "Colaboradores Auditados:*", $row, "style='width:500px;'");
            //echo form_default_dropdown('cd_auditor_lider', 'Auditor Líder:*', $ar_comite, array($row['cd_auditor_lider']));
            echo form_default_text('ds_empresa', 'Empresa:*', $row['ds_empresa'], 'style="width:300px;"');
            echo form_default_textarea('conclusao', "Conclusão da equipe de auditoria:*", $row, "style='width:500px;'");
        echo form_end_box("default_box");
        
        echo form_command_bar_detail_start(); 
            if($fl_permissao)
            {
                echo button_save("Salvar");
            }
            
            if($row['cd_relatorio_auditoria'] > 0)
            {
                echo button_save("Imprimir PDF", "gera_pdf(".$row['cd_relatorio_auditoria'].")", "botao_disabled");
            }
        echo form_command_bar_detail_end();
    echo form_close();
	if(count($collection) > 0)
	{
		echo $grid->render();
	}
	echo br(10);
echo aba_end();

$this->load->view('footer_interna');
?>