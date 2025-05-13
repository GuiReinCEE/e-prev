<?php
	include_once('class.SocketAbstraction.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/config.inc.php');

	$LISTNER_IP    = SKT_IP;
	$LISTNER_PORTA = SKT_PORTA;
	
	$sessao         = $_REQUEST['session_id'];
	$num_prestacoes = $_REQUEST['num_prestacoes'];
	$send = "fnc_busca_inf_concessao;$sessao;$num_prestacoes";
   
	$cn = new Socket();
	$cn->SetRemoteHost($LISTNER_IP);
	$cn->SetRemotePort($LISTNER_PORTA);
	$cn->SetBufferLength(131072);
	$cn->SetConnectTimeOut(1);
	
	if ($cn->Connect()) 
	{
		$ret = $cn->Ask($send);
		if ($cn->Error()) 
		{
			echo "Ocorreu um erro de conexão com o webservice";
		} 
		else 
		{
			// Coloca os dados na tela
			$dom = new DOMDocument("1.0", "ISO-8859-1");
			//$dom = new DOMDocument("1.0", "utf-8");
			$dom->loadXML(utf8_encode($ret)); // Rever isto, pois está enviando caracteres estranhos ao invés dos acentos.
			$campos = $dom->getElementsByTagName("fld");
			$tpl = new TemplatePower('tpl/tpl_confirma_emprestimo_dap.html');
			$tpl->prepare();
			$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
			include_once('inc/skin.php');
			//$tpl->assign('usuario', $N);
			$ip=$_SERVER['REMOTE_ADDR'];
		
			if ((substr($ip, 0, 9) == '10.63.255') or (substr($ip, 0, 9) == '10.65.255')) 
			{
				$tpl->assign('usuario', $usuario_emp);
			}
			$tpl->assign('divsao', $D);
			$tpl->assign('usuario_emp', $usuario_emp);	
			$tpl->assign('MOSTRAR_BANNER', $_REQUEST['MOSTRAR_BANNER']);	

			foreach ($campos as $nodo) 
			{
				if ($nodo->getAttribute('tp') == 'DAT') 
				{
				   $tpl->assign(strtolower($nodo->getAttribute('id')), $nodo->nodeValue);
				}
				
				switch(strtolower($nodo->getAttribute('id'))) 
				{
					case 'cd_empresa': $_EMP = $nodo->nodeValue; break;
					case 'cd_registro_empregado': $_RE = $nodo->nodeValue; break;
					case 'seq_dependencia': $_SEQ = $nodo->nodeValue; break;
					case 'origem': $origemContrato = $nodo->nodeValue; break;
					case 'forma_calculo': $forma_calculo = $nodo->nodeValue; break;
					case 'cd_instituicao': $banco = $nodo->nodeValue; break;
					case 'cd_agencia': $agencia = $nodo->nodeValue; break;
					case 'forma_pgto_fundacao':
						if($nodo->nodeValue == "BCO")
						{
							$tpl->assign('fl_frm_pg_BCO', ' selected');
						}
						else if($nodo->nodeValue == "CXA")
						{
							$tpl->assign('fl_frm_pg_CXA', ' selected');
						}						
				   break;
				}
			}
			
			if($forma_calculo == "O") ## POS-FIXADO
			{
				$tpl->assign("label_prestacao", "1ª Prest. Projetada");
			}
			
			if($forma_calculo == "P") ## PRE-FIXADO
			{
				$tpl->assign("label_prestacao", "Primeira Prest.");
			}				
		 
			if ($origemContrato == 'C' )
			{
				if($forma_calculo == "O") ## POS-FIXADO
				{
					$tpl->newBlock('blk_msg_callcenter_posfixado');
					$tpl->assign("tipo_emprestimo", "PÓS-FIXADO");
					foreach ($campos as $nodo) 
					{
						if ($nodo->getAttribute('tp') == 'DAT') 
						{
							$tpl->assign(strtolower($nodo->getAttribute('id')), $nodo->nodeValue);
						}
					}				
				}
				
				if($forma_calculo == "P") ## PRE-FIXADO
				{
					$tpl->newBlock('blk_msg_callcenter_prefixado');
					$tpl->assign("tipo_emprestimo", "PREFIXADO");
					foreach ($campos as $nodo) 
					{
						if ($nodo->getAttribute('tp') == 'DAT') 
						{
							$tpl->assign(strtolower($nodo->getAttribute('id')), $nodo->nodeValue);
						}
					}
				}
			}
			else
			{
				$tpl->newBlock('blk_msg_normal');
				if($forma_calculo == "O") ## POS-FIXADO
				{
					$tpl->assign("tipo_emprestimo", "PÓS-FIXADO");
				}
				
				if($forma_calculo == "P") ## PRE-FIXADO
				{
					$tpl->assign("tipo_emprestimo", "PREFIXADO");
				}				
			}

		
			

			
			#### BUSCA TIPO DE CONTRATO ####
			$cn2 = new Socket();
			$cn2->SetRemoteHost($LISTNER_IP);
			$cn2->SetRemotePort($LISTNER_PORTA);
			$cn2->SetBufferLength(131072);
			$cn2->SetConnectTimeOut(1);
			$send = "fnc_tp_senha_callcenter;$_EMP;$_RE;$_SEQ";
			$xml = $cn2->Ask($send);
			if ($cn2->Error()) 
			{
				echo "Ocorreu um erro: ".$cn2->Error();
			}
			$dom = new DOMDocument("1.0", "utf-8");
			$dom->loadXML(utf8_encode($xml)); 
			$campos = $dom->getElementsByTagName("fld");

			foreach ($campos as $nodo) 
			{
				if (($nodo->getAttribute('tp') == 'DAT') and ($nodo->getAttribute('id') == 'tp_senha_callcenter'))
				{
					if(($nodo->nodeValue == 2) and ($origemContrato == "N"))
					{
						$tpl->assignGlobal('fl_autenticar_participante', ' checked');
						$tpl->assignGlobal('ver_autenticar_participante', '');
						$tpl->assignGlobal('ver_autenticar_atendente', 'display:none;');
					}
					else
					{
						$tpl->assignGlobal('fl_autenticar_atendente', ' checked');				
						$tpl->assignGlobal('fl_autenticar_participante', ' disabled');				
						$tpl->assignGlobal('ver_autenticar_participante', 'display:none;');				
					}
				}
			}
		 
			//----- Preenche combos -----
			// Combo Bancos
			$cn2 = new Socket();
			$cn2->SetRemoteHost($LISTNER_IP);
			$cn2->SetRemotePort($LISTNER_PORTA);
			$cn2->SetBufferLength(131072);
			$cn2->SetConnectTimeOut(1);
			$send = "fnc_combo_bancos;$banco";
			$xml = $cn2->Ask($send);
			if ($cn2->Error()) 
			{
				echo "Ocorreu um erro: ".$cn2->Error();
			}
			
			$dom = new DOMDocument("1.0", "utf-8");
			$dom->loadXML(utf8_encode($xml)); // Rever isto, pois está enviando caracteres estranhos ao invés dos acentos.
			$campos = $dom->getElementsByTagName("fld");
			
			foreach ($campos as $nodo) 
			{
				if ($nodo->getAttribute('tp') == 'LST') 
				{
					$tpl->newBlock('blk_bancos');
					$tpl->assign('cd_instituicao', $nodo->getAttribute('value'));
					$tpl->assign('nome_instituicao', $nodo->nodeValue);
					if ($nodo->getAttribute('selected') == 'TRUE') 
					{
						$tpl->assign('selBanco', ' selected');
					}
				}
			}
         
			$tpl->gotoBlock('_ROOT');
			$tpl->assign('cd_agencia_default', $agencia);

			// Combo Agências
			$cn3 = new Socket();
			$cn3->SetRemoteHost($LISTNER_IP);
			$cn3->SetRemotePort($LISTNER_PORTA);
			$cn3->SetBufferLength(131072);
			$cn3->SetConnectTimeOut(1);
			$send = "fnc_combo_agencias;$banco;$agencia";
			$xml = $cn3->Ask($send);
			if ($cn3->Error()) 
			{
				echo "Ocorreu um erro: ".$cn3->Error();
			}
			
			$dom = new DOMDocument("1.0", "utf-8");
			$dom->loadXML(utf8_encode($xml)); // Rever isto, pois está enviando caracteres estranhos ao invés dos acentos.
			$campos = $dom->getElementsByTagName("fld");
			foreach ($campos as $nodo) 
			{
				if ($nodo->getAttribute('tp') == 'LST') 
				{
					$tpl->newBlock('blk_agencias');
					$tpl->assign('cd_agencia', $nodo->getAttribute('value'));
					//$tpl->assign('nome_instituicao', $nodo->nodeValue);
					if ($nodo->getAttribute('selected') == 'TRUE') 
					{
						$tpl->assign('selAgencia', ' selected');
					}
				}
			}

			$tpl->assignGlobal('session_id', $sessao);
		}
	} 
	else 
	{
		echo "Ocorreu um erro de conexão com o webservice";
	}
	$tpl->printToScreen();   
?>
