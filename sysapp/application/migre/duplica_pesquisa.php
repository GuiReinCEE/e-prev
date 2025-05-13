<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	$cd_nova_enquete = getNextval("projetos", "enquetes", "cd_enquete", $db); 
// ------------------------------------------------ Enquete:
	$sql = "
			INSERT INTO projetos.enquetes(
						cd_enquete, titulo, cd_site, cd_responsavel, dt_inclusao, texto_abertura, 
						dt_exclusao, cd_evento_institucional, cd_publicacao, imagem, 
						dt_inicio, dt_fim, controle_respostas, ultimo_respondente, cd_servico, 
						tipo_enquete, tipo_layout, texto_encerramento, obrigatoriedade, 
						nr_publico_total, cd_divisao_responsavel, flag_percentual_respondentes)	
			(
			SELECT ".$cd_nova_enquete." AS cd_enquete, titulo, cd_site, cd_responsavel, dt_inclusao, texto_abertura, 
				   dt_exclusao, cd_evento_institucional, cd_publicacao, imagem, 
				   dt_inicio, dt_fim, controle_respostas, ultimo_respondente, cd_servico, 
				   tipo_enquete, tipo_layout, texto_encerramento, obrigatoriedade, 
				   nr_publico_total, cd_divisao_responsavel, flag_percentual_respondentes
			  FROM projetos.enquetes
			 WHERE cd_enquete = ".$num_pesquisa."
			);
	       ";

	if (!@pg_exec($db, $sql)) 
	{
		echo "Ocorreu um erro ao tentar gravar esta enquete.";
		exit;
	}
	
// ------------------------------------------------ Respostas:
	$sql = "insert into projetos.enquete_respostas (
			cd_enquete, cd_resposta , nome, ordem, valor )
			(  select ". $cd_nova_enquete. ", cd_resposta, nome, ordem, valor from  projetos.enquete_respostas 
			where cd_enquete = ".$num_pesquisa.") ";

	if (!@pg_exec($db, $sql)) 
	{
		echo "Ocorreu um erro ao tentar gravar as respostas desta enquete.";
		exit;
	}
	
// ------------------------------------------------ Descobre os agrupamentos novos
	$sql_1 = "select cd_agrupamento, nome, indic_escala, ordem, mostrar_valores, nota_rodape
			 from projetos.enquete_agrupamentos where cd_enquete = ".$num_pesquisa;
	$rs_1 = pg_query($db, $sql_1);
	$cont = 0;
	while ($reg=pg_fetch_array($rs_1)) 
	{
// ------------------------------------------------ Busca o novo agrupamento a ser inserido
		$cd_novo_agrupamento = getNextval("projetos", "enquete_agrupamentos", "cd_agrupamento", $db); 
// ------------------------------------------------ Agrupamentos:
		$sql = "insert into projetos.enquete_agrupamentos (
				cd_enquete , 
				cd_agrupamento, 
				nome, 
				indic_escala, 
				ordem, 
				mostrar_valores, 
				nota_rodape
			) values ( 
				".$cd_nova_enquete.",
				".$cd_novo_agrupamento.", 
				'".$reg['nome']."', 
				'".$reg['indic_escala']."', 
				".$reg['ordem'].", 
				'".$reg['mostrar_valores']."', 
				'".$reg['nota_rodape']."')" ;
		if (!@pg_query($db, $sql))
		{
			echo "Ocorreu um erro ao tentar gravar os agrupamentos desta enquete.";
			exit;
		}
// ------------------------------------------------ Perguntas:
		$sql = "
				INSERT INTO projetos.enquete_perguntas(
							cd_enquete, cd_pergunta, texto, r1, r2, r3, r4, r5, r6, r7, r8, 
							r9, r10, r11, r12, cd_agrupamento, pergunta_texto, rotulo1, rotulo2, 
							rotulo3, rotulo4, rotulo5, rotulo6, rotulo7, rotulo8, rotulo9, 
							rotulo10, rotulo11, rotulo12, r_diss, rotulo_dissertativa, r_justificativa, 
							rotulo_justificativa, dt_exclusao, cd_usu_exclusao, legenda1, 
							legenda2, legenda3, legenda4, legenda5, legenda6, legenda7, legenda8, 
							legenda9, legenda10, legenda11, legenda12, flag_percentual_respondentes, 
							r1_complemento, r2_complemento, r3_complemento, r4_complemento, 
							r5_complemento, r6_complemento, r7_complemento, r8_complemento, 
							r9_complemento, r10_complemento, r11_complemento, r12_complemento, 
							fl_multipla_resposta, r1_complemento_rotulo, r2_complemento_rotulo, 
							r3_complemento_rotulo, r4_complemento_rotulo, r5_complemento_rotulo, 
							r6_complemento_rotulo, r7_complemento_rotulo, r8_complemento_rotulo, 
							r9_complemento_rotulo, r10_complemento_rotulo, r11_complemento_rotulo, 
							r12_complemento_rotulo)		
						(							
						SELECT ".$cd_nova_enquete." AS cd_enquete, cd_pergunta, texto, r1, r2, r3, r4, r5, r6, r7, r8, 
							   r9, r10, r11, r12, ".$cd_novo_agrupamento." AS cd_agrupamento, pergunta_texto, rotulo1, rotulo2, 
							   rotulo3, rotulo4, rotulo5, rotulo6, rotulo7, rotulo8, rotulo9, 
							   rotulo10, rotulo11, rotulo12, r_diss, rotulo_dissertativa, r_justificativa, 
							   rotulo_justificativa, dt_exclusao, cd_usu_exclusao, legenda1, 
							   legenda2, legenda3, legenda4, legenda5, legenda6, legenda7, legenda8, 
							   legenda9, legenda10, legenda11, legenda12, flag_percentual_respondentes, 
							   r1_complemento, r2_complemento, r3_complemento, r4_complemento, 
							   r5_complemento, r6_complemento, r7_complemento, r8_complemento, 
							   r9_complemento, r10_complemento, r11_complemento, r12_complemento, 
							   fl_multipla_resposta, r1_complemento_rotulo, r2_complemento_rotulo, 
							   r3_complemento_rotulo, r4_complemento_rotulo, r5_complemento_rotulo, 
							   r6_complemento_rotulo, r7_complemento_rotulo, r8_complemento_rotulo, 
							   r9_complemento_rotulo, r10_complemento_rotulo, r11_complemento_rotulo, 
							   r12_complemento_rotulo
						  FROM projetos.enquete_perguntas
						 WHERE cd_enquete     = ".$num_pesquisa." 
						   AND cd_agrupamento = ".$reg['cd_agrupamento']."   
						);						
		       ";

		if (!@pg_query($db, $sql))
		{
			echo "Ocorreu um erro ao tentar gravar as perguntas desta enquete.";
			exit;
		}
	}
	header('location: cad_enquetes_definicao.php?c='.$cd_nova_enquete);

?>