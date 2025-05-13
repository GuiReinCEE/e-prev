<?php
set_title('Atendimentos');
$this->load->view('header');
?>
<script>
function ir_location(local)
{
    switch(local)
    {
    case 1:
      location.href='<?php echo site_url("ecrm/atendimento_lista/atendente"); ?>';
      break;
    case 2:
      location.href='<?php echo site_url("ecrm/atendimento_lista/data"); ?>';
      break;
    case 3:
      location.href='<?php echo site_url("ecrm/atendimento_lista/tipo"); ?>';
      break;
    case 4:
      location.href='<?php echo site_url("ecrm/atendimento_lista/programa"); ?>';
      break;
    case 5:
      location.href='<?php echo site_url("ecrm/atendimento_lista/index"); ?>';
      break;
    }
}

function filtrar()
{
	load();
}

function load()
{
    encaminhamento();
    reclamacao();
    reclamacao_sugestao();
    busca_atendimento();
}

function encaminhamento()
{
    document.getElementById("encaminhamento_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_lista/listar_atend_encaminhamento'
		,{
			cd_atendimento: $('#cd_atendimento').val()
		}
		,
	function(data)
		{
			document.getElementById("encaminhamento_div").innerHTML = data;
			//configure_result_table();
		}
	);
}

function reclamacao()
{
    document.getElementById("reclamacao_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_lista/listar_atend_reclamacao'
		,{
			cd_atendimento: $('#cd_atendimento').val()
		}
		,
	function(data)
		{
			document.getElementById("reclamacao_div").innerHTML = data;
			//configure_result_table();
		}
	);
}

function reclamacao_sugestao()
{
    document.getElementById("reclamacao_sugestao_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_lista/listar_atend_sugestao'
		,{
			cd_atendimento: $('#cd_atendimento').val()
		}
		,
	function(data)
		{
			document.getElementById("reclamacao_sugestao_div").innerHTML = data;
			//configure_result_table();
		}
	);
}

function busca_atendimento()
{
    document.getElementById("atendimento_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_lista/busca_atendimento'
		,{
			cd_atendimento: $('#cd_atendimento').val()
		}
		,
	function(data)
		{
			document.getElementById("atendimento_div").innerHTML = data;
			//configure_result_table();
		}
	);
}
</script>
<?php
$abas[] = array('aba_lista', 'Atendente', FALSE, 'ir_location(1);');
$abas[] = array('aba_lista', 'Data', FALSE, 'ir_location(2);');
$abas[] = array('aba_lista', 'Tipo', FALSE, 'ir_location(3);');
$abas[] = array('aba_lista', 'Programa', FALSE, 'ir_location(4);');
$abas[] = array('aba_lista', 'Todos', FALSE, 'ir_location(5);');
$abas[] = array('aba_lista', 'Atendimento', TRUE, 'location.reload();');

echo aba_start( $abas );
echo form_start_box( "default_box", "Informações do atendimento ". $row['cd_atendimento'] );
    echo form_default_hidden('cd_atendimento', "", $row['cd_atendimento']);
    echo form_default_text('', "Atendente: ", $row['guerra'], "style='width:300%;border: 0px;' readonly" );
	if(trim($row['id_callcenter']) != "")
	{
		echo form_default_text('', "ID Callcenter: ", $row['id_callcenter'], "style='width:300%;border: 0px;' readonly" );
	}
    echo form_default_text('', "Emp/re/seq: ", $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'], "style='width:100%;border: 0px;' readonly" );
    echo form_default_text('', "Nome participante: ", $row['nome'], "style='width:300%;border: 0px; color:green;' readonly" );
    echo form_default_text('', "Observações: ", $row['obs'], "style='width:300%;border: 0px; color:green;' readonly" );
    echo form_default_text('', "Data horário do registro: ", $row['dt_atendimento'], "style='width:300%;border: 0px;' readonly" );
    echo form_default_text('', "Duração do Atendimento: ", $row['hr_atendimento'], "style='width:300%;border: 0px;' readonly" );
    echo form_default_text('', "Início: ", $row['dt_atendimento'], "style='width:300%;border: 0px;' readonly" );
    echo form_default_text('', "Fim: ", $row['dt_fim_atendimento'], "style='width:300%;border: 0px;' readonly" );
    
echo form_end_box("default_box");

?>
<div id="encaminhamento_div"></div>
<div id="reclamacao_div"></div>
<div id="reclamacao_sugestao_div"></div>
<div id="atendimento_div"></div>
<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>
