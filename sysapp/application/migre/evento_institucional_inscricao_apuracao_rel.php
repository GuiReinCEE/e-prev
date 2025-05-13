<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_evento_institucional_inscricao_apuracao_rel.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### PERMISSOES ####
	if(($_SESSION['D'] != "GRI") and ($_SESSION['D'] != "GI"))
	{
		
	}
	
	$qr_sql = "
				SELECT eiav.cd_eventos_institucionais_inscricao AS cd_inscricao,
					   eii.nome,
					   eii.cd_empresa,
					   eii.cd_registro_empregado,
					   eii.seq_dependencia,
					   eii.telefone,
					   eii.email,
					   eii.endereco,
					   eii.cidade,
					   eii.cep,
					   eii.uf,
					   eii.observacao,					   
				       eea.anexo,					   
				       COUNT(*) AS qt_voto
				  FROM projetos.eventos_institucionais_apuracao_voto eiav
				  JOIN projetos.eventos_institucionais_inscricao eii
				    ON eii.cd_eventos_institucionais_inscricao = eiav.cd_eventos_institucionais_inscricao
				  LEFT JOIN projetos.evento_inscricao_anexo eea
				    ON eea.cd_eventos_institucionais_inscricao = eiav.cd_eventos_institucionais_inscricao
				 WHERE eiav.cd_eventos_institucionais_apuracao = ".$_REQUEST['cd_apuracao']."
				 GROUP BY cd_inscricao,
						  eii.nome,
						  eii.cd_empresa,
						  eii.cd_registro_empregado,
						  eii.seq_dependencia,
						  eii.telefone,
						  eii.email,
						  eii.endereco,
						  eii.cidade,
						  eii.cep,
						  eii.uf,
						  eii.observacao,					   
				          eea.anexo	
				 ORDER BY qt_voto DESC, 
				          cd_inscricao ASC
				 
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 1;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista');
		$tpl->assign('nr_posicao',  $nr_conta);
		$tpl->assign('cd_inscricao',  $ar_reg['cd_inscricao']);
		$tpl->assign('ds_nome',  $ar_reg['nome']);
		$tpl->assign('cd_empresa',  $ar_reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado',  $ar_reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia',  $ar_reg['seq_dependencia']);
		
		$contato = "Telefone: ".$ar_reg['telefone'];
		$contato.= "<BR>E-mail: ".$ar_reg['email'];
		$contato.= "<BR>Endereço: ".$ar_reg['endereco'];
		$contato.= "<BR>Cidade - UF: ".$ar_reg['cidade']." - ".$ar_reg['uf'];
		$contato.= "<BR>CEP: ".$ar_reg['cep'];
		
		$tpl->assign('contato',  $contato);
		
		//$tpl->assign('observacao',  $ar_reg['observacao']);
		$tpl->assign('observacao',  "<img src='../upload/concurso_frase_foto_2008/".$ar_reg['anexo']."' width='60%' height='60%' border='0'>");
		
		
		$tpl->assign('qt_voto',  $ar_reg['qt_voto']);
		$nr_conta++;
	}

	$tpl->printToScreen();
	pg_close($db);
?>