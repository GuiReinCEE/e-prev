<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
//-------------------------------------------------------   
	$tpl = new TemplatePower('tpl/tpl_cad_mailing.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
//-------------------------------------------------------
	if ($tr == 'U') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	$tpl->assign('insere', $n);
	$tpl->assign('chkconf', $chkconf);
	$tpl->assign('cac', $cac);
	$tpl->assign('scep',$cep);
//-------------------------------------------------------
	if (isset($c))	{
        $sql =   " ";
		$sql = $sql . " select 	cd_mailing		as cd_mailing, ";
		$sql = $sql . "			nome_pessoa			as nome, ";
		$sql = $sql . "			cargo				as cargo, ";
		$sql = $sql . "			cd_cargo			as cd_cargo, ";
		$sql = $sql . "        	endereco	     	as endereco, ";
		$sql = $sql . "        	bairro		     	as bairro, ";
		$sql = $sql . "        	estado		     	as estado, ";
		$sql = $sql . "        	cd_municipio    	as cd_municipio, ";
		$sql = $sql . "        	cep		     		as cep, ";
		$sql = $sql . "			cnpj				as cnpj,	";
		$sql = $sql . "			email_1				as email, ";
		$sql = $sql . "			ddd					as ddd, ";
		$sql = $sql . "			telefone_comercial	as telefone, ";
		$sql = $sql . "			fax					as fax, ";
		$sql = $sql . "			url					as site, ";
		$sql = $sql . "			celular				as celular, ";
		$sql = $sql . "        	cd_emp_inst    		as cd_emp_inst, ";
		$sql = $sql . "        	cd_empresa    		as cd_empresa, ";
		$sql = $sql . "        	cd_registro_empregado	as cd_registro_empregado, ";
		$sql = $sql . "        	seq_dependencia		as seq_dependencia, ";
		$sql = $sql . "        	cd_comunidade  		as cd_comunidade, ";
		$sql = $sql . "        	cd_com_secundaria	as cd_com_secundaria, ";
		$sql = $sql . "		 	numero				as numero, ";
		$sql = $sql . "		 	complemento			as complemento ";
		$sql = $sql . "  from 	expansao.mailing	";
		$sql = $sql . "  where 	cd_mailing			= $c ";
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$cod_evento = $reg['cd_evento'];
		$cod_projeto = $reg['cd_projeto'];
		$cod_tipo_evento = $reg['cd_tipo_evento'];
		$cod_data_referencia = $reg['cd_dt_referencia'];
		$tipo_acao = $reg['tipo_acao'];
		$tpl->assign('codigo', $reg['cd_mailing']);
        $tpl->assign('nome', $reg['nome']);
		$tpl->assign('cargo', $reg['cargo']);
		$tpl->assign('logradouro', $reg['endereco']);
		$tpl->assign('numero', $reg['numero']);
		$tpl->assign('complemento', $reg['complemento']);
		$tpl->assign('bairro', $reg['bairro']);
		$tpl->assign('estado', $reg['estado']);
		$tpl->assign('cep', $reg['cep']);
		$tpl->assign('cep1', substr($reg['cep'],0,5));
		$tpl->assign('cep2', substr($reg['cep'],5,3));
		$tpl->assign('ddd', $reg['ddd']);
		$tpl->assign('telefone', $reg['telefone']);
		$tpl->assign('ramal', $reg['ramal']);
		$tpl->assign('celular',	$reg['celular']);
		$tpl->assign('fax', $reg['fax']);
		$tpl->assign('email', $reg['email']);
		$tpl->assign('site', $reg['site']);
		$tpl->assign('cnpj', $reg['cnpj']);
		$tpl->assign('cd_empresa', $reg['cd_empresa']);
		$tpl->assign('cd_cargo', $reg['cd_cargo']);
		$tpl->assign('cd_registro_empregado', $reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
		$tpl->assign('me', 'Modificar empresa');
		$tpl->assign('tc', 'Trocar cargo');
		$tpl->assign('ec', 'Editar cargo');
		$v_estado = $reg['estado'];
		$v_cd_municipio = $reg['cd_municipio'];
		$v_cd_cargo = $reg['cd_cargo'];
		$v_cd_emp_inst = $reg['cd_emp_inst'];
		$v_cd_empresa = $reg['cd_empresa'];
		$v_cd_comunidade = $reg['cd_comunidade'];
		$v_cd_com_secundaria = $reg['cd_com_secundaria'];
   }
   	else {
		$v_cd_emp_inst = $cd_emp_inst;
		$tpl->assign('estado', 'RS');
		$sql = " select max(cd_mailing) as cd_mailing ";
		$sql = $sql . " from   expansao.mailing ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', ($reg['cd_mailing'] + 1));
		$cd_mailing = $reg['cd_mailing'] + 1;
		$v_cd_comunidade = $cac;
	}
// ---------------------------------------------------------------------------------- Atualiza o nome da cidade na tela
	if ($v_cd_municipio != '') {
		$sql = "select cd_municipio_ibge, cd_microregiao, cd_macroregiao, nome_cidade, sigla_uf, cd_corede from expansao.cidades where sigla_uf = '".$v_estado."' and cd_municipio_ibge = ".$v_cd_municipio;
		$rs = pg_exec($db, $sql);
		if ($reg = pg_fetch_array($rs)) {
			$tpl->assign('nome_cidade', $reg['nome_cidade']);		
		}
	}
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
//----------------------------------------------- Empresas/instituições:
	if ($v_cd_emp_inst != '') {
		$sql = "select nome_empresa_entidade from expansao.empresas_instituicoes where cd_emp_inst = ".$v_cd_emp_inst;
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->assign('cd_emp_inst', $v_cd_emp_inst);
			$tpl->assign('empresa', $reg['nome_empresa_entidade']);
		}
	}
//----------------------------------------------- Cargos:
	if ($v_cd_cargo != '') {
		$sql = "select cd_cargo, descricao from expansao.cargos_mailing where cd_cargo = ".$v_cd_cargo;
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->assign('cd_cargo', $reg['cd_cargo']);
			$tpl->assign('cargo', $reg['descricao']);
		}
	}
//----------------------------------------------- Emails cadastrados:
	if ($c != '') {
		$sql = "select cd_mailing, cd_email, email, tipo, 
				to_char(dt_inclusao, 'dd/mm/yyyy') as dt_inclusao
				from expansao.mailing_email where dt_exclusao is null and cd_mailing = ".$c;
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('email_cadastrado');		
			$tpl->assign('codigo', $c);
			$tpl->assign('cd_email', $reg['cd_email']);
			$tpl->assign('email', $reg['email']);
			$tpl->assign('dt_inclusao', $reg['dt_inclusao']);		
		}
	}
//----------------------------------------------- Lista de Estados:
	if ($v_estado == '') { $v_estado = 'RS';}
	$sql = "select cd_ibge, sigla, nome from expansao.estados order by nome";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('estado');
		$tpl->assign('cd_estado', $reg['sigla']);
		$tpl->assign('estado', $reg['sigla']." - ".$reg['nome']);
		if ($reg['sigla'] == $v_estado) { 
			$tpl->assign('sel_estado', 'selected'); 
		}
	}
//----------------------------------------------- Lista de Cidades:
	if	($v_cd_municipio == '') { $v_cd_municipio = 14902; }
	$sql = "select cd_municipio_ibge, cd_microregiao, cd_macroregiao, nome_cidade from expansao.cidades where sigla_uf = UPPER('".$v_estado."') order by nome_cidade";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cidade');
		$tpl->assign('cd_cidade', $reg['cd_municipio_ibge']);
		$tpl->assign('nome_cidade', $reg['nome_cidade']);
		if ($reg['cd_municipio_ibge'] == $v_cd_municipio) { 
			$tpl->assign('sel_cidade', 'selected'); 
		}
	}
//----------------------------------------------- Lista de Patrocinadoras:
	$sql = "select cd_empresa, descricao from patrocinadoras p, listas l where l.categoria = 'PATR' and l.codigo::integer = p.cd_empresa order by nome_empresa";
	$tpl->newBlock('patrocinadora');
	$tpl->assign('cd_patrocinadora', '');
	$tpl->assign('nome_patrocinadora', '');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('patrocinadora');
		$tpl->assign('cd_patrocinadora', $reg['cd_empresa']);
		$tpl->assign('nome_patrocinadora', $reg['descricao']);
		if ($reg['cd_empresa'] == $v_cd_empresa) { 
			$tpl->assign('sel_patrocinadora', 'selected'); 
		}
	}
//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();
   require_once('inc/ajaxobject.php');	
?>