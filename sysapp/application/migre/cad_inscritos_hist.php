<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/ePrev.DAL.DBConnection.php');
   include_once('inc/class.TemplatePower.inc.php');
   
/*
 * 	TODO: 14732.516 - VALIDAR DOCUMENTOS - ENVIAR PARA PRODUÇÃO
 */
class controle_projetos_cad_inscritos_hist {
	
	private $rg_and_cid_is_valid = false;
	private $pedido_inscricao_is_valid = false;
	private $dal;

	private $CD_EMPRESA = 7;
	private $CD_DOC_RG_E_CIC = 1;
	private $CD_DOC_PEDIDO_INSCRICAO = 225;

	function controle_projetos_cad_inscritos_hist($_db){
		$this->dal = new DBConnection();
		$this->dal->loadConnection($_db);
	}
	
	public function verificarDocumentos($_cd_registro_empregado){

		$this->dal->createQuery( "

			SELECT cd_doc, 
                   nro_via, 
                   obrigatorio, 
                   to_char(dt_entrega, 'dd/mm/yyyy') as dt_entrega, to_char(dt_inclusao, 'dd/mm/yyyy') as dt_inclusao
              FROM expansao.registros_documentos
             WHERE cd_registro_empregado = ::cd_registro_empregado and cd_empresa = ::cd_empresa

        " );

        $this->dal->setAttribute( "::cd_registro_empregado", $_cd_registro_empregado );
        $this->dal->setAttribute( "::cd_empresa",            $this->CD_EMPRESA );

		$result = $this->dal->getResultset();

		if ( $this->dal->getAffectedRowsCount()>0 ) {
			while ( $reg=pg_fetch_array($result) ) {
				if ($reg['cd_doc'] == $this->CD_DOC_RG_E_CIC){
					$this->rg_and_cid_is_valid = true;
				}
				elseif ($reg['cd_doc'] == $this->CD_DOC_PEDIDO_INSCRICAO) {
					$this->pedido_inscricao_is_valid = true;
				}
			}
		}

	}

	public function getRG_and_CIC_is_Valid(){
		return $this->rg_and_cid_is_valid;
	} 

	public function getPedido_Inscricao_is_Valid(){
		return $this->pedido_inscricao_is_valid;
	} 

}
$thisPage = new controle_projetos_cad_inscritos_hist($db);


   $tpl = new TemplatePower('tpl/tpl_cad_inscritos_hist.html');

   $tpl->prepare();
   
	// *** abas
	$abas[] = array('aba_identificacao', 'Identificação', false, 'ir_aba_ident()');
	$abas[] = array('aba_contato', 'Contato', false, 'ir_aba_cont()');
	$abas[] = array('aba_anexo', 'Anexo', false, 'ir_aba_anx()');
	$abas[] = array('aba_historico', 'Histórico', true, 'ir_aba_hist()');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end('') );
	$tpl->assignGlobal( 'link_lista', site_url("cadastro/avaliacao_cargo") );
	// *** abas
   
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	
	$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   $tpl->newBlock('cadastro');
   // ----------------------------------------------------- se é um novo evento, TR vem com 'I'
		if ($tr == 'U') {
			$n = 'U';
		}
		else {
			$n = 'I';
		}
		$tpl->assign('insere', $n);

   if ( isset($c) ) {
        $sql =   " ";
		$sql = $sql . " select 	cd_registro_empregado, nome, endereco, cep, complemento_cep, cd_pacote, opt_irpf, ";
		$sql = $sql . "			TO_CHAR(dt_documentacao_confirmada, 'DD/MM/YYYY HH24:MI:SS') as dt_documentacao_confirmada,  ";
		$sql = $sql . "			to_char(dt_inscricao, 'dd/mm/yyyy') as dt_inscricao, ";
		$sql = $sql . "			to_char(dt_senge_confirmado, 'dd/mm/yyyy') as dt_senge_confirmado, ";
		$sql = $sql . "			to_char(dt_nascimento, 'dd/mm/yyyy') as dt_nascimento, ";
		$sql = $sql . "			to_char(dt_email_confirmado, 'dd/mm/yyyy') as dt_email_confirmado ";
		$sql = $sql . "  from 	expansao.inscritos		";
		$sql = $sql . "  where cd_registro_empregado	= $c ";
        $rs  = pg_query($db, $sql);
        $reg = pg_fetch_array($rs);
		
		$tpl->assignGlobal('cd_registro_empregado', $reg['cd_registro_empregado']);
        $tpl->assign('nome', $reg['nome']);
		$tpl->assign('dt_ok_dap', $reg['dt_documentacao_confirmada']);
		$tpl->assign('dt_inscricao', $reg['dt_inscricao']);
		$tpl->assign('dt_nascimento', $reg['dt_nascimento']);
		$tpl->assign('endereco', $reg['endereco']);
		$tpl->assign('cep', $reg['cep']);
		$tpl->assign('compl_cep', $reg['complemento_cep']);
		$tpl->assign('opt_pacote', $reg['cd_pacote']);
		
		if(trim($reg['dt_documentacao_confirmada']) != "")
		{
			$tpl->assign('fl_confirma', 'disabled');
		}
		
		if ($reg['cd_pacote'] == 1) {
			$tpl->assign('desc_pacote', 'Internet');
		}
		else {
			$tpl->assign('desc_pacote', 'Correios');
		}
		$tpl->assign('opt_irpf', $reg['opt_irpf']);
		if ($reg['opt_irpf'] == 1) {
			$tpl->assign('desc_irpf', 'Optou pela tabela regressiva');
		}
		elseif ($reg['opt_irpf'] == 2) {
			$tpl->assign('desc_irpf', 'NÃO optou pela tabela regressiva');
		}
		$tpl->assign('dt_senge', $reg['dt_senge_confirmado']);
		$tpl->assign('dt_email', $reg['dt_email_confirmado']);
		
		$thisPage->verificarDocumentos( $c );
		
		$tpl->assign( 'rg_and_cic_is_valid', 
		               ( $thisPage->getRG_and_CIC_is_Valid() )
		               ? "S"
		               : "N"
		            );
        $tpl->assign( 'pedido_inscricao_is_valid', 
		               ( $thisPage->getPedido_Inscricao_is_Valid() )
		               ? "S"
		               : "N"
		            );

   }

//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>