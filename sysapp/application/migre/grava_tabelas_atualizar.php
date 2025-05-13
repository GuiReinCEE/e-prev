<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');

	if ($codigo<>"") 
	{
		$sql =        " update 	projetos.tabelas_atualizar 
						set 	comando = '$comando', 
						    	contagem = '$contagem',  
						    	periodicidade = '$periodicidade',  
								postgres = '$postgres',  
								oracle = '$oracle',  
								access_callcenter = '$access_callcenter',  
								comando_inicial = '$comando_inicial',  
								comando_final = '$comando_final',  
								incrementar = '$incrementar', 
								campo_controle_incremental = '$campo_controle', 
								truncar = '$truncar',
								condicao = '".$_POST['condicao']."'								
						where 	tabela = '$codigo'      ";
   }
	if ($rs=pg_query($db, $sql)) {
		pg_close($db);
		header('location: cad_tabelas_atualizar.php?c='.$codigo);
	}
	else {
		pg_close($db);
		header('location: cad_tabelas_atualizar.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
	}
	
?>