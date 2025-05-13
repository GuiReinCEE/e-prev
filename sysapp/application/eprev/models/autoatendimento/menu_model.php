<?php
class Menu_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array(), $cd_menu_pai = 0)
	{
		$qr_sql = "
			SELECT cd_menu,
				   cd_menu_pai,
				   nr_ordem,
				   ds_codigo,
			       ds_menu,
				   CASE WHEN fl_status = 'D' THEN 'Desativado'
						ELSE 'Ativo'
				   END AS status,
				   CASE WHEN fl_status = 'D' THEN 'label label-important'
						ELSE 'label label-success'
				   END AS class_status,
				   ds_resumo
			  FROM autoatendimento.menu
			 WHERE 1 = 1
			   ".(trim($args['ds_menu']) != '' ? "AND UPPER(ds_menu) LIKE UPPER('%".trim($args["ds_menu"])."%')" : "")."
			   ".(trim($args['fl_status']) != '' ? "AND UPPER(fl_status) LIKE UPPER('%".trim($args["fl_status"])."%')" : "")."
			   AND ".(intval($cd_menu_pai) > 0 ? "cd_menu_pai = ".intval($cd_menu_pai) : "cd_menu_pai IS NULL").";";
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_menu)
	{
		$qr_sql = "
			SELECT cd_menu,
			       cd_menu_pai,
				   ds_codigo,
				   ds_menu,
				   nr_ordem,
				   fl_status,
				   ds_href,
				   ds_icone,
				   ds_resumo
			  FROM autoatendimento.menu
			 WHERE cd_menu = ".intval($cd_menu).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_menu = intval($this->db->get_new_id('autoatendimento.menu', 'cd_menu'));

		$qr_sql = "
			INSERT INTO autoatendimento.menu
			     (
			       cd_menu,
			       cd_menu_pai,
				   ds_codigo,
				   ds_menu,
				   nr_ordem,
				   fl_status,
				   ds_href,
				   ds_icone,
				   ds_resumo,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_menu).",
			     	".(intval($args['cd_menu_pai']) > 0 ? intval($args['cd_menu_pai']) : "DEFAULT").",
					".(trim($args['ds_codigo']) != '' ? str_escape($args['ds_codigo']) : "DEFAULT").",
                    ".(trim($args['ds_menu']) != '' ? str_escape($args['ds_menu']) : "DEFAULT").",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                    ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
                    ".(trim($args['ds_href']) != '' ? str_escape($args['ds_href']) : "DEFAULT").",
                    ".(trim($args['ds_icone']) != '' ? str_escape($args['ds_icone']) : "DEFAULT").",
			        ".(trim($args['ds_resumo']) != '' ? str_escape($args['ds_resumo']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
			     );

			INSERT INTO autoatendimento.menu_tipo_participante(cd_menu, tipo_participante, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_menu).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES ('".implode("'),('", $args['tipo_participante'])."')) x;

			INSERT INTO autoatendimento.menu_patrocinadoras(cd_menu, cd_empresa, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_menu).", y.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES (".implode("),(", $args['empresa']).")) y;";
			     
		$this->db->query($qr_sql);

		return $cd_menu;
	}

	public function atualizar($cd_menu, $args = array())
	{
		$qr_sql = "
			UPDATE autoatendimento.menu
               SET ds_codigo			= ".(trim($args['ds_codigo']) != '' ? str_escape($args['ds_codigo']) : "DEFAULT").",
				   ds_menu    			= ".(trim($args['ds_menu']) != '' ? str_escape($args['ds_menu']) : "DEFAULT").",
				   nr_ordem 			= ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
				   fl_status    		= ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
				   ds_href      		= ".(trim($args['ds_href']) != '' ? str_escape($args['ds_href']) : "DEFAULT").",
				   ds_icone     		= ".(trim($args['ds_icone']) != '' ? str_escape($args['ds_icone']) : "DEFAULT").",
                   ds_resumo   			= ".(trim($args['ds_resumo']) != '' ? str_escape($args['ds_resumo']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao 		= CURRENT_TIMESTAMP
             WHERE cd_menu = ".intval($cd_menu).";

            UPDATE autoatendimento.menu_tipo_participante
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_menu           = ".intval($cd_menu)."
			   AND dt_exclusao       IS NULL
			   AND tipo_participante NOT IN ('".implode("','", $args['tipo_participante'])."');
   
			INSERT INTO autoatendimento.menu_tipo_participante(cd_menu, tipo_participante, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_menu).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES ('".implode("'),('", $args['tipo_participante'])."')) x
			 WHERE x.column1 NOT IN (SELECT a.tipo_participante
									   FROM autoatendimento.menu_tipo_participante a
									  WHERE a.cd_menu = ".intval($cd_menu)."
									    AND a.dt_exclusao IS NULL);

			UPDATE autoatendimento.menu_patrocinadoras
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_menu           = ".intval($cd_menu)."
			   AND dt_exclusao       IS NULL
			   AND cd_empresa NOT IN (".implode(",", $args['empresa']).");
   
			INSERT INTO autoatendimento.menu_patrocinadoras(cd_menu, cd_empresa, cd_usuario_inclusao, cd_usuario_alteracao)
			SELECT ".intval($cd_menu).", y.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
			  FROM (VALUES (".implode("),(", $args['empresa']).")) y
			 WHERE y.column1 NOT IN (SELECT b.cd_empresa
									   FROM autoatendimento.menu_patrocinadoras b
									  WHERE b.cd_menu = ".intval($cd_menu)."
									    AND b.dt_exclusao IS NULL);";        

        $this->db->query($qr_sql);  
	}
	
	public function alterar_ordem($cd_menu, $nr_ordem, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.menu
               SET nr_ordem   			= ".(trim($nr_ordem) != '' ? intval($nr_ordem) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_menu = ".intval($cd_menu).";";    

        $this->db->query($qr_sql);  
	}
	
	public function sub_menu_listar($cd_menu)
	{
		$qr_sql = "
			SELECT cd_menu,
				   cd_menu_pai,
				   nr_ordem,
				   ds_codigo,
			       ds_menu,
				   CASE WHEN fl_status = 'D' THEN 'Desativado'
						ELSE 'Ativo'
				   END AS status,
				   CASE WHEN fl_status = 'D' THEN 'label label-important'
						ELSE 'label label-success'
				   END AS class_status,
				   ds_resumo
			  FROM autoatendimento.menu
			 WHERE cd_menu_pai = ".intval($cd_menu)."
			 ORDER BY nr_ordem;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_menu_ordem($cd_menu_pai = 0)
	{
		$qr_sql = "
			SELECT COALESCE(nr_ordem + 1, 0) AS nr_ordem
		      FROM autoatendimento.menu
		     WHERE dt_exclusao IS NULL
			   AND ".(intval($cd_menu_pai) > 0 ? "cd_menu_pai = ".intval($cd_menu_pai) : "cd_menu_pai IS NULL")."
			   AND fl_status   = 'A'
             ORDER BY nr_ordem DESC
	         LIMIT 1;";
		
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_empresa()
	{
		$qr_sql = "
			SELECT p.cd_empresa AS value,
			       COALESCE(pl.ds_patrocinadoras_libera, p.sigla) AS text
		      FROM autoatendimento.patrocinadoras_libera pl
		      JOIN public.patrocinadoras p
			    ON p.cd_empresa = pl.cd_empresa
		     WHERE pl.dt_exclusao IS NULL
		     ORDER BY pl.nr_ordem;";
				  
		return $this->db->query($qr_sql)->result_array();
	}	

	public function menu_tipo_participante($cd_menu)
	{
		$qr_sql = "
			SELECT tipo_participante, 
				   tipo_participante AS value,
				   CASE WHEN tipo_participante = 'ATIV' THEN 'Ativo'
				        WHEN tipo_participante = 'APOS' THEN 'Aposentado'
				        WHEN tipo_participante = 'EXAU' THEN 'Ex Autárquico'
						WHEN tipo_participante = 'CTP' THEN 'CTP'
						WHEN tipo_participante = 'AUXD' THEN 'Auxilio Doença'
						ELSE 'Pensionista'
				   END AS text
			  FROM autoatendimento.menu_tipo_participante
			 WHERE cd_menu = ".intval($cd_menu)."
			   AND dt_exclusao IS NULL
			 ORDER BY tipo_participante;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function menu_patrocinadoras($cd_menu)
	{
		$qr_sql = "
			SELECT p.cd_empresa,
				   p.cd_empresa AS value,
			       COALESCE(pl.ds_patrocinadoras_libera, p.sigla) AS text
		      FROM autoatendimento.patrocinadoras_libera pl
		      JOIN public.patrocinadoras p
			    ON p.cd_empresa = pl.cd_empresa
			  JOIN autoatendimento.menu_patrocinadoras mp
			    ON mp.cd_empresa = pl.cd_empresa
		     WHERE pl.dt_exclusao IS NULL
			   AND mp.dt_exclusao IS NULL
		       AND mp.cd_menu = ".intval($cd_menu)."
		     ORDER BY mp.cd_empresa;";

		return $this->db->query($qr_sql)->result_array();
	}
}