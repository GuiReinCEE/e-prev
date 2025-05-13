<?
	include ('inc/pdfClasses/class.ezpdf.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
// ---------------------------------------------------------------------------------
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$mes_hoje = date("m");
	$ano_hoje = date("Y");
	$dia_hoje = date("d");
	$txt_dt_inicial	= ( $dt_inicial    == '' ? date('Y-m-d') : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? date('Y-m-d') : convdata_br_iso($dt_final));
	$offset = (($lote - 1) * 10);
	$sql = " delete from projetos.certificados_participantes_tmp ";
	$s = (pg_exec($db, $sql));

	$v_num_regs = $reg['num_regs'];
	$sql_lote = "
					insert into projetos.certificados_participantes_tmp 
	(cd_plano, 
	 cd_empresa, 
	 cd_registro_empregado, 
	 seq_dependencia, 
	 nome, 
	 dt_ingresso, 
	 cd_plano_spc, 
	 nome_plano_certificado, 
	 largura_imagem, 
	 pos_imagem, 
	 dt_aprovacao_spc) 
	(select 
	 pc.cd_plano, 
	 p.cd_empresa, 
	 p.cd_registro_empregado, 
	 p.seq_dependencia, 
	 nome, 
	 dt_ingresso_eletro, 
	 cd_spc, 
	 nome_certificado, 
	 largura_imagem, 
	 pos_imagem, 
	 dt_aprovacao_spc 
	 from participantes p, titulares t, planos_certificados pc 
	 where	p.cd_empresa = t.cd_empresa 
	 and	p.cd_registro_empregado = t.cd_registro_empregado 
	 and	p.seq_dependencia = t.seq_dependencia 
	 and 	pc.cd_plano =  CASE WHEN p.cd_empresa = 3 then 3  else CASE WHEN p.cd_empresa = 2 then 1  else p.cd_plano  end  end
	 and 	p.dt_envio_certificado is null and p.dt_obito is null  
	 and 	p.cd_plano = ".$cd_plano."
	 and 	p.cd_empresa = ".$cd_empresa."
	 and dt_aprovacao_spc is not null 
	 and 	date_trunc('day', t.dt_ingresso_eletro) >= '".$txt_dt_inicial."'
	 and date_trunc('day', t.dt_ingresso_eletro) <= '".$txt_dt_final."' 
	 and dt_aprovacao_spc <= t.dt_ingresso_eletro 
	 --limit 10 offset $offset
	 ) ";
	#echo "<PRE>$sql_lote</PRE>";	EXIT;
	if ($rs=pg_exec($db, $sql_lote)) {
		pg_close($db);
		header('location: cert_participantes_lotes.php'); 
	}
//---------------------------------------------------------------------------
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
//--------------------------------------------------------------	
?>