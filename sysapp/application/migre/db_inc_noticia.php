<?PHP
   include_once('inc/conexao.php');
   $descricao = ereg_replace("'", "\'", $noticia);
//   $txt_descricao  = ( $noticia == '' ? 'Null' : "'".str_replace("'"," ", $noticia)."'" );
	$txt_descricao = $noticia;
	switch ($editorial)
      {
			case 'FC':$ordem = 0; break;
			case 'FP':$ordem = 1; break;
			case 'PR':$ordem = 2; break;
			case 'PO':$ordem = 3; break;
			case 'EC':$ordem = 4; break;
			case 'EN':$ordem = 5; break;
			case 'CO':$ordem = 6; break;
			case 'ET':$ordem = 7; break;
			case 'GE':$ordem = 8; break;
			case 'QV':$ordem = 9; break;
			case 'CT':$ordem = 10; break;
			case 'RH':$ordem = 11; break;
			case 'QU':$ordem = 12; break;
      }

   $sql = "INSERT INTO acs.noticias (titulo, descricao, data, editorial, ordem) values ('$titulo', '$txt_descricao', now(), '$editorial', $ordem)";

   if (pg_exec($db, $sql)) 
   {
      pg_close($db);
      header("location: lst_noticias.php");
   }
   else
   {
      pg_close($db);
      header("location: erro.php?cod=1");
   }            
?>
