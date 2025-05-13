<?PHP
	include_once("inc/sessao.php");
	include_once('inc/conexao.php');

	header( 'Location:'.base_url().'index.php/ecrm/informativo' );

	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lista_noticias.htm');
	$tpl->assignInclude('menu', 'inc/menu_noticias.htm');
	$tpl->prepare();
   
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);

	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   $sql =        " SELECT codigo, data,  ";
   $sql = $sql . "        lpad(extract(day from data)::text, 2, '0')||'/'||lpad(extract(month from data)::text,2,'0')||'/'||extract(year from data)::text||' - '||lpad(extract(hour from data)::text,2,'0')||':'||lpad(extract(minutes from data)::text,2,'0')||':'||lpad(extract(seconds from data)::text,2,'0') as data_ed, ";
   $sql = $sql . "        titulo, ";
   $sql = $sql . "        editorial ";
   $sql = $sql . " FROM acs.noticias ";
   $sql = $sql . " ORDER BY DATA DESC ";
   $rs = pg_exec($db, $sql);
   while ($r = pg_fetch_array($rs)) 
   {
      $tpl->newBlock('noticias');
      $tpl->assign('codigo', $r['codigo']);
      $tpl->assign('data', $r['data_ed']);
      $tpl->assign('titulo', $r['titulo']);
      switch ($r['editorial']) 
      {
        case 'FP':$tpl->assign('editorial', 'Fundos de pensão');break;         
        case 'FC':$tpl->assign('editorial', 'Fundação CEEE');break;         
        case 'PR':$tpl->assign('editorial', 'Previdência');break;         
        case 'PO':$tpl->assign('editorial', 'Política'); break;
	 	case 'PR':$tpl->assign('editorial', 'Previdência'); break;
		case 'EC':$tpl->assign('editorial', 'Economia'); break;
		case 'EN':$tpl->assign('editorial', 'Energia'); break;
		case 'EA':$tpl->assign('editorial', 'Educação Ambiental'); break;
		case 'CO':$tpl->assign('editorial', 'Colunistas'); break;
		case 'ET':$tpl->assign('editorial', 'Entrevista'); break;
		case 'GE':$tpl->assign('editorial', 'Geral'); break;
		case 'QV':$tpl->assign('editorial', 'Qualidade de Vida'); break;
		case 'CT':$tpl->assign('editorial', 'Ciência e Tecnologia'); break;
		case 'RH':$tpl->assign('editorial', 'Recursos Humanos'); break;
		case 'QU':$tpl->assign('editorial', 'Qualidade'); break;
      }
   }
   $tpl->printToScreen();
   pg_close($db);      
?>
