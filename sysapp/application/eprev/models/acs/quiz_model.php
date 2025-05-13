<?php
class Quiz_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function quizCadastroListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT qc.cd_quiz_cadastro, 
					       qc.cd_quiz, 
						   TO_CHAR(qc.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   qc.nome, 
						   qc.empresa, 
						   qc.cargo, 
                           qc.email, 
						   qc.telefone, 
						   qc.celular, 
						   qc.plano,
						   (SELECT COUNT(qr1.cd_quiz_resposta)
							  FROM acs.quiz_cadastro qc1
							  LEFT JOIN acs.quiz_resposta qr1
								ON qr1.cd_quiz_cadastro = qc1.cd_quiz_cadastro
							   AND qr1.cd_quiz          = qc1.cd_quiz
							  LEFT JOIN acs.quiz_pergunta_item qti1
								ON qti1.cd_quiz_pergunta_item = qr1.cd_quiz_pergunta_item
							   AND qti1.fl_certo = 'S'
							  LEFT JOIN acs.quiz_pergunta qp1
								ON qp1.cd_quiz_pergunta = qti1.cd_quiz_pergunta
							 WHERE qc1.cd_quiz_cadastro = qc.cd_quiz_cadastro) AS qt_acerto
                      FROM acs.quiz_cadastro qc
					 ORDER BY qc.nome
		          ";
		$result = $this->db->query($qr_sql);
	}
	

}
?>