<?php
	require('inc/conexao.php');
	require('inc/sessao.php');
	header("Location: ".site_url("gestao/nc/acao_corretiva/".$_REQUEST['ac']);


exit;
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   
   $tpl = new TemplatePower('tpl/tpl_cad_acao_corretiva.html');
   $tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   $tpl->prepare();
   $tpl->assign('n', $n);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
   $tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   //-------------------------------------------------------------------------------------------
	if($msg == 'E')
	{
	    echo "<script language='JavaScript'>alert('Não é possível cadastrar uma ação corretiva sem especificar o reponsável pela Não Conformidade.');</script>";
		$msg='';
	}
//------------------------------------------------------------------------------------------- 

//------------------------------------ Controle de existência de NAO CONFORMIDADE:
	if ($pro == '') 
	{ 
		$tpl->newBlock('mensagem');
		$tpl->assign('msg', 'Antes de cadastrar a ação corretiva, salve a não-conformidade.');
	}
	else
	{
	   	$tpl->newBlock('cadastro');
		$tpl->assign('num_nao_conf', conv_num_nc($ac));
   		$tpl->assign('num_processo', $pro);
		$tpl->assign('nnc', $ac);
		$tpl->assign('prj', $pro);
// ------------------------------------ Processo em que a ação está atuando:
		$sql = " SELECT cd_processo AS processo, 
		                procedimento AS nome_processo 
				   FROM projetos.processos
				  WHERE cd_processo = ".$pro;
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) 
		{
			$tpl->assign('nome_processo', $reg['nome_processo']);
		}
		// ------------------------------------	Acessa não conformidade:
		$sql = " SELECT nc.cd_responsavel AS cod_responsavel,
		                TO_CHAR(nc.dt_cadastro,'dd/mm/yyyy') AS dt_cadastro,
						TO_CHAR((nc.dt_cadastro + '15 days'::interval),'dd/mm/yyyy') AS dt_limite,
						disposicao, 
						causa
				   FROM projetos.nao_conformidade nc
				  WHERE nc.cd_nao_conformidade	= ".$ac;
		$rs = pg_exec($db, $sql);
        while ($reg=pg_fetch_array($rs)) 
		{
			$tpl->assign('dt_limite',   $reg['dt_limite']);
			$tpl->assign('dt_cadastro',  $reg['dt_cadastro']);
			$disposicao = $reg['disposicao'];
			$causa = $reg['causa'];
			$cod_responsavel = $reg['cod_responsavel'];
		}
		// -------------------------------------- Limita campos para o responsável pela Ação Corretiva:
		if ($Z != $cod_responsavel)
		{
			$tpl->assign('ro_resp', 'readonly');
		}
		// ------------------------------------ Usuários membros do comitê da Qualidade:
		$sql = " SELECT indic_12 
		           FROM projetos.usuarios_controledi 
				  WHERE codigo = ".$Z ;
		$rs = pg_exec($db, $sql);
		while( $reg=pg_fetch_array($rs) ) 
		{
			$indic12 = $reg['indic_12'];
		}
		
		if ( $indic12 != '*' )
		{
			$tpl->assign('ro_comite', 'readonly');
		}

		// ------------------------------------	Verifica se existe ACAO CORRETIVA:

		$sql = " SELECT ac.cd_acao AS cd_acao,
		                ac.cd_nao_conformidade AS cod_nao_conf,
						pp.desc_proc AS desc_processo,
						nc.cd_processo AS cod_processo,
						puc.nome AS responsavel,
						nc.cd_responsavel AS cod_responsavel,
						ac.tipo_acao AS tipo_acao,
						TO_CHAR(ac.dt_limite_apres,'dd/mm/yyyy') AS dt_limite_apres,
						TO_CHAR(ac.dt_apres,'dd/mm/yyyy') AS dt_apres,
						TO_CHAR(ac.dt_prop_imp,'dd/mm/yyyy') AS dt_prop_imp,
						TO_CHAR(ac.dt_efe_imp,'dd/mm/yyyy') AS dt_efe_imp,
						TO_CHAR(ac.dt_prop_verif,'dd/mm/yyyy') AS dt_prop_verif,
						TO_CHAR(ac.dt_efe_verif,'dd/mm/yyyy') AS dt_efe_verif,
						TO_CHAR(ac.dt_prorrogada,'dd/mm/yyyy') AS dt_prorrogada,
						TO_CHAR(ac.dt_prorrogada_em,'yyyymmdd') AS dt_prorrogada_em,
						ac.ac_proposta AS ac_proposta,
						ac.raz_nao_apres AS raz_nao_apres,
						ac.raz_nao_imp AS raz_nao_imp
				   FROM projetos.acao_corretiva ac,
				        projetos.processos pp,
						projetos.nao_conformidade nc,
						projetos.usuarios_controledi puc
				  WHERE ac.cd_processo    = pp.cd_processo
				    and ac.cd_acao        = nc.cd_nao_conformidade
					and pp.cd_processo    = nc.cd_processo
					and nc.cd_responsavel = puc.codigo 
				    and ac.cd_acao        = ".$ac;
		$rs = pg_query($db, $sql);

		$n = 'I';
        while( $reg=pg_fetch_array($rs) )
		{
			$cod_acao = $reg['cd_acao'];
			$cod_nao_conf = $reg['cod_nao_conf'];
			$cod_processo = $reg['cod_processo'];
			$tpl->assign('nnc', $cod_nao_conf);
			$tpl->assign('prj', $pro);
			$tipo_acao = $reg['tipo_acao'];
			$tpl->assign('dt_limite',   			$reg['dt_limite_apres']);
			$tpl->assign('dt_apresentacao', 		$reg['dt_apres']);

			$tpl->assign('dt_prop_imp',   			trim($reg['dt_prop_imp']));
			$tpl->assign('dt_prop_impl_original',  	trim($reg['dt_prop_imp']));
			if(trim($reg['dt_prop_imp']) != "")
			{
				$tpl->assign('dt_prop_readonly', 'readonly');
			}

			$tpl->assign('dt_efe_imp', trim($reg['dt_efe_imp']) );
			$tpl->assign('dt_efe_imp_orig', trim( $reg['dt_efe_imp']) );

			$ro_dt_efe_imp = "readonly";
			if( $indic12=="*" )
			{
				$ro_dt_efe_imp = "";
			}
			elseif( $Z == $cod_responsavel && $reg['dt_efe_imp']=="" )
			{
				$ro_dt_efe_imp = "";
			}
			$tpl->assign( "ro_dt_efe_imp", $ro_dt_efe_imp );

			$tpl->assign('dt_prop_verif',   		trim($reg['dt_prop_verif']));
			$tpl->assign('dt_efe_verif',   			trim($reg['dt_efe_verif']));
			if( trim($reg['dt_efe_verif']) != "" )
			{
				$tpl->assign('fl_button', 'none');
			}

			$tpl->assign('dt_prorrogada', $reg['dt_prorrogada']);

			if( $reg['dt_prorrogada_em']!='' AND $reg['dt_prorrogada_em']<date('Ymd') )
			{
				$tpl->assign('ro_resp', 'readonly');
				$readonly = 'readonly'; 
			}
			else
			{
				$readonly = ''; 
			}
			$tpl->assign( 'prorrogacao_readonly', $readonly );

			$tpl->assign('descricao',   			$reg['ac_proposta'] );
			$tpl->assign('descricao_print',         nl2br(htmlentities($reg['ac_proposta'])));
			$tpl->assign('raz_nao_apres',   		$reg['raz_nao_apres'] );
			$tpl->assign('raz_nao_imp',   			$reg['raz_nao_imp'] );
			$tpl->assign('raz_nao_imp_print',         nl2br(htmlentities($reg['raz_nao_imp'])));
			$tpl->assign('data_hoje',   			date('d/m/Y') );
			$n = 'U';
        }
		if ($n == 'I')
		{
			$date = date("d/m/Y");
			$tpl->assign('dt_apresentacao',  $date);
			$tpl->assign('cor_fundo', '#F0E0C7');
			$tpl->assign('mensagem', 'Ainda não foi apresentada Ação Corretiva');
		}
		$date = date("d/m/Y");
		$tpl->assign('txt_sysdate',  $date);
		$tpl->assign('insere', $n);
		// ------------------------------------- Demais campos:
		$tpl->newBlock('codigo');
		$tpl->assign('cod_acao', $ac);
	  	$tpl->assign('cod_processo', $pro);
   	  	$tpl->assign('cod_nao_conf', $ac);
		$tpl->assign('cod_responsavel', $cod_responsavel);
		$tpl->assign('disposicao', $disposicao);
		$tpl->assign('causa', $causa);
		// -------------------------------------------------------
	}
	pg_close($db);
	$tpl->printToScreen();	
	// -------------------------------------------------------------

function somadata($dias,$datahoje)
{  
	// Desmembra Data -------------------------------------------------------------
	if (ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datahoje, $sep)) {  
		$dia = $sep[1];  
		$mes = $sep[2];  
		$ano = $sep[3];  
		} 
	else {  
		echo "<b>Formato Inválido de Data - $datahoje</b><br>";  
	}  
	$i = $dias;  
 	
	for($i = 0;$i<$dias;$i++){  

		if ($mes == 01 || $mes == 03 || $mes == 05 || $mes == 07 || $mes == 8 || $mes == 10 || $mes == 12)
		{  
			if($mes == 12 && $dia == 31){  
				$mes = 01;  
				$ano++;  
				$dia = 00;  
			}  
			if($dia == 31 && $mes != 12){  
				$mes++;  
				$dia = 00;  
			}  
		} // fecha if geral

		if($mes == 04 || $mes == 06 || $mes == 09 || $mes == 11){  
			if($dia == 30){  
				$dia =  00;  
				$mes++;  
			}  
		}//fecha if geral

		if($mes == 02){  
			if($ano % 4 == 0 && $ano % 100 != 0){ //ano bissexto
				if($dia == 29){  
					$dia = 00;  
					$mes++;       
				}  
			}  
			else{  
				if($dia == 28){  
					$dia = 00;  
					$mes++;  
				}  
			}  
		}//FECHA IF DO MÊS 2
		$dia++;  
	}//fecha o for()

// Confirma Saída de 2 dígitos ------------------------------------------------

	if(strlen($dia) == 1){$dia = "0".$dia;};  
	if(strlen($mes) == 1){$mes = "0".$mes;};  

// Monta Saída ----------------------------------------------------------------

	$nova_data = $dia."/".$mes."/".$ano;  

	return $nova_data;  

	}//fecha a funçâo data
 //---------------------------------------------------------------------------------------------
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
//---------------------------------------------------------------------------------------------
	function conv_num_nc($n) {
// Pressupõe que o num esteja no formato AAAANNN
		$aaaa = substr($n, 0, 4);
		$nc = substr($n, 4, 3);
		return $nc.'/'.$aaaa;
	}
?>