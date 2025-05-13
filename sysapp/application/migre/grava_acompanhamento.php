<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');

	if ($insere == 'A') 
	{
		$sql = "UPDATE projetos.acompanhamento 
		           SET situacao = '".$_POST['situacao']."' 
				 WHERE cd_acompanhamento = ".$_POST['cod_acompanhamento'];
	}
	else 
	{
		$sql = "INSERT INTO projetos.acompanhamento 
		                  ( 
						    cd_processo,                        
							cd_nao_conformidade,               
							data,                              
							situacao,                          
							auditor                           
						  )                                     
 				     VALUES 
					      (                               
						    ".$_POST['cod_processo'].",                    
							".$_POST['cod_nao_conf'].",                    
							TO_DATE('".$_POST['data']."','DD/MM/YYYY'),                        
							'".$_POST['situacao']."',                      
							".$_POST['auditor']."                           
						  )";
	}

	if (pg_query($db, $sql)) 
	{
		pg_close($db);
		header('location: cad_nao_conformidade.php?c='.$cod_nao_conf.'&tr=U');
	}
	else 
	{
		pg_close($db);
		echo "Ocorreu um erro ao tentar incluir este acompanhamento";
	}
?>