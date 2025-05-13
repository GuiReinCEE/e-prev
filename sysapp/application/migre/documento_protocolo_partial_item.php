<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

include 'oo/start.php';
using( array( 'projetos.documento_protocolo', 'projetos.documento_protocolo_item') );

header( 'location:'.base_url().'index.php/ecrm/protocolo_digitalizacao/receber/'.$_REQUEST['cd']);

class documento_protocolo_partial_item
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
	
	public function esta_confirmada_indexacao()
	{
		return ($this->documento_protocolo['dt_indexacao']!="");
	}
	
	public function esta_recebido()
	{
		return ($this->documento_protocolo['dt_ok']!="");
	}

	public function getItemResult()
    { 
    	$service = new service_projetos( $this->db );
        $rst = $service->documento_protocolo_item_FetchAll( $this->cd_documento_protocolo );
        $service = null;
        return $rst;
	}

	public function getAllowOk()
	{
		return ( $this->divisao_usuario_logado==$this->DIVISAO_GAD ); // Apenas GAD pode confirmar recebimento (OK)
	}

	function receber_item()
    {
        $recebido = '';
        if(isset($_POST['fl_recebido_' . $_POST["cd_comando_text"] ]))
        {
        	$recebido = $_POST['fl_recebido_' . $_POST["cd_comando_text"] ];
        	if($recebido) $recebido='S'; else $recebido='N';
        }

		$item = new entity_projetos_documento_protocolo_item();
        $item->set_cd_documento_protocolo_item( $_POST["cd_comando_text"] );
        $item->set_fl_recebido( $recebido );

        $service = new service_projetos( $this->db );
        $service->documento_protocolo_item_Receber( $item );

        $service = null;
        $item = null;
    }

    function salvar_indexacao()
    {
    	$dados['dt_indexacao'] = "";
    	$dados['ds_observacao_indexacao'] = "";

        if( isset($_POST['cd_documento_protocolo_item']) ) $cd_documento_protocolo_item = $_POST['cd_documento_protocolo_item'];
        if( isset($_POST['dt_indexacao']) ) $dados['dt_indexacao'] = utf8_decode( $_POST['dt_indexacao'] );
        if( isset($_POST['ds_observacao_indexacao']) ) $dados['ds_observacao_indexacao'] = utf8_decode( $_POST['ds_observacao_indexacao'] );

		if( $cd_documento_protocolo_item=="" )
		{
			echo "Item não selecionado";
			exit;
		}
		
		if( $dados['dt_indexacao']=="" )
		{
			echo "Informe a data de indexação";
			exit;
		}

        $r = documento_protocolo_item::salvar_dados_da_indexacao($cd_documento_protocolo_item, $dados);

        echo ($r)?"true":"false";
    }

    function carregar_indexacao()
    {
    	$item = documento_protocolo_item::carregar( $_POST['cd_documento_protocolo_item'] );
    	if( $item['dt_indexacao']!="" || $item['ds_observacao_indexacao']!="" )
    	{
	    	echo "Indexado em: " . $item['dt_indexacao'] . "<br />";
			echo "Obs: " . $item['ds_observacao_indexacao'] . "<br />";
    	}
    }
    
    function atualizar_total_indexacao()
    {
    	echo documento_protocolo_item::consultar_total_por_indexacao($_POST['dt_indexacao']);
    }
    
    function carregar_total_indexados()
    {
    	return documento_protocolo_item::consultar_total_indexados( $this->cd_documento_protocolo );
    }
}

$esta = new documento_protocolo_partial_item($db, $D);

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
	<script src="inc/mascara.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="inc/prototype_1_6_0.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="inc/effects.js"></script>
	<script language="JavaScript" type="text/JavaScript" src="documento_protocolo.js"></script>

	<!-- SORT TABLE -->
	<script type='text/javascript' src='<?php echo base_url(); ?>skins/skin002/sort_table/sortabletable.js'></script>
	<link type='text/css' rel='StyleSheet' href='<?php echo base_url(); ?>skins/skin002/sort_table/sortabletable.css'>
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

	<script type='text/javascript' src='documento_protocolo_partial_item.js'></script>

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

		<div id="painel" style="display:none;border-width: 1px; border-style:solid;background:#EEEEEE;padding:10 10 10 10; margin-bottom:10;">
		
		</div>
		
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
			Total de ítens recebidos deste protocolo: <b><?php echo documento_protocolo_item::consultar_total_indexados($esta->cd_documento_protocolo) ?></b><br>
			Total de ítens devolvidos deste protocolo: <b><?php echo documento_protocolo_item::consultar_total_devolvidos($esta->cd_documento_protocolo) ?></b><br>
			Total de ítens deste procolo: <b><span id="total"><?php echo pg_num_rows($collection) ?></span></b><br>
		</div>

		<div id="lista">

			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">

				<thead>
					<tr>
						<td><b>Visto</b></td>
						<td><b>Devolver</b></td>
                        <td><b>Descartar</b></td>
						<td><b>Observações</b></td>
						<td><b>Data Index.</b></td>
						<td><b>Participante</b></td>
						<td><b>Documento</b></td>
						<td><b>Processo</b></td>
						<td><b>Folhas</b></td>
						<td><b>Arquivo</b></td>
					</tr>
				</thead>
				
				<tbody>
					<? while( $item = pg_fetch_array($collection) ) : $id = $item['cd_documento_protocolo_item']; ?>

						<?php $background = ($background=="#FFFFFF")?"#E8EEF7":"#FFFFFF"; ?>

						<tr id="linha_<?php echo $id; ?>" style="background:<?php echo $background; ?>;">
							<td align="center">
								<input id="cd_documento_protocolo_item_<?php echo $id; ?>" 
									name="cd_documento_protocolo_item<?php echo $id; ?>" 
									type="hidden" 
									value="<?php echo $id; ?>"
									/>
								<input <?php if( $item["fl_recebido"]=="S" ) echo "checked"; ?> 
									name="marcar_check_<?php echo $id; ?>" 
									id="visto_check_<?php echo $id; ?>" 
									type="radio" 
									value="receber" 
									onclick="visto(this, '<?php echo $id; ?>');" 
									/>
								<a href="javascript:desmarcar('<?php echo $id; ?>');">X</a>
							</td>
							<td align="center"><input <?php if( $item["dt_devolucao"]!="" ) echo "checked"; ?> name="marcar_check_<?php echo $id; ?>" id="devolver_check_<?php echo $id; ?>" type="radio" value="devolver" onclick="devolver(this, '<?php echo $id; ?>');" /></td>
							<td align="center"><?php if( $item["fl_descartar"]=="S" ) echo '<span style="color:red; font-weight:bold">SIM</span>';  else  echo '<span style="color:black; font-weight:bold">NÃO</span>';?></td>
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
							<td><input name="dt_indexacao_<?php echo $id; ?>" 
									id="dt_indexacao_<?php echo $id; ?>" 
									type="text" 
									maxlength="10" 
									value="<?php echo $item["dt_indexacao"] ?>"
									style="width:80px;"
									OnKeyDown="mascaraData(this,event);"
									/><script>MaskInput( document.getElementById('dt_indexacao_<?php echo $id; ?>'), "99/99/9999" );</script>
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
							<td><?php echo $item["ds_processo"]; ?></td>
							<td><?php echo $item["nr_folha"]; ?></td>
							<td><?php echo (trim($item['arquivo_nome']) != "" ? '<a href="'.base_url().'up/protocolo_digitalizacao_'.intval($item["cd_documento_protocolo"]).'/'.$item['arquivo'].'" target="_blank">'.$item['arquivo'].'</a>' : ""); ?></td>
						</tr>

					<? endwhile; ?>
				</tbody>

			</table>

			<br />
			<center>
				<?php
					if(trim($_REQUEST['t']) == "D")
					{
						echo '<input type="button" class="botao" name="down_button" id="down_button" style="height:30px;" value="Download arquivos" onclick="downDocs('.intval($_REQUEST['cd']).');" />';
					}
				?>
				<input type="button" class="botao" name="salvar_button" id="salvar_button" style="width:100px;height:30px;" value="Salvar" onclick="salvar();" />
				<input type="button" class="botao" name="salvar_continuar_button" id="salvar_continuar_button" style="height:30px;" value="Salvar e Confirmar" onclick="salvar_e_confirmar();" />
				<input type="button" class="botao_disabled" name="voltar_button" id="voltar_button" style="height:30px; width:100px;" value="Voltar" onclick="document.location.href='<?php echo base_url(); ?>/index.php/ecrm/protocolo_digitalizacao'" />
			</center>

		</div>

		<div id="resultado" style="display:none;"></div>

 <?php endif; ?>

 </form>
			</div>
			<br />
	</div>

</div>
 
<script>
	function downDocs(cd_protocolo)
	{
		window.open('<?php echo base_url(); ?>/index.php/ecrm/protocolo_digitalizacao/zip_docs/'+cd_protocolo, '_blank', 'width=100,height=100,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0');
	}
</script>
 
 </BODY>
</HTML>