<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_empresas.html');
    
    //-----------------------------------------------   
    if(!isset($n))
    {
        $n = '';
    }
    
    if(!isset($v_estado))
    {
        $v_estado = '';
    }
    
    if(!isset($v_cd_municipio))
    {
        $v_cd_municipio = '';
    }

    if(!isset($v_cd_segmento))
    {
        $v_cd_segmento = '';
    }

    if(!isset($v_cd_porte))
    {
        $v_cd_porte = '';
    }

    if(!isset($v_relacionamento))
    {
        $v_relacionamento = '';
    }
    //-----------------------------------------------   
	
    $tpl->prepare();
	
    $tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

    // -----------------------------------------------
	$tpl->newBlock('cadastro');
	if (isset($c))
    {
		$tpl->assign('codigo', $c);
		$tpl->newBlock('dados_cadastrais');
		$sql =        " SELECT 	cd_emp_inst, nome_empresa_entidade, cnpj, endereco, cep, ddd, telefone_comercial, estado, complemento, ";
		$sql = $sql . " 		fax, url, cd_municipio, bairro, email, cd_ramo, cd_porte, num_funcionarios, cd_segmento, possui_plano, com_quem, relacionamento ";
		$sql = $sql . " FROM 	expansao.empresas_instituicoes ";
		$sql = $sql . " WHERE 	cd_emp_inst = " . $c . " ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_emp_inst']);
		$tpl->assign('nome_empresa_entidade', $reg['nome_empresa_entidade']);
		$tpl->assign('cnpj', sprintf("%014.0f", $reg['cnpj']));
		$tpl->assign('endereco', $reg['endereco']);
		$tpl->assign('complemento', $reg['complemento']);
		$tpl->assign('cep', $reg['cep']);
		$tpl->assign('ddd', $reg['ddd']);
		$tpl->assign('telefone', $reg['telefone_comercial']);
		$tpl->assign('fax', $reg['fax']);
		$tpl->assign('url', $reg['url']);		
		$tpl->assign('bairro', $reg['bairro']);
		$tpl->assign('estado', $reg['estado']);
		$tpl->assign('email', $reg['email']);
		$tpl->assign('cd_ramo', $reg['cd_ramo']);
		$tpl->assign('num_func', $reg['num_funcionarios']);
		if ($reg['possui_plano'] == 'S')
        {
			$tpl->assign('chk_possui', 'checked');
		}
        elseif ($reg['possui_plano'] == 'N')
        {
			$tpl->assign('chk_nao_possui', 'checked');
		}
        else
        {
			$tpl->assign('chk_nao_sei', 'checked');
		}
		$tpl->assign('plano_previdencia', $reg['com_quem']);
		$v_estado = $reg['estado'];
		$v_cd_municipio = $reg['cd_municipio'];
		$v_cd_ramo = $reg['cd_ramo'];
		$v_cd_porte = $reg['cd_porte'];
		$v_cd_segmento = $reg['cd_segmento'];
		$v_relacionamento = $reg['relacionamento'];
		if ($v_cd_municipio != '')
        {
			$sql = "select cd_municipio_ibge, cd_microregiao, cd_macroregiao, nome_cidade, sigla_uf, cd_corede from expansao.cidades where sigla_uf = '".$v_estado."' and cd_municipio_ibge = ".$v_cd_municipio;
			$rs = pg_query($db, $sql);
			if ($reg = pg_fetch_array($rs))
            {
				$tpl->assign('estado', $reg['sigla_uf']);	
				$tpl->assign('municipio', $reg['nome_cidade']);
				$tpl->assign('nome_cidade', $reg['nome_cidade']);		
				$tpl->assign('cd_microregiao', $reg['cd_microregiao']);
				$tpl->assign('cd_macroregiao', $reg['cd_macroregiao']);
				$v_cd_corede = $reg['cd_corede'];
			}
		}
		if ($v_cd_corede != '') {
			$sql = "select nome from expansao.coredes where cd_corede = ".$v_cd_corede;
			$rs = pg_query($db, $sql);
			if ($reg = pg_fetch_array($rs)) {
				$tpl->assign('corede', $reg['nome']);	
			}
		}
//----------------------------------------------- Ramo empresarial:
		if ($v_cd_ramo != '') {
			$sql = "select descricao from expansao.ramo_empresarial where cd_ramo = " . $v_cd_ramo;
			$rs = pg_query($db, $sql);
			if ($reg = pg_fetch_array($rs)) {
				$tpl->assign('ramo', $reg['descricao']);
			}
		}
	} 
    else 
    {
		$tpl->assign('exibir_aba_pessoas', 'display:none;');
		$tpl->assign('exibir_aba_contatos', 'display:none;');
		$tpl->assign('exibir_aba_comunidades', 'display:none;');
		$tpl->newBlock('dados_cadastrais');
		$tpl->assign('insere', 'I');
	}
//----------------------------------------------- Lista de Estados:
    if ($v_estado == '') { $v_estado = 'RS';}
	$sql = "select cd_ibge, sigla, nome from expansao.estados order by nome";
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('estado');
		$tpl->assign('cd_estado', $reg['sigla']);
		$tpl->assign('estado', $reg['sigla']." - ".$reg['nome']);
		if ($reg['sigla'] == $v_estado) { 
			$tpl->assign('sel_estado', 'selected'); 
		}
	}
//----------------------------------------------- Lista de Cidades:
//	if	($v_cd_municipio == '') { $v_cd_municipio = 14902; }
	$tpl->newBlock('cidade');
	$tpl->assign('cd_cidade', '');
	$tpl->assign('nome_cidade', 'Selecione ...');
	$sql = "select cd_municipio_ibge, cd_microregiao, cd_macroregiao, nome_cidade from expansao.cidades where sigla_uf = UPPER('".$v_estado."') order by nome_cidade";
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cidade');
		$tpl->assign('cd_cidade', $reg['cd_municipio_ibge']);
		$tpl->assign('nome_cidade', $reg['nome_cidade']);
		if ($reg['cd_municipio_ibge'] == $v_cd_municipio) { 
			$tpl->assign('sel_cidade', 'selected'); 
		}
	}
//----------------------------------------------- Lista de Segmentos:
	$sql = "select codigo, descricao from listas where categoria = 'SACS' order by descricao";
	$rs = pg_query($db, $sql);
	$tpl->newBlock('segmento');
	$tpl->assign('cd_segmento', '');
	$tpl->assign('desc_segmento', '');

	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('segmento');
		$tpl->assign('cd_segmento', $reg['codigo']);
		$tpl->assign('desc_segmento', $reg['descricao']);
		if ($reg['codigo'] == $v_cd_segmento) { $tpl->assign('sel_segmento', 'selected'); }
	}
//----------------------------------------------- Lista de Porte:
	$sql = "select codigo, descricao from listas where categoria = 'POEM' order by descricao";
	$rs = pg_query($db, $sql);
	$tpl->newBlock('porte');
	$tpl->assign('cd_porte', '');
	$tpl->assign('desc_porte', '');
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('porte');
		$tpl->assign('cd_porte', $reg['codigo']);
		$tpl->assign('desc_porte', $reg['descricao']);
		if ($reg['codigo'] == $v_cd_porte) { $tpl->assign('sel_porte', 'selected'); }
	}
//----------------------------------------------- Lista de Relacionamentos:
	$sql = "select codigo, descricao from listas where categoria = 'RREL' order by descricao";
	$rs = pg_query($db, $sql);
	$tpl->newBlock('relacionamento');
	$tpl->assign('cd_relacionamento', '');
	$tpl->assign('desc_relacionamento', '');
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('relacionamento');
		$tpl->assign('cd_relacionamento', $reg['codigo']);
		$tpl->assign('desc_relacionamento', $reg['descricao']);
		if ($reg['codigo'] == $v_relacionamento) { $tpl->assign('sel_relacionamento', 'selected'); }
	}
    // -----------------------------------------------
	pg_close($db);
    $tpl->printToScreen();	
    require_once('inc/ajaxobject.php');	
?>