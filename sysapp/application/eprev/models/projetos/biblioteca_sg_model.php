<?php
class Biblioteca_sg_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT bl.cd_biblioteca_livro,
			       bl.nr_biblioteca_livro,
			       bl.ds_biblioteca_livro,
			       bl.autor,
			       
			       (SELECT m.cd_biblioteca_livro_movimento FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) AS cd_biblioteca_livro_movimento,

			       CASE WHEN (SELECT m.cd_biblioteca_livro_movimento FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL
			                 OR
			                 (SELECT m.dt_devolvido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
				        THEN 'S'
				        ELSE 'N'
			       END AS fl_locar,
			       CASE WHEN (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
			                 AND
			                 (SELECT m.dt_devolvido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL  
				        THEN 'S'
				        ELSE 'N'
			       END AS fl_devolver,

			       CASE WHEN (SELECT m.cd_biblioteca_livro_movimento FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL
			                 OR
			                 (SELECT m.dt_devolvido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
				        THEN 'Disponível'
			            WHEN (SELECT m.dt_retirada FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
			                 AND
			                 (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL
			            THEN 'Reservado'
			            WHEN (SELECT m.dt_retirada + interval '15 day' FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1)  < CURRENT_TIMESTAMP
			            	 AND
			            	 (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL 
			            THEN 'Emprestado (Atrasado) - ' || 
			            	 (SELECT p.nome 
							     FROM projetos.participante_cpf((SELECT m.cpf FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1), 1) x 
							     JOIN participantes p 
							       ON p.cd_empresa = x.cd_empresa 
							      AND p.cd_registro_empregado = x.cd_registro_empregado 
							      AND p.seq_dependencia = x.seq_dependencia)  
			            WHEN (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1)  IS NOT NULL 
			            THEN 'Emprestado - ' || 
			                 (SELECT p.nome 
							    FROM projetos.participante_cpf((SELECT m.cpf FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1), 1) x 
							    JOIN participantes p 
								  ON p.cd_empresa = x.cd_empresa 
								 AND p.cd_registro_empregado = x.cd_registro_empregado 
								 AND p.seq_dependencia = x.seq_dependencia)  
			       END AS status,

			       CASE WHEN (SELECT m.cd_biblioteca_livro_movimento FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL
			                 OR
			                 (SELECT m.dt_devolvido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
				        THEN 'label'
			            WHEN (SELECT m.dt_retirada FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL
			                 AND
			                 (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NULL
			            THEN 'label label-success'
			            WHEN (SELECT m.dt_retirada + interval '15 day' FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1)  < CURRENT_TIMESTAMP
			            	 AND
			            	 (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1) IS NOT NULL 
			            THEN 'label label-important'
			            WHEN  (SELECT m.dt_recebido FROM projetos.biblioteca_livro_movimento m WHERE m.cd_biblioteca_livro = bl.cd_biblioteca_livro AND m.dt_exclusao IS NULL ORDER BY m.cd_biblioteca_livro_movimento DESC LIMIT 1)  IS NOT NULL 
			            THEN 'label label-warning'
			       END AS class_status

			  FROM projetos.biblioteca_livro bl
			 WHERE bl.dt_exclusao IS NULL;";

		$result = $this->db->query($qr_sql);
	}

	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_biblioteca_livro,
				   nr_biblioteca_livro,
				   ds_biblioteca_livro,
				   autor
			  FROM projetos.biblioteca_livro
			 WHERE cd_biblioteca_livro = ".intval($args["cd_biblioteca_livro"]).";";
			 
		$result = $this->db->query($qr_sql);
	}	

	function salvar(&$result, $args=array())
	{
		if(intval($args["cd_biblioteca_livro"]) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.biblioteca_livro 
				     (
				     	nr_biblioteca_livro,
					    ds_biblioteca_livro,
					    autor,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
					    ".(trim($args["nr_biblioteca_livro"]) != "" ? intval($args["nr_biblioteca_livro"]) : "DEFAULT").",
					    ".(trim($args["ds_biblioteca_livro"]) != "" ? str_escape($args["ds_biblioteca_livro"]) : "DEFAULT").",
					    ".(trim($args["autor"]) != "" ? str_escape($args["autor"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.biblioteca_livro
				   SET nr_biblioteca_livro  = ".(trim($args["nr_biblioteca_livro"]) != "" ? intval($args["nr_biblioteca_livro"]) : "DEFAULT").",
					   ds_biblioteca_livro  = ".(trim($args["ds_biblioteca_livro"]) != "" ? str_escape($args["ds_biblioteca_livro"]) : "DEFAULT").",
					   autor                = ".(trim($args["autor"]) != "" ? str_escape($args["autor"]) : "DEFAULT").",
				       cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
				       dt_alteracao         = CURRENT_TIMESTAMP
			 	 WHERE cd_biblioteca_livro = ".intval($args["cd_biblioteca_livro"]).";";
		}
		
			 
		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.biblioteca_livro
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_biblioteca_livro = ".intval($args["cd_biblioteca_livro"]).";";
			 
		$this->db->query($qr_sql);
	}

	function devolver(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.biblioteca_livro_movimento
			   SET dt_devolvido         = CURRENT_TIMESTAMP,
			       cd_usuario_devolvido = ".intval($args["cd_usuario"])."
		     WHERE cd_biblioteca_livro_movimento = ".intval($args["cd_biblioteca_livro_movimento"]).";";
			 
		$this->db->query($qr_sql);
	}

	function alugar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.biblioteca_livro_movimento
			     (
			     	cd_biblioteca_livro,
			     	cpf,
			     	cd_usuario_inclusao,
			     	cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".(trim($args["cd_biblioteca_livro"]) != "" ? intval($args["cd_biblioteca_livro"]) : "DEFAULT").",
			     	".(trim($args["cpf"]) != "" ? str_escape($args["cpf"]) : "DEFAULT").",
			     	".intval($args["cd_usuario"]).",
					".intval($args["cd_usuario"])."
			     )";

		$this->db->query($qr_sql);
	}

	function confirmar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.biblioteca_livro_movimento
			   SET dt_recebido = CURRENT_TIMESTAMP
		     WHERE cd_biblioteca_livro_movimento = ".intval($args["cd_biblioteca_livro_movimento"]).";";
			 
		$this->db->query($qr_sql);
	}

	function busca_participante(&$result, $args=array())
	{
		$qr_sql = "
			SELECT p.nome 
			  FROM projetos.participante_cpf('".trim($args["cpf"])."', 1) x 
			  JOIN participantes p 
			    ON p.cd_empresa = x.cd_empresa 
			   AND p.cd_registro_empregado = x.cd_registro_empregado 
			   AND p.seq_dependencia = x.seq_dependencia;";

		$result = $this->db->query($qr_sql);
	}

	function historico(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(m.dt_retirada, 'DD/MM/YYYY HH24:MI:ss') AS dt_retirada,
			       TO_CHAR(m.dt_recebido, 'DD/MM/YYYY HH24:MI:ss') AS dt_recebido,
			       TO_CHAR(m.dt_devolvido, 'DD/MM/YYYY HH24:MI:ss') AS dt_devolvido,
                   m.cpf,
                   (SELECT p.nome 
					  FROM projetos.participante_cpf(m.cpf, 1) x 
					  JOIN participantes p 
					    ON p.cd_empresa = x.cd_empresa 
					   AND p.cd_registro_empregado = x.cd_registro_empregado 
					   AND p.seq_dependencia = x.seq_dependencia) AS nome
			  FROM projetos.biblioteca_livro_movimento m
			 WHERE m.cd_biblioteca_livro = ".intval($args["cd_biblioteca_livro"])."
			   AND m.dt_exclusao IS NULL
			 ORDER BY m.dt_retirada;";

		$result = $this->db->query($qr_sql);
	}
}