<?
//   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_imprime_inscricao.html');
//----------------
	$sql =        " select email ";
	$sql = $sql . " from   	projetos.eventos ";
	$sql = $sql . " where  	cd_evento = 14 ";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
 	echo '<script language="JavaScript1.2">alert("' . $reg['email'] . '");</script>';
//----------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$tpl->assign('cd_edicao', $ed);
	$sql =        " select 	nome, cpf, rg, emissor, cd_registro_empregado, sexo, cd_instituicao, cd_pacote, ";
	$sql = $sql . "        	cd_agencia, conta_bco, endereco, bairro, /*nome_cidade, descricao_grau_instrucao, */";
	$sql = $sql . "        	uf, cep, complemento_cep, ddd, telefone, email, nome_mae, nome_pai, opt_irpf, ";
	$sql = $sql . "        	to_char(dt_emissao, 'DD/MM/YYYY') as dt_emissao, ";
	$sql = $sql . "        	to_char(dt_inscricao, 'DD/MM/YYYY') as dt_inscricao, ";
	$sql = $sql . "        	to_char(dt_nascimento, 'DD/MM/YYYY') as dt_nascimento ";
	$sql = $sql . " from   	expansao.inscritos i/*, expansao.cidades c, grau_instrucaos g */";
	$sql = $sql . " where  	cd_registro_empregado   = $n and cd_empresa = 7";
	/*$sql = $sql . "		and	cd_municipio_ibge::bigint = cidade::bigint  ";
	$sql = $sql . "		and	c.sigla_uf = i.uf  ";
	$sql = $sql . "		and	i.cd_grau_instrucao = g.cd_grau_de_instrucao ";*/
	
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$date = date("d/m/Y");
	$opt_irpf = $reg['opt_irpf'];
	
	if($_REQUEST['n'] > 10)
	{
		$tpl->assign('dt_hoje',  $date);
	}
	
	$tpl->assign('nome', $reg['nome']);
	$tpl->assign('cpf', $reg['cpf']);
	$tpl->assign('rg', $reg['rg']);
	$tpl->assign('orgao_rg', $reg['emissor']);
	$tpl->assign('dt_emissao', $reg['dt_emissao']);		 
	$tpl->assign('dt_inclusao',  $reg['dt_inscricao']);
	$tpl->assign('re', $reg['cd_registro_empregado']);		 
	$tpl->assign('dt_implementacao', $reg['dt_implementacao']);		 
	$tpl->assign('cd_inscricao', $n);
	$tpl->assign('dt_nascimento', $reg['dt_nascimento']);		 
	$tpl->assign('sexo', $reg['sexo']);		 
	$tpl->assign('banco', $reg['cd_instituicao']);
	$tpl->assign('num_agencia', $reg['cd_agencia']);
	$tpl->assign('conta', $reg['conta_bco']);
	$tpl->assign('logradouro', $reg['endereco']);
	$tpl->assign('cidade', $reg['nome_cidade']);
	$tpl->assign('bairro', $reg['bairro']);
	$tpl->assign('uf', $reg['uf']);
	$tpl->assign('cep', $reg['cep'] . "-" . $reg['complemento_cep']);
	$tpl->assign('ddd', $reg['ddd']);
	$tpl->assign('telefone', $reg['telefone']);
	$tpl->assign('email', $reg['email']);
	$tpl->assign('filiacao', $reg['nome_mae'] . " / " . $reg['nome_pai']);
	$tpl->assign('grau_instrucao', $reg['descricao_grau_instrucao']);
// ----------------------------------------------------------
//	$tpl->newBlock('conteudo2');
	if ($reg['cd_pacote'] == 1)
	{
		$tpl->assign('opt_internet', 'X');
	}
	else 
	{
		$tpl->assign('opt_correio', 'X');
	}
	$sql =        " SELECT descricao, preco ";
	$sql = $sql . "   FROM pacotes p ";
	$sql = $sql . "  WHERE cd_plano = 7 ";
	$sql = $sql . "	   AND cd_empresa = 7 ";
	$sql = $sql . "	   AND tipo_cobranca = 'I' ";
	$sql = $sql . "	   AND dt_inicio = (select max(dt_inicio) from pacotes p, expansao.inscritos i where p.cd_empresa = 7 and p.cd_plano = 7 and p.cd_plano = i.cd_plano and p.cd_empresa = i.cd_empresa  and date_trunc('month', dt_inicio) = date_trunc('month', dt_inscricao)) ";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->assign('desc_internet', $reg['descricao']);		 
	$tpl->assign('custo_internet', $reg['preco']);
	$vl_adm = $reg['preco'];
// ----------------------------------------------------------
	$sql =        " select 	descricao, preco ";
	$sql = $sql . " from   	pacotes p ";
	$sql = $sql . " where  	cd_plano = 7 ";
	$sql = $sql . "		and	cd_empresa = 7 ";
	$sql = $sql . "		and	tipo_cobranca = 'C' ";
	$sql = $sql . "		and dt_inicio = (select max(dt_inicio) from pacotes p, expansao.inscritos i where p.cd_empresa = 7 and p.cd_plano = 7 and p.cd_plano = i.cd_plano and p.cd_empresa = i.cd_empresa  and date_trunc('month', dt_inicio) = date_trunc('month', dt_inscricao))";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->assign('desc_correio', $reg['descricao']);		 
	$tpl->assign('custo_correio', $reg['preco']);
// ----------------------------------------------------------
	$sql =        " select 	conteudo, titulo ";
	$sql = $sql . " from   	projetos.conteudo_site p ";
	$sql = $sql . " where  	cd_site = 1 ";
	if ($opt_irpf == 1)
	{
		$sql = $sql . "		and	cd_materia = 32 ";
	}
	else
	{
		$sql = $sql . "		and	cd_materia = 33 ";
	}
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->assign('texto_mari', $reg['conteudo']);
// ----------------------------------------------------------
	$sql =        " select 	nome, percentual ";
	$sql = $sql . " from   	expansao.peculio p ";
	$sql = $sql . " where  	cd_empresa = 7 ";
	$sql = $sql . "		and	cd_registro_empregado = $n ";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('peculio');
		$tpl->assign('nome_peculio', $reg['nome']);		 
		$tpl->assign('percentual', $reg['percentual']);
	}

	
	
	

	
	$fl_debito = true;
	if($_REQUEST['n'] > 4)
	{
		$qr_select = "
						SELECT dcc.banco, 
						       dcc.agencia, 
							   dcc.conta, 
							   dcc.vlr_debito
                          FROM expansao.debito_conta_contribuicao dcc
						 WHERE dcc.cd_empresa = 7
						   AND dcc.cd_registro_empregado = ".$_REQUEST['n']."
						   AND dcc.seq_dependencia       = 0
						   AND dcc.num_seq = (SELECT MIN(dcc1.num_seq)
						                        FROM expansao.debito_conta_contribuicao dcc1
											   WHERE dcc1.cd_empresa = dcc.cd_empresa
											     AND dcc1.cd_registro_empregado = dcc.cd_registro_empregado
												 AND dcc1.seq_dependencia       = dcc.seq_dependencia)
		             ";
		$ob_resul = pg_exec($db, $qr_select);
		$ar_reg = pg_fetch_array($ob_resul);
		
		
		if(trim($ar_reg['vlr_debito']) != "")
		{
			$vl_valor = number_format(($ar_reg['vlr_debito'] + $vl_adm),2,',','');
			$nr_banco = $ar_reg['banco'];
			$nr_agencia = $ar_reg['agencia'];
			$nr_conta = $ar_reg['conta'];			
		}
		else
		{
			$fl_debito = false;
		}
	}
	else
	{
		$nr_banco = "BANRISUL";
		$nr_agencia = "_____________________________";
		$nr_conta = "_____________________________";
		$vl_valor = "_________________";
		
	}
	
	$texto_debito = "
<BR>	
<div style='text-align:center; width:100%;'><STRONG>AUTORIZAÇÃO PARA DÉBITO EM CONTA</STRONG></div>
<P>
Autorizo a Fundação CEEE de Seguridade Social a debitar na conta corrente do BANRISUL abaixo indicada, no primeiro dia útil de cada mês, o valor de R$ ".$vl_valor.", referente a contribuição do plano SENGE Previdência.  
<br>
Estou ciente de que não ocorrendo o débito em conta do valor autorizado, efetuarei o pagamento através de documento de arrecadação.
<BR>
O débito em conta corrente autorizado será sempre no primeiro dia útil de cada mês.
<BR>
A primeira contribuição para o plano SENGE Previdência deverá ser efetuada via documento de arrecadação.
<BR>
<STRONG>Banco: ".$nr_banco."<BR><BR>Agência: ".$nr_agencia."<BR><BR>Conta: ".$nr_conta."<STRONG>
<BR><BR>
<STRONG>Local/Data:_______________________,</STRONG> ___/___/______ <STRONG>Assinatura do Requerente:</STRONG>  _________________________ </P>
";
	if($fl_debito)
	{
		$tpl->assignGlobal('texto_debito', $texto_debito);
	}
	
	
	
	
	
	
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
// ----------------------------------------------------------
	function convdata_br_iso($dt) {
		// Pressupõe que a data esteja no formato DD/MM/AAAA
		// A melhor forma de gravar datas no PostgreSQL é utilizando 
		// uma string no formato DDDD-MM-AA. Esta função justamente 
		// adequa a data a este formato
		$d = substr($dt, 0, 2);
		$m = substr($dt, 3, 2);
		$a = substr($dt, 6, 4);
		return $a.'-'.$m.'-'.$d;
	}
?>