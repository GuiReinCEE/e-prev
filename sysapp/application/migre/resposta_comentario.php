<?
        include_once("inc/sessao.php");
        include_once("inc/conexao.php");
        include_once("inc/funcoes.php");
        include_once("inc/class.TemplatePower.inc.php"); 
    header( 'location:'.base_url().'index.php/ecrm/operacional_contato/detalhe/'.$codigo);

   $sql = "";
   $sql = $sql . " SELECT  codigo,   ";
   $sql = $sql . "         nome,     ";
   $sql = $sql . "         endereco, ";
   $sql = $sql . "         bairro,   ";
   $sql = $sql . "         cep,      ";
   $sql = $sql . "         cidade,   ";
   $sql = $sql . "         estado,   ";
   $sql = $sql . "         ddd,      ";
   $sql = $sql . "         telefone, ";
   $sql = $sql . "         ramal,    ";
   $sql = $sql . "         fax,      ";
   $sql = $sql . "         email,    ";
   $sql = $sql . "         comentario,";
   $sql = $sql . "         resposta,";   
   $sql = $sql . "         to_char(data, 'dd/mm/yyyy') as data,     ";
   $sql = $sql . "         hora      , ";
   $sql = $sql . "         case when resposta is null then 'N' else 'S' end as respondido, ";   
   $sql = $sql . "		   cd_atendimento, ";
   $sql = $sql . "		   empresa, ";
   $sql = $sql . "		   re ";
   $sql = $sql . " FROM    Contatos_internet";
   $sql = $sql . " WHERE   codigo  =  ".$codigo;
 //  echo $sql;
   $rs = pg_exec($db,$sql);
    
	if (pg_numrows($rs) > 0)
	{
        $reg = pg_fetch_array($rs);

        $mensagem = '
<BR>
Gerência de Atendimento ao Participante<BR>
Rua dos Andradas, 702 Porto Alegre- RS CEP 90020-004<BR>
Ligue grátis: 0800 51 2596<BR>
Atendimento de segunda a sexta, das 08 às 17 horas.<BR>
';
        $mensagem = $mensagem .'<br><br>--- Em ' . $reg['data'] .' às '. $reg['hora'].', ' . $reg['nome'] . ' escreveu: ---<br><br>' . str_replace('\r','<br>',$reg['comentario']) . '<br><br><br>Mensagem nº ' . $reg['codigo'];
        
		$tpl = new TemplatePower('tpl/tpl_resposta_comentario.html');
        $tpl->prepare();
		// --------------------------------------------------------- inicialização do skin das telas:
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);

        $tpl->assign('atendente',$ATENDENTE);
        $tpl->assign('codigo',$reg['codigo']);
        $tpl->assign('endereco',$reg['endereco']);
        $tpl->assign('bairro',$reg['bairro']);
        $tpl->assign('cep',$reg['cep']);
        $tpl->assign('cidade',$reg['cidade']);		
        $tpl->assign('estado',$reg['estado']);
        $tpl->assign('fone',$reg['ddd'].$reg['telefone']);		
        $tpl->assign('ramal',$reg['ramal']);
        $tpl->assign('fax',$reg['fax']);
        $tpl->assign('nome',$reg['nome']);
        $tpl->assign('email',$reg['email']);
        $tpl->assign('data',$reg['data']);
        $tpl->assign('hora',$reg['hora']);
		$tpl->assign('empresa',$reg['empresa']);
		$tpl->assign('re',$reg['re']);
        $tpl->assign('resposta', ($reg['respondido']=='N' ? $mensagem : 'Resposta: <br>'.$reg['resposta'].'<br>Pergunta:<br>'.$mensagem ));
		
//        $tpl->assign('resposta',(is_null($reg['data'])?$mensagem : 'Resposta: <br>'.$reg['resposta'].'<br>Pergunta:<br>'.$mensagem ));
//        $tpl->assign('resposta',$mensagem);
	}
	
//------------------------------------------------------------------------------------------- Combo Tipo do Contato:
	$sql = "SELECT * FROM listas WHERE categoria='TPCT' ORDER BY descricao";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_tipo_atendimento');
		$tpl->assign('codat', $reg['codigo']);
		$tpl->assign('nomeat', $reg['descricao']);
		$tpl->assign('chkat', ($reg['cd_atendimento'] == $cbo_tipo_atendimento ? ' selected' : ''));
	}
//-------------------------------------------------------------------------------------------
	
    $tpl->PrintToScreen();
?>