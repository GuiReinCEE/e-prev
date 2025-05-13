<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_empresas_contatos.html');

    // -----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

    // -----------------------------------------------
	$tpl->newBlock('cadastro');
	if (isset($c))	{
		$sql =        " select 	cd_emp_inst, nome_empresa_entidade, cnpj, endereco, cep, ddd, telefone_comercial, estado,  ";
		$sql = $sql . " 		fax, url, cd_municipio, bairro, email, cd_ramo, cd_porte, num_funcionarios, cd_segmento, relacionamento ";
		$sql = $sql . " from 	expansao.empresas_instituicoes ";
		$sql = $sql . " where 	cd_emp_inst = $c ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_emp_inst']);
		$tpl->assign('cd_empresa', $c);
		$tpl->assign('nome_empresa_entidade', $reg['nome_empresa_entidade']);
	} else {
		$tpl->assign('insere', 'I');
	}

    // ----------------------------------------------- Lista de Pessoas:
	if (isset($c))	{
		$tpl->newBlock('informacoes_adicionais');	
		$tpl->assign('cor_fundo1', $v_cor_fundo1);
		$tpl->assign('cor_fundo2', $v_cor_fundo2);
		$tpl->assign('cac',$cac);
		$tpl->assign('cd_empresa', $c);
		$tpl->assign('codigo', $c);
		$sql = "SELECT 	cd_contato, texto_acomp, 
				TO_CHAR(dt_contato, 'dd/mm/yyyy') AS dt_contato_ed, dt_contato 
				FROM 	expansao.contatos_empresa
				WHERE 	cd_empresa = $c AND dt_exclusao IS NULL 
				ORDER 	BY dt_contato desc";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('contato');
			if ($l == 'P') {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
				$l = 'I';
			} else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$l = 'P';
			}
			$tpl->assign('data', $reg['dt_contato_ed']);
			$tpl->assign('descrica', $reg['texto_acomp']);
			$tpl->assign('cd_empresa', $c);
			$tpl->assign('cd_contato', $reg['cd_contato']);
		}
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
    require_once('inc/ajaxobject.php');	
?>