<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

include 'oo/start.php';
using( array( 'projetos.documento_protocolo', 'projetos.documento_protocolo_item') );

header( 'location:'.base_url().'index.php/ecrm/protocolo_digitalizacao/indexar/'.$_REQUEST['cd']);

class documento_protocolo_partial_indexar
{
	private $db;
	private $DIVISAO_GAD = "GAD";
	private $divisao_usuario_logado = "";
	private $ajax_command="";

	public $cd_documento_protocolo = 0;
	
	public $documento_protocolo;

	function verificar_permissao()
	{
		if(($_SESSION["D"]!='GAD') and ($_SESSION['Z'] != 170))
	    {
	        header( 'Location: acesso_restrito.php?IMG=' );
	    }
	}
	
	function __construct($db, $divisao)
	{
		$this->verificar_permissao();
		
		$this->db = $db;
		$this->divisao_usuario_logado = $divisao;
		$this->requestParams();
		
		$this->documento_protocolo = documento_protocolo::carregar( $this->cd_documento_protocolo );
		
		// Desvio do fluxo em virtude de comandos ajax
        if ($this->ajax_command=="receber_item")
        {
			// Recebimento de item, comando executado na lista de itens de um protocolo
        	$this->receber_item();
        	exit;
		}
        if ($this->ajax_command=="salvar_indexacao")
        {
        	$this->salvar_indexacao();
        	exit;
		}
        if ($this->ajax_command=="carregar_indexacao")
        {
        	$this->carregar_indexacao();
        	exit;
		}
        if ($this->ajax_command=="atualizar_total_indexacao")
        {
        	$this->atualizar_total_indexacao();
        	exit;
		}
	}
	private function requestParams()
	{
		if( isset($_REQUEST['cd']) ) $this->cd_documento_protocolo = $_REQUEST['cd'];
		if( isset($_POST['command']) ) $this->ajax_command = $_POST['command'];
	}
	
	public function getItemResult()
    {
    	$service = new service_projetos( $this->db );
        $rst = $service->documento_protocolo_item_FetchAll( $this->cd_documento_protocolo, true );
        $service = null;
        return $rst;
	}

    function atualizar_total_indexacao()
    {
    	echo documento_protocolo_item::consultar_total_por_indexacao( $_POST['dt_indexacao'] );
    }

    function carregar_total_indexados()
    {
    	return documento_protocolo_item::consultar_total_indexados( $this->cd_documento_protocolo );
    }
}

$esta = new documento_protocolo_partial_indexar( $db, $D );

$bgcoloritem="#F3F5F2";
$participante="";
$participante_exibir="";
$collection = $esta->getItemResult();
$indexados = $esta->carregar_total_indexados();
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<HTML>
	 <HEAD>
	  <TITLE> Lista de Itens protocolados para digitalização </TITLE>
	  <META NAME="Generator" CONTENT="EditPlus">
	  <META NAME="Author" CONTENT="">
	  <META NAME="Keywords" CONTENT="">
	  <META NAME="Description" CONTENT="">
	  	<script src="inc/mascara.js"></script>
	  	<script language="JavaScript" type="text/JavaScript" src="inc/prototype_1_6_0.js"></script>
		<script language="JavaScript" type="text/JavaScript" src="inc/effects.js"></script>
		<script language="JavaScript" type="text/JavaScript" src="documento_protocolo.js"></script>

	  	<!-- SORT TABLE -->
		<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
		<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>
		<!-- SORT TABLE -->
		
		
		<!-- calendar stylesheet -->
		<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-eprev.css" title="win2k-cold-1" />

		<!-- main calendar program -->
		<script type="text/javascript" src="jscalendar/calendar.js"></script>

		<!-- language for the calendar -->
		<script type="text/javascript" src="jscalendar/lang/calendar-br.js"></script>

		<!-- the following script defines the Calendar.setup helper function, which makes
			 adding a calendar a matter of 1 or 2 lines of code. -->
		<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
		

		<script type='text/javascript' src='documento_protocolo_partial_indexar.js'></script>

		<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
		<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>
		
	<link href="inc/abas_verde.css" rel="stylesheet" type="text/css">		
		<link href="main.css" rel="stylesheet" type="text/css">

		<style>
			.tachado
			{
				color:#9F4000;
			}
		</style>
	 </HEAD>

	 <BODY onload="init();">
<div class='aba_definicao'>

	<div id='aba'>
		<ul>
			<li id='aba_lista' onclick="location.href='<?php echo base_url(); ?>/index.php/ecrm/protocolo_digitalizacao'"><span>Lista</span></li>
			<li id='aba_atendimento' class='abaSelecionada' onclick="location.reload();"><span>Documentos Protocolados</span></li>
		</ul>
	</div>

	<div class='div_aba_content'>
		<!--<br /><br /><br />-->
		<div id='command_bar' class='command-bar'>
			<br /><br />
			<div id="result_div">
	 
	 
	 
	 <iframe name="comandos" style="display:none;width:100%;"></iframe>
	 <form name="only_form" id="only_form" method="POST">

	 	<input id="cd_documento_protocolo" name="cd_documento_protocolo" type="hidden" value="<?php echo $esta->cd_documento_protocolo; ?>" />
	 	<input id="cd_comando_text" name="cd_comando_text" type="hidden" value="" />
	 	<input id="command" name="command" type="hidden" value="" />

		<br />

	 	<?php if( documento_protocolo::protocolo_ja_confirmado( $esta->cd_documento_protocolo ) ) : ?>

	 		<center><h1>Protocolo já confirmado</h1></center>

	 	<?php else : ?>

	 		<div style="border-width: 1px; border-style:solid;background:#EEEEEE;padding:10 10 10 10; margin-bottom:10;">
	 			Data de indexação: <input type="text" name="dt_indexacao" id="dt_indexacao" style="width:75px;" OnKeyDown="/*mascaraData(this,event);*/" onblur="/*carregar_total_indexados_na_data();*/" readonly /> (<a href="javascript:limpar_data();">limpar data</a>)
	 			<script type="text/javascript">
				    Calendar.setup({
				        inputField     :    "dt_indexacao",   		// id of the input field
				        ifFormat       :    "%d/%m/%Y",       // format of the input field
				        showsTime      :    false,
				        timeFormat     :    "24",
				        onUpdate       :    carregar_total_indexados_na_data
				    });
				</script>
	 			<br />
	 			Total de indexação no dia informado: <b><span id="total_indexados"></span></b>
	 			<hr />
	 			Total de ítens devolvidos deste protocolo: <b><?php echo documento_protocolo_item::consultar_total_devolvidos($esta->cd_documento_protocolo) ?></b><br>
	 			Total de ítens listados abaixo (não devolvidos): <b><span id="total"><?php echo pg_num_rows($collection) ?></span></b><br>
	 		</div>

			<div id="lista">

				<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">

			    	<thead>
						<tr>
							<td><b></b></td>
							<td><b>Data Indexação</b></td>
							<td><b>Observações</b></td>
							<td><b>Participante</b></td>
							<td><b>Documento</b></td>
                                                        <td><b>Descartar</b></td>
							<td><b>Processo</b></td>
							<td><b>Folhas</b></td>
						</tr>
			    	</thead>

			    	<tbody>
			    		<? while( $item = pg_fetch_array($collection) ) : $id = $item['cd_documento_protocolo_item']; ?>
	
			    			<?php $background = ($background=="#F3F5F2")?"#C9D0C8":"#F3F5F2"; ?>
	
				    		<tr id="linha_<?php echo $id; ?>" style="background:<?php echo $background; ?>;">
								<td align="center">
									<input id="cd_documento_protocolo_item_<?php echo $id; ?>" name="cd_documento_protocolo_item<?php echo $id; ?>" type="hidden" value="<?php echo $id; ?>" />
									<input name="marcar_check_<?php echo $id; ?>" id="marcar_check_<?php echo $id; ?>" type="checkbox" value="<?php echo $id; ?>" onclick="marcar(this,'<?php echo $id; ?>');" />
								</td>
								<td align="center">
									<input name="dt_indexacao_<?php echo $id; ?>" id="dt_indexacao_<?php echo $id; ?>" type="text" value="<?php echo $item["dt_indexacao"]; ?>" style="width:75px;"   OnKeyDown="mascaraData(this,event);" />
									<script>MaskInput( document.getElementById('dt_indexacao_<?php echo $id; ?>'), "99/99/9999" );</script>
								</td>
								<td align="center">
									<?php 
									$observacao = "";
									if( $item["ds_observacao_indexacao"]!="" ) 
									{
										$observacao .= $item["ds_observacao_indexacao"];
									}
									if( $item["ds_observacao_indexacao"]!="" && $item["motivo_devolucao"]!="" )
									{
										$observacao .= " - ";
									}
									if( $item["motivo_devolucao"]!="" ) 
									{
										$observacao .= $item["motivo_devolucao"];
									}
									?>
									<input name="observacao_text_<?php echo $id; ?>" 
										id="observacao_check_<?php echo $id; ?>" 
										type="text" 
										maxlength="500" 
										value="<?php echo $observacao ?>" 
										/>
								</td>
								<td><?php echo $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"]; ?></td>
								<td>
								<?php 
								if($item["cd_tipo_doc"]!="") 
								{
									echo $item["cd_tipo_doc"] . " - " . $item["nome_documento"];
								}
								else
								{
									if($item["cd_doc_juridico"]!="")
									{
										echo $item["cd_doc_juridico"] . ' - ' . $item["descricao_documento_juridico"];
									}
								} 

								?></td>
                                                                <td><?php echo ($item["fl_descartar"] == 'S' ? 'Sim' : 'Não'); ?></td>
								<td><?php echo $item["ds_processo"]; ?></td>
								<td><?php echo $item["nr_folha"]; ?></td>
							</tr>
	
						<? endwhile; ?>
			    	</tbody>

				</table>

				<br />
				<center>
					<input type="button" class="botao" name="salvar_button" id="salvar_button" style="width:100px;height:30px;" value="Salvar" onclick="salvar();" />
					<input type="button" class="botao" name="salvar_continuar_button" id="salvar_continuar_button" style="height:30px;" value="Salvar e Confirmar" onclick="salvar_e_confirmar();" />
					<input type="button" class="botao" name="voltar_button" id="voltar_button" style="height:30px; width:100px;" value="Voltar" onclick="document.location.href='documento_protocolo.php'" />
				</center>

			</div>

			<div id="resultado" style="display:none;"></div>

	 <?php endif; ?>

	 </form>
			</div>
			<br />
	</div>

</div>
	 </BODY>
	</HTML>