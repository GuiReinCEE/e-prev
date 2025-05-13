<?php
class Treinamento_colaborador_formulario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_treinamento_tipo()
	{
		$qr_sql = "
			SELECT cd_treinamento_colaborador_tipo AS value,
                   ds_treinamento_colaborador_tipo AS text
              FROM projetos.treinamento_colaborador_tipo
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT f.cd_treinamento_colaborador_formulario,
			       f.ds_treinamento_colaborador_formulario,
				   f.nr_dias_envio,
			       CASE WHEN fl_enviar_para = 'C' THEN 'Colaborador'
			            ElSE 'Gestor'
			       END AS enviar_para,
			       CASE WHEN fl_enviar_para = 'C' THEN 'label label-info'
			            ElSE 'label label-success'
			       END AS class_enviar_para
			  FROM projetos.treinamento_colaborador_formulario f
			 WHERE f.dt_exclusao IS NULL
			   ".(intval($args['cd_treinamento_colaborador_tipo']) > 0 ? "AND f.cd_treinamento_colaborador_tipo = ".intval($args['cd_treinamento_colaborador_tipo']) : "")."
			   ".(trim($args['fl_enviar_para']) != '' ? "AND f.fl_enviar_para = '".trim($args['fl_enviar_para'])."'" : "")."
			   ".(trim($args['ds_treinamento_colaborador_formulario']) != '' ? "AND UPPER(f.ds_treinamento_colaborador_formulario) LIKE UPPER('%".trim($args["ds_treinamento_colaborador_formulario"])."%')" : "")."
			   ".(intval($args['nr_dias_envio']) > 0 ? "AND f.nr_dias_envio = ".intval($args['nr_dias_envio']) : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_treinamento_colaborador_formulario)
	{
		$qr_sql = "
			SELECT cd_treinamento_colaborador_formulario, 
			       ds_treinamento_colaborador_formulario,
				   nr_dias_envio,
				   fl_enviar_para,
				   CASE WHEN fl_enviar_para = 'C' THEN 'Colaborador'
			            ElSE 'Gestor'
			       END AS enviar_para
			  FROM projetos.treinamento_colaborador_formulario
			 WHERE cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_treinamento_colaborador_formulario = intval($this->db->get_new_id('projetos.treinamento_colaborador_formulario', 'cd_treinamento_colaborador_formulario'));

		$qr_sql = "
			INSERT INTO projetos.treinamento_colaborador_formulario
			     (
			       cd_treinamento_colaborador_formulario,
			       ds_treinamento_colaborador_formulario,
				   fl_enviar_para,
				   nr_dias_envio,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_treinamento_colaborador_formulario).",
                    ".(trim($args['ds_treinamento_colaborador_formulario']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario']) : "DEFAULT").",
			        ".(trim($args['fl_enviar_para']) != '' ? str_escape($args['fl_enviar_para']) : "DEFAULT").",
					".intval($args['nr_dias_envio']).",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );

			INSERT INTO projetos.treinamento_colaborador_formulario_tipo(cd_treinamento_colaborador_formulario, cd_treinamento_colaborador_tipo, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_treinamento_colaborador_formulario).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES (".implode("),(", $args['tipo']).")) x;";
				 
		$this->db->query($qr_sql);
		
		return $cd_treinamento_colaborador_formulario;
	}

	public function atualizar($cd_treinamento_colaborador_formulario, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_formulario
               SET ds_treinamento_colaborador_formulario = ".(trim($args['ds_treinamento_colaborador_formulario']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario']) : "DEFAULT").",
                   fl_enviar_para						 = ".(trim($args['fl_enviar_para']) != '' ? str_escape($args['fl_enviar_para']) : "DEFAULT").",
				   nr_dias_envio						 = ".intval($args['nr_dias_envio']).",
				   cd_usuario_alteracao                  = ".intval($args['cd_usuario']).",
                   dt_alteracao                          = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario).";

            UPDATE projetos.treinamento_colaborador_formulario_tipo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario)."
			   AND dt_exclusao                           IS NULL
			   AND cd_treinamento_colaborador_tipo       NOT IN (".implode(",", $args['tipo']).");
   
			INSERT INTO projetos.treinamento_colaborador_formulario_tipo(cd_treinamento_colaborador_formulario, cd_treinamento_colaborador_tipo, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_treinamento_colaborador_formulario).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES (".implode("),(", $args['tipo']).")) x
			 WHERE x.column1 NOT IN (SELECT a.cd_treinamento_colaborador_tipo
									   FROM projetos.treinamento_colaborador_formulario_tipo a
									  WHERE a.cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario)."
									    AND a.dt_exclusao IS NULL);";    

        $this->db->query($qr_sql);  
	}

	public function get_formulario_tipo($cd_treinamento_colaborador_formulario)
	{
		$qr_sql = "
			SELECT ft.cd_treinamento_colaborador_formulario_tipo,
			       ft.cd_treinamento_colaborador_tipo,
			       t.ds_treinamento_colaborador_tipo
			  FROM projetos.treinamento_colaborador_formulario_tipo ft
			  JOIN projetos.treinamento_colaborador_tipo t
			    ON t.cd_treinamento_colaborador_tipo = ft.cd_treinamento_colaborador_tipo
			 WHERE ft.dt_exclusao IS NULL
			   AND ft.cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario).";";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_estrutura_tipo()
	{
		$qr_sql = "
			SELECT cd_treinamento_colaborador_formulario_estrutura_tipo AS value,
				   ds_treinamento_colaborador_formulario_estrutura_tipo AS text
			  FROM projetos.treinamento_colaborador_formulario_estrutura_tipo
			 WHERE dt_exclusao IS NULL;";
			 
        return $this->db->query($qr_sql)->result_array();
	}

	public function estrutura_carrega($cd_treinamento_colaborador_formulario_estrutura)
	{
		$qr_sql = "
			SELECT f.cd_treinamento_colaborador_formulario,
				   f.cd_treinamento_colaborador_formulario_estrutura,
				   f.nr_treinamento_colaborador_formulario_estrutura,
				   f.ds_treinamento_colaborador_formulario_estrutura,
				   f.cd_treinamento_colaborador_formulario_estrutura_pai,
				   f.fl_obrigatorio,
			       t.ds_treinamento_colaborador_formulario_estrutura_tipo,
			       t.cd_treinamento_colaborador_formulario_estrutura_tipo,
				   t.ds_class
			  FROM projetos.treinamento_colaborador_formulario_estrutura f
			  JOIN projetos.treinamento_colaborador_formulario_estrutura_tipo t
			    ON t.cd_treinamento_colaborador_formulario_estrutura_tipo = f.cd_treinamento_colaborador_formulario_estrutura_tipo
			 WHERE f.dt_exclusao IS NULL
			   AND cd_treinamento_colaborador_formulario_estrutura = ".intval($cd_treinamento_colaborador_formulario_estrutura).";";
			 
        return $this->db->query($qr_sql)->row_array();
	}
	
	public function estrutura_listar($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura_pai = 0)
	{
		$qr_sql = "
			SELECT f.cd_treinamento_colaborador_formulario_estrutura,
			       f.ds_treinamento_colaborador_formulario_estrutura,
				   f.nr_treinamento_colaborador_formulario_estrutura,
				   f.cd_treinamento_colaborador_formulario_estrutura_pai,
				   f.cd_treinamento_colaborador_formulario,
				   f.fl_obrigatorio,
				   CASE WHEN f.fl_obrigatorio = 'S' THEN 'Sim'
			            ELSE 'Não'
			       END AS obrigatorio,
			       t.ds_treinamento_colaborador_formulario_estrutura_tipo,
			       t.cd_treinamento_colaborador_formulario_estrutura_tipo,
				   t.ds_class,
				   CASE WHEN t.cd_treinamento_colaborador_formulario_estrutura_tipo = 1 THEN 'D'
						WHEN t.cd_treinamento_colaborador_formulario_estrutura_tipo = 2 THEN 'O'
						WHEN t.cd_treinamento_colaborador_formulario_estrutura_tipo = 3 THEN 'S'
			            ElSE ''
			       END AS fl_tipo,
				   (SELECT COUNT(*)
						   FROM projetos.treinamento_colaborador_formulario_estrutura_conf c
				           WHERE c.dt_exclusao IS NULL
				           AND f.cd_treinamento_colaborador_formulario_estrutura = c.cd_treinamento_colaborador_formulario_estrutura)
			  FROM projetos.treinamento_colaborador_formulario_estrutura f
			  JOIN projetos.treinamento_colaborador_formulario_estrutura_tipo t
			    ON t.cd_treinamento_colaborador_formulario_estrutura_tipo = f.cd_treinamento_colaborador_formulario_estrutura_tipo
			 WHERE f.dt_exclusao IS NULL
			   AND ".(intval($cd_treinamento_colaborador_formulario_estrutura_pai) > 0 ? "f.cd_treinamento_colaborador_formulario_estrutura_pai = ".intval($cd_treinamento_colaborador_formulario_estrutura_pai) : "f.cd_treinamento_colaborador_formulario_estrutura_pai IS NULL")."
			   AND f.cd_treinamento_colaborador_formulario =".intval($cd_treinamento_colaborador_formulario)."
			 ORDER BY f.nr_treinamento_colaborador_formulario_estrutura ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function estrutura_salvar($args = array())
	{
		$cd_treinamento_colaborador_formulario_estrutura = intval($this->db->get_new_id('projetos.treinamento_colaborador_formulario_estrutura', 'cd_treinamento_colaborador_formulario_estrutura'));

		$qr_sql = "
			INSERT INTO projetos.treinamento_colaborador_formulario_estrutura
			     (
			       cd_treinamento_colaborador_formulario_estrutura,
			       ds_treinamento_colaborador_formulario_estrutura,
				   nr_treinamento_colaborador_formulario_estrutura,
			       cd_treinamento_colaborador_formulario_estrutura_tipo,
				   cd_treinamento_colaborador_formulario_estrutura_pai,
				   cd_treinamento_colaborador_formulario,
				   fl_obrigatorio,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_treinamento_colaborador_formulario_estrutura).",
                    ".(trim($args['ds_treinamento_colaborador_formulario_estrutura']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
			        ".(trim($args['nr_treinamento_colaborador_formulario_estrutura']) != '' ? intval($args['nr_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
					".(trim($args['cd_treinamento_colaborador_formulario_estrutura_tipo']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura_tipo']) : "DEFAULT").",
			        ".(trim($args['cd_treinamento_colaborador_formulario_estrutura_pai']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura_pai']) : "DEFAULT").",
					".(trim($args['cd_treinamento_colaborador_formulario']) != '' ? intval($args['cd_treinamento_colaborador_formulario']) : "DEFAULT").",
					".(trim($args['fl_obrigatorio']) != '' ? str_escape($args['fl_obrigatorio']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).", 
				    ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_treinamento_colaborador_formulario;
	}

	public function estrutura_atualizar($cd_treinamento_colaborador_formulario_estrutura, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_formulario_estrutura
               SET ds_treinamento_colaborador_formulario_estrutura 		= ".(trim($args['ds_treinamento_colaborador_formulario_estrutura']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
                   cd_treinamento_colaborador_formulario_estrutura_tipo = ".(trim($args['cd_treinamento_colaborador_formulario_estrutura_tipo']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura_tipo']) : "DEFAULT").",
                   cd_treinamento_colaborador_formulario_estrutura_pai  = ".(trim($args['cd_treinamento_colaborador_formulario_estrutura_pai']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura_pai']) : "DEFAULT").",
				   nr_treinamento_colaborador_formulario_estrutura		= ".(trim($args['nr_treinamento_colaborador_formulario_estrutura']) != '' ? intval($args['nr_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
				   cd_treinamento_colaborador_formulario				= ".(trim($args['cd_treinamento_colaborador_formulario']) != '' ? intval($args['cd_treinamento_colaborador_formulario']) : "DEFAULT").",
				   fl_obrigatorio										= ".(trim($args['fl_obrigatorio']) != '' ? str_escape($args['fl_obrigatorio']) : "DEFAULT").",
                   cd_usuario_alteracao                  				= ".intval($args['cd_usuario']).",
                   dt_alteracao                          				= CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_formulario_estrutura = ".intval($cd_treinamento_colaborador_formulario_estrutura).";";    

        $this->db->query($qr_sql);  
	}
	
	public function alterar_ordem($cd_treinamento_colaborador_formulario_estrutura,  $nr_treinamento_colaborador_formulario_estrutura, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_formulario_estrutura
               SET nr_treinamento_colaborador_formulario_estrutura = ".(trim($nr_treinamento_colaborador_formulario_estrutura) != '' ? intval($nr_treinamento_colaborador_formulario_estrutura) : "DEFAULT").",
				   cd_usuario_alteracao                  		   = ".intval($cd_usuario).",
                   dt_alteracao                          		   = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_formulario_estrutura = ".intval($cd_treinamento_colaborador_formulario_estrutura).";";    

        $this->db->query($qr_sql);  
	}
	
	public function get_estrutura_ordem($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura_pai = 0)
	{
		$qr_sql= "
			SELECT COALESCE(nr_treinamento_colaborador_formulario_estrutura + 1, 0) AS nr_treinamento_colaborador_formulario_estrutura
		      FROM projetos.treinamento_colaborador_formulario_estrutura
		     WHERE dt_exclusao IS NULL
	           AND cd_treinamento_colaborador_formulario = ".intval($cd_treinamento_colaborador_formulario)."
			   AND ".(intval($cd_treinamento_colaborador_formulario_estrutura_pai) > 0 ? "cd_treinamento_colaborador_formulario_estrutura_pai = ".intval($cd_treinamento_colaborador_formulario_estrutura_pai) : "cd_treinamento_colaborador_formulario_estrutura_pai IS NULL")."
             ORDER BY nr_treinamento_colaborador_formulario_estrutura DESC
	         LIMIT 1;";
		
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function configurar_salvar($args = array())
	{
		$cd_treinamento_colaborador_formulario_estrutura_conf = intval($this->db->get_new_id('projetos.treinamento_colaborador_formulario_estrutura_conf', 'cd_treinamento_colaborador_formulario_estrutura_conf'));

		$qr_sql = "
			INSERT INTO projetos.treinamento_colaborador_formulario_estrutura_conf
				 (  
				   cd_treinamento_colaborador_formulario_estrutura_conf,
				   nr_treinamento_colaborador_formulario_estrutura_conf,
				   ds_treinamento_colaborador_formulario_estrutura_conf,
				   cd_treinamento_colaborador_formulario_estrutura,
				   fl_campo_adicional,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			       ".intval($cd_treinamento_colaborador_formulario_estrutura_conf).",
			       ".(trim($args['nr_treinamento_colaborador_formulario_estrutura_conf']) != '' ? intval($args['nr_treinamento_colaborador_formulario_estrutura_conf']) : "DEFAULT").",
                   ".(trim($args['ds_treinamento_colaborador_formulario_estrutura_conf']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario_estrutura_conf']) : "DEFAULT").",
			       ".(trim($args['cd_treinamento_colaborador_formulario_estrutura']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
				   ".(trim($args['fl_campo_adicional']) != '' ? str_escape($args['fl_campo_adicional']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).", 
				   ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_treinamento_colaborador_formulario_estrutura;
	}

	public function configurar_atualizar($cd_treinamento_colaborador_formulario_estrutura_conf, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_formulario_estrutura_conf
               SET nr_treinamento_colaborador_formulario_estrutura_conf = ".(trim($args['nr_treinamento_colaborador_formulario_estrutura_conf']) != '' ? intval($args['nr_treinamento_colaborador_formulario_estrutura_conf']) : "DEFAULT").",
                   ds_treinamento_colaborador_formulario_estrutura_conf = ".(trim($args['ds_treinamento_colaborador_formulario_estrutura_conf']) != '' ? str_escape($args['ds_treinamento_colaborador_formulario_estrutura_conf']) : "DEFAULT").",
				   cd_treinamento_colaborador_formulario_estrutura      = ".(trim($args['cd_treinamento_colaborador_formulario_estrutura']) != '' ? intval($args['cd_treinamento_colaborador_formulario_estrutura']) : "DEFAULT").",
				   fl_campo_adicional									= ".(trim($args['fl_campo_adicional']) != '' ? str_escape($args['fl_campo_adicional']) : "DEFAULT").",
				   cd_usuario_alteracao                  				= ".intval($args['cd_usuario']).",
                   dt_alteracao                          				= CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_formulario_estrutura_conf = ".intval($cd_treinamento_colaborador_formulario_estrutura_conf).";";    

        $this->db->query($qr_sql);  
	}
	
	public function configurar_carrega($cd_treinamento_colaborador_formulario_estrutura_conf)
	{
		$qr_sql = "
			SELECT c.cd_treinamento_colaborador_formulario_estrutura_conf,
				   c.nr_treinamento_colaborador_formulario_estrutura_conf,
				   c.ds_treinamento_colaborador_formulario_estrutura_conf,
				   c.fl_campo_adicional,
				   e.cd_treinamento_colaborador_formulario_estrutura,
				   e.cd_treinamento_colaborador_formulario
			  FROM projetos.treinamento_colaborador_formulario_estrutura_conf c
			  JOIN projetos.treinamento_colaborador_formulario_estrutura e
			    ON e.cd_treinamento_colaborador_formulario_estrutura = c.cd_treinamento_colaborador_formulario_estrutura
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_treinamento_colaborador_formulario_estrutura_conf = ".intval($cd_treinamento_colaborador_formulario_estrutura_conf).";";
			 
        return $this->db->query($qr_sql)->row_array();
	}
	
	public function configurar_listar($cd_treinamento_colaborador_formulario_estrutura)
	{
		$qr_sql = "
			SELECT c.cd_treinamento_colaborador_formulario_estrutura_conf,
				   c.nr_treinamento_colaborador_formulario_estrutura_conf,
				   c.ds_treinamento_colaborador_formulario_estrutura_conf,
				   c.fl_campo_adicional,
				   CASE WHEN c.fl_campo_adicional = 'S' THEN 'Sim'
			            ELSE 'Não'
			       END AS campo_adicional,
				   e.cd_treinamento_colaborador_formulario_estrutura,
				   e.cd_treinamento_colaborador_formulario
			  FROM projetos.treinamento_colaborador_formulario_estrutura_conf c
			  JOIN projetos.treinamento_colaborador_formulario_estrutura e
			    ON e.cd_treinamento_colaborador_formulario_estrutura = c.cd_treinamento_colaborador_formulario_estrutura
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_treinamento_colaborador_formulario_estrutura = ".intval($cd_treinamento_colaborador_formulario_estrutura)."
			 ORDER BY c.nr_treinamento_colaborador_formulario_estrutura_conf ASC;";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function altera_ordem_configurar($cd_treinamento_colaborador_formulario_estrutura_conf,  $nr_treinamento_colaborador_formulario_estrutura_conf, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_formulario_estrutura_conf
               SET nr_treinamento_colaborador_formulario_estrutura_conf = ".(trim($nr_treinamento_colaborador_formulario_estrutura_conf) != '' ? intval($nr_treinamento_colaborador_formulario_estrutura_conf) : "DEFAULT").",
				   cd_usuario_alteracao                  		        = ".intval($cd_usuario).",
                   dt_alteracao                          		        = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_formulario_estrutura_conf = ".intval($cd_treinamento_colaborador_formulario_estrutura_conf).";";    
			 
        $this->db->query($qr_sql);  
	}
	
	public function get_configurar_ordem($cd_treinamento_colaborador_formulario_estrutura)
	{
		$qr_sql= "
			SELECT COALESCE(nr_treinamento_colaborador_formulario_estrutura_conf + 1, 0) AS nr_treinamento_colaborador_formulario_estrutura_conf
		      FROM projetos.treinamento_colaborador_formulario_estrutura_conf
		     WHERE dt_exclusao IS NULL
	           AND cd_treinamento_colaborador_formulario_estrutura = ".intval($cd_treinamento_colaborador_formulario_estrutura)."
			 ORDER BY nr_treinamento_colaborador_formulario_estrutura_conf DESC
	         LIMIT 1;";
		
		return $this->db->query($qr_sql)->row_array();
	}
	
}