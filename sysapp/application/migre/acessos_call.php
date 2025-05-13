<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_acessos_call.html');
	$tpl->prepare();

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('dt_inicial', $dt_inicial);
	$tpl->assign('dt_final', $dt_final);
	$txt_dt_inicial	= ( $dt_inicial    == '' ? 'Null' : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? 'Null' : convdata_br_iso($dt_final));

	$tpl->newBlock('lista');
	$sql =        " select distinct count(transaction) as num_acessos, ";
	$sql = $sql . " date_trunc('day',calldate), ";
	$sql = $sql . " to_char(date_trunc('day',calldate),'dd/mm/yyyy') as data";
	$sql = $sql . " from ura.ivrchannelheader ";
	$sql = $sql . " where transaction > 24 ";
	if ($txt_dt_inicial <> 'Null')
	{
		$sql = $sql . "and (calldate >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null')
	{
		$sql = $sql . "and (calldate <= '$txt_dt_final') ";
	}
	$sql = $sql . " group by date_trunc('day',calldate)  ";
	$sql = $sql . " order by date_trunc('day',calldate) desc ";

	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('projetos');
		$cont = $cont + 1;

		if (($cont % 2) <> 0)
		{
			$tpl->assign('cor_fundo', '#F4F4F4');
		}
		else
		{
			$tpl->assign('cor_fundo', '#FAFAFA');
		}
		$tpl->assign('data', $reg['data']);
		$tpl->assign('numero_acessos', $reg['num_acessos']);
	}
	pg_close($db);

	$tpl->printToScreen();

	function convdata_br_iso($dt)
	{
		// Pressupõe que a data esteja no formato DD/MM/AAAA
		// A melhor forma de gravar datas no PostgreSQL é utilizando 
		// uma string no formato DDDD-MM-AA. Esta função justamente 
		// adequa a data a este formato
		$d = substr($dt, 0, 2);
		$m = substr($dt, 3, 2);
		$a = substr($dt, 6, 4);

		return $a . '-' . $m . '-' . $d;
   }
?>