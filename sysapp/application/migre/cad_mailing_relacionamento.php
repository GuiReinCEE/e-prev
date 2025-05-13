<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
//-------------------------------------------------------   
	$tpl = new TemplatePower('tpl/tpl_cad_mailing_relacionamento.html');
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
		$sql = $sql . "        	cd_comunidade  		as cd_comunidade, ";
		$sql = $sql . "        	cd_com_secundaria	as cd_com_secundaria, ";
		$sql = $sql . "		 	flag_confirmado		as confirmado, ";
		$sql = $sql . "		 	numero				as numero, ";
		$sql = $sql . "		 	email_1				as email, ";
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
		$tpl->assign('cor_fundo1', $v_cor_fundo1);
		$tpl->assign('cor_fundo2', $v_cor_fundo2);
		$tpl->assign('cor_fundo3', $v_cor_fundo3);
		$tpl->assign('cor_fundo4', $v_cor_fundo4);


		$v_email = $reg['email'];
		$v_estado = $reg['estado'];
		$v_cd_municipio = $reg['cd_municipio'];
		$v_cd_cargo = $reg['cd_cargo'];
		$v_cd_emp_inst = $reg['cd_emp_inst'];
		$v_cd_empresa = $reg['cd_empresa'];
		$v_cd_comunidade = $reg['cd_comunidade'];
		$v_cd_com_secundaria = $reg['cd_com_secundaria'];
		if ($reg['etiqueta'] == 'S') { $tpl->assign('check_etiqueta', 'checked'); }
	  	if ($reg['confirmado'] == 'S') { $tpl->assign('check_presenca_confirmada', 'checked'); }
	  	if ($reg['mala_direta_enviada'] == 'S')	{ $tpl->assign('check_mala_direta_enviada', 'checked'); }
	  	if ($reg['pagamento_efetuado'] == 'S') 	{ $tpl->assign('check_pagamento_efetuado', 'checked'); }
	  	if ($reg['email_enviado'] == 'S') { $tpl->assign('check_email_enviado', 'checked'); }
	  	if ($reg['reimprimir_certificado'] == 'S') { $tpl->assign('check_reimprimir_certificado', 'checked'); }
	  	if ($reg['presenca'] == 'S') { $tpl->assign('check_presenca', 'checked'); }
//----------------------------------------------- Eventos desta pessoa:
		$sql = "select 	e.cd_evento, e.nome, to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as data_ev, 
						me.presenca_confirmada, me.mala_direta_enviada, me.email_enviado, me.seq, 
						me.pagamento_efetuado, me.certificado_impresso, me.presente_no_evento, 
						to_char(dt_atualizacao, 'DD/MM/YYYY HH24:MI') as dt_atualizacao
				from   	projetos.eventos_institucionais e, expansao.mailing_eventos me
				where  	e.cd_evento = me.cd_evento and e.dt_exclusao is null and me.dt_exclusao is null and cd_mailing = $c order by e.dt_inicio desc ";
		$rs = pg_query($sql);
		while ($reg = pg_fetch_object($rs)) 
		{
			$tpl->newBlock('evento');
			if ($f == 'P') {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
				$f = 'I';
			} else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
				$f = 'P';
			}
			$tpl->assign('evento', $reg->nome);			
			if ($reg->presenca_confirmada == 'S') { $tpl->assign('chk_confirmado', 'checked'); }
			if ($reg->mala_direta_enviada == 'S') { $tpl->assign('chk_mala', 'checked'); }
			if ($reg->email_enviado == 'S') { $tpl->assign('chk_email', 'checked'); }	
			if ($reg->pagamento_efetuado == 'S') { $tpl->assign('chk_pagamento', 'checked'); }	
			if ($reg->certificado_impresso == 'S') { $tpl->assign('chk_certificado', 'checked'); }
			if ($reg->presente_no_evento == 'S') { $tpl->assign('chk_presenca', 'checked'); }
			$tpl->assign('cor_fundo1', $v_cor_fundo1);
			$tpl->assign('cor_fundo2', $v_cor_fundo2);
			$tpl->assign('cd_mailing', $c);
			$tpl->assign('cd_evento', $reg->cd_evento);
			$tpl->assign('seq', $reg->seq);
			$tpl->assign('dt_atualizacao', $reg->dt_atualizacao);
		}
//----------------------------------------------- Comunidades desta pessoa:
		$sql = "select 	mc.cd_comunidade, l.descricao,  
						mc.seq, to_char(mc.dt_alteracao, 'DD/MM/YYYY HH24:MI') as dt_alteracao
				from    listas l, expansao.mailing_comunidades mc
				where  	l.codigo = mc.cd_comunidade and mc.dt_exclusao is null and cd_mailing = $c order by l.descricao ";
		$rs = pg_query($sql);
		while ($reg = pg_fetch_object($rs)) 
		{
			$tpl->newBlock('comun');
			if ($f == 'P') {
				$tpl->assign('cor_fundo', $v_cor_fundo3);
				$f = 'I';
			} else {
				$tpl->assign('cor_fundo', $v_cor_fundo4);
				$f = 'P';
			}
			$tpl->assign('comunidade', $reg->descricao);			
			$tpl->assign('cor_fundo3', $v_cor_fundo3);
			$tpl->assign('cor_fundo4', $v_cor_fundo4);
			$tpl->assign('cd_mailing', $c);
			$tpl->assign('cd_comunidade', $reg->cd_comunidade);
			$tpl->assign('seq', $reg->seq);
			$tpl->assign('dt_atualizacao', $reg->dt_atualizacao);
		}
//----------------------------------------------- Emails enviados para esta pessoa:
		if ($v_email != '') {
			$sql = "select  cd_email, assunto,   
							to_char(dt_envio, 'DD/MM/YYYY HH24:MI') as dt_envio
					from    projetos.envia_emails
					where  	para = '".$v_email."' order by cd_email desc ";
			$rs = pg_query($sql);
			while ($reg = pg_fetch_object($rs)) 
			{
				$tpl->newBlock('email');
				if ($f == 'P') {
					$tpl->assign('cor_fundo', $v_cor_fundo1);
					$f = 'I';
				} else {
					$tpl->assign('cor_fundo', $v_cor_fundo2);
					$f = 'P';
				}
				$tpl->assign('assunto', $reg->assunto);			
				$tpl->assign('dt_envio', $reg->dt_envio);
				$tpl->assign('cd_email', $reg->cd_email);
			}
		}
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
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
//----------------------------------------------- Comunidades:
	$sql = "select codigo, descricao from listas where categoria = 'CACS' order by descricao";
	$tpl->newBlock('comunidade');
	$tpl->assign('cd_comunidade', '');
	$tpl->assign('nome_comunidade', '');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('sel_comunidade');
		$tpl->assign('cd_comunidade', $reg['codigo']);
		$tpl->assign('nome_comunidade', $reg['descricao']);

	}
//----------------------------------------------- Eventos institucionais:
	if ($c != '') {
	$sql = "select cd_evento, nome, to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as data_ev 
			from   projetos.eventos_institucionais
			where dt_exclusao is null and cd_evento not in 
			(select distinct cd_evento from expansao.mailing_eventos where cd_mailing = $c and dt_exclusao is null) order by dt_inicio desc";
	} else {
	$sql = "select cd_evento, nome, to_char(dt_inicio, 'DD/MM/YYYY HH24:MI') as data_ev 
			from   projetos.eventos_institucionais
			where dt_exclusao is null order by dt_inicio desc";
	}
	$rs = pg_exec($db, $sql);
	$tpl->newBlock('sel_evento');
	$tpl->assign('cd_evento', '');
	$tpl->assign('nome_evento', 'Selecione...');
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('sel_evento');
		$tpl->assign('cd_evento', $reg['cd_evento']);
		$tpl->assign('nome_evento', $reg['nome']);
	}
//-------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
	require_once('inc/ajaxobject.php');
?>