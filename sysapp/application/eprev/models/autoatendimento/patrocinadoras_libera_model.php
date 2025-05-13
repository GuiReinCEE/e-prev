<?php
class Patrocinadoras_libera_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function alterar_ordem($cd_patrocinadoras_libera, $nr_ordem, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.patrocinadoras_libera
               SET nr_ordem   			= ".(trim($nr_ordem) != '' ? intval($nr_ordem) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_patrocinadoras_libera = ".intval($cd_patrocinadoras_libera).";";    

        $this->db->query($qr_sql);  
	}
	
	public function listar()
	{
		$qr_sql = "
			SELECT pl.cd_patrocinadoras_libera,
				   pl.nr_ordem,
				   pl.nr_ano,
				   pl.ds_patrocinadoras_libera,
				   p.sigla
			  FROM autoatendimento.patrocinadoras_libera pl
			  JOIN public.patrocinadoras p
			    ON p.cd_empresa = pl.cd_empresa
			 WHERE 1=1;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_patrocinadoras_libera)
	{
		$qr_sql = "
			SELECT pl.cd_patrocinadoras_libera,
				   pl.cd_empresa,
				   pl.nr_ordem,
				   pl.nr_ano,
				   pl.ds_patrocinadoras_libera,
				   p.sigla
			  FROM autoatendimento.patrocinadoras_libera pl
			  JOIN public.patrocinadoras p
			    ON p.cd_empresa = pl.cd_empresa
			 WHERE cd_patrocinadoras_libera = ".intval($cd_patrocinadoras_libera).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_patrocinadoras_libera = intval($this->db->get_new_id('autoatendimento.patrocinadoras_libera', 'cd_patrocinadoras_libera'));

		$qr_sql = "
			INSERT INTO autoatendimento.patrocinadoras_libera
			     (
			       cd_patrocinadoras_libera,
				   nr_ordem,
				   nr_ano,
				   cd_empresa,
				   ds_patrocinadoras_libera,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_patrocinadoras_libera).",
                    ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
			     	".(intval($args['nr_ano']) > 0 ? intval($args['nr_ano']) : "DEFAULT").",
					".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					".(trim($args['ds_patrocinadoras_libera']) != '' ? str_escape($args['ds_patrocinadoras_libera']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
			     );";
			     
		$this->db->query($qr_sql);

		return $cd_patrocinadoras_libera;
	}

	public function atualizar($cd_patrocinadoras_libera, $args = array())
	{
		$qr_sql = "
			UPDATE autoatendimento.patrocinadoras_libera
               SET nr_ordem 			    = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
				   nr_ano 			        = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
				   ds_patrocinadoras_libera = ".(trim($args['ds_patrocinadoras_libera']) != '' ? str_escape($args['ds_patrocinadoras_libera']) : "DEFAULT").",
				   cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
				   dt_alteracao 		    = CURRENT_TIMESTAMP
             WHERE cd_patrocinadoras_libera = ".intval($cd_patrocinadoras_libera).";";

        $this->db->query($qr_sql);  
	}

	public function get_menu_ordem()
	{
		$qr_sql = "
			SELECT COALESCE(nr_ordem + 1, 0) AS nr_ordem
		      FROM autoatendimento.patrocinadoras_libera
		     WHERE dt_exclusao IS NULL
             ORDER BY nr_ordem DESC
	         LIMIT 1;";
		
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_empresa()
	{
		$qr_sql = "
			SELECT p.cd_empresa AS value,
				   p.sigla AS text
			  FROM public.patrocinadoras p
			 WHERE p.cd_empresa NOT IN (SELECT pl.cd_empresa 
										  FROM autoatendimento.patrocinadoras_libera pl 
										 WHERE pl.dt_exclusao IS NULL);";
										 
		return $this->db->query($qr_sql)->result_array();
	}
	
}