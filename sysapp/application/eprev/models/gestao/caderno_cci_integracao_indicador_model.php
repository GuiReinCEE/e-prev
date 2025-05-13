<?php
class Caderno_cci_integracao_indicador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_integracao_indicador,
				   ds_indicador,
			       ds_caderno_cci_integracao_indicador
			  FROM gestao.caderno_cci_integracao_indicador
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['ds_indicador']) != '' ? "AND UPPER(ds_indicador) LIKE UPPER('%".trim($args["ds_indicador"])."%')" : "")."
			 ".(trim($args['ds_caderno_cci_integracao_indicador']) != '' ? "AND UPPER(ds_caderno_cci_integracao_indicador) LIKE UPPER('%".trim($args["ds_caderno_cci_integracao_indicador"])."%')" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_caderno_cci_integracao_indicador)
	{
		$qr_sql = "
			SELECT cd_caderno_cci_integracao_indicador,
				   ds_indicador,
				   ds_caderno_cci_integracao_indicador
			  FROM gestao.caderno_cci_integracao_indicador
			 WHERE cd_caderno_cci_integracao_indicador = ".intval($cd_caderno_cci_integracao_indicador).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_caderno_cci_integracao_indicador = intval($this->db->get_new_id('gestao.caderno_cci_integracao_indicador', 'cd_caderno_cci_integracao_indicador'));

		$qr_sql = "
			INSERT INTO gestao.caderno_cci_integracao_indicador
			     (
			       cd_caderno_cci_integracao_indicador,
				   ds_indicador,
			       ds_caderno_cci_integracao_indicador,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_caderno_cci_integracao_indicador).",
                    ".(trim($args['ds_indicador']) != '' ? str_escape($args['ds_indicador']) : "DEFAULT").",
			        ".(trim($args['ds_caderno_cci_integracao_indicador']) != '' ? str_escape($args['ds_caderno_cci_integracao_indicador']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_caderno_cci_integracao_indicador;
	}

	public function atualizar($cd_caderno_cci_integracao_indicador, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_integracao_indicador
               SET ds_indicador						   = ".(trim($args['ds_indicador']) != '' ? str_escape($args['ds_indicador']) : "DEFAULT").",
				   ds_caderno_cci_integracao_indicador = ".(trim($args['ds_caderno_cci_integracao_indicador']) != '' ? str_escape($args['ds_caderno_cci_integracao_indicador']) : "DEFAULT").",
                   cd_usuario_alteracao                = ".intval($args['cd_usuario']).",
                   dt_alteracao                        = CURRENT_TIMESTAMP
             WHERE cd_caderno_cci_integracao_indicador = ".intval($cd_caderno_cci_integracao_indicador).";";    

        $this->db->query($qr_sql);  
	}
	
	public function integracao_listar($cd_caderno_cci_integracao_indicador, $cd_caderno_cci_integracao_indicador_campo = 0)
	{
		$qr_sql = "
			SELECT fl_referencia_tabela AS value,
			       CASE WHEN fl_referencia_tabela = 'P' THEN 'Projetado' 
			            WHEN fl_referencia_tabela = 'I' THEN 'Índice'
			            WHEN fl_referencia_tabela = 'B' THEN 'Benchmark'
			            ELSE 'Estrutura'
			       END AS referencia_tabela,
				   cd_caderno_cci_integracao_indicador_campo,
				   ds_caderno_cci_integracao_indicador_campo,
			       cd_caderno_cci_integracao_indicador,
			       cd_referencia_integracao
			  FROM gestao.caderno_cci_integracao_indicador_campo
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci_integracao_indicador =".intval($cd_caderno_cci_integracao_indicador).";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
			 
	public function integracao_salvar($args = array())
	{
		$cd_caderno_cci_integracao_indicador_campo = intval($this->db->get_new_id('gestao.caderno_cci_integracao_indicador_campo', 'cd_caderno_cci_integracao_indicador_campo'));

		$qr_sql = "
			INSERT INTO gestao.caderno_cci_integracao_indicador_campo
			     (
			       cd_caderno_cci_integracao_indicador_campo,
			       fl_referencia_tabela,
				   ds_caderno_cci_integracao_indicador_campo,
			       cd_caderno_cci_integracao_indicador	,
				   cd_referencia_integracao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_caderno_cci_integracao_indicador_campo).",
                    ".(trim($args['fl_referencia_tabela']) != '' ? str_escape($args['fl_referencia_tabela']) : "DEFAULT").",
                    ".(trim($args['ds_caderno_cci_integracao_indicador_campo']) != '' ? str_escape($args['ds_caderno_cci_integracao_indicador_campo']) : "DEFAULT").",
			        ".(trim($args['cd_caderno_cci_integracao_indicador']) != '' ? intval($args['cd_caderno_cci_integracao_indicador']) : "DEFAULT").",
					".(trim($args['cd_referencia_integracao']) != '' ? str_escape($args['cd_referencia_integracao']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).", 
				    ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_caderno_cci_integracao_indicador_campo;
	}
	
	public function integracao_atualizar($cd_caderno_cci_integracao_indicador_campo, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_integracao_indicador_campo
               SET fl_referencia_tabela 					 = ".(trim($args['fl_referencia_tabela']) != '' ? str_escape($args['fl_referencia_tabela']) : "DEFAULT").",
				   ds_caderno_cci_integracao_indicador_campo = ".(trim($args['ds_caderno_cci_integracao_indicador_campo']) != '' ? str_escape($args['ds_caderno_cci_integracao_indicador_campo']) : "DEFAULT").",
				   cd_caderno_cci_integracao_indicador  	 = ".(trim($args['cd_caderno_cci_integracao_indicador']) != '' ? intval($args['cd_caderno_cci_integracao_indicador']) : "DEFAULT").",
				   cd_referencia_integracao 				 = ".(trim($args['cd_referencia_integracao']) != '' ? str_escape($args['cd_referencia_integracao']) : "DEFAULT").", 
				   cd_usuario_alteracao                  	 = ".intval($args['cd_usuario']).",
                   dt_alteracao                          	 = CURRENT_TIMESTAMP
             WHERE cd_caderno_cci_integracao_indicador_campo = ".intval($cd_caderno_cci_integracao_indicador_campo).";";    

        $this->db->query($qr_sql);  
	}	
	
	public function integracao_carrega($cd_caderno_cci_integracao_indicador_campo)
	{
		$qr_sql = "
			SELECT cd_caderno_cci_integracao_indicador_campo,
				   fl_referencia_tabela,
				   ds_caderno_cci_integracao_indicador_campo,
				   cd_referencia_integracao,
				   cd_caderno_cci_integracao_indicador
			  FROM gestao.caderno_cci_integracao_indicador_campo
			 WHERE cd_caderno_cci_integracao_indicador_campo = ".intval($cd_caderno_cci_integracao_indicador_campo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_campo_atualizar($ds_caderno_cci_integracao_indicador)
	{
		$qr_sql = "
			SELECT c.fl_referencia_tabela,
			       c.ds_caderno_cci_integracao_indicador_campo,
			       c.cd_referencia_integracao
			  FROM gestao.caderno_cci_integracao_indicador i
			  JOIN gestao.caderno_cci_integracao_indicador_campo c
			    ON c.cd_caderno_cci_integracao_indicador = i.cd_caderno_cci_integracao_indicador
			 WHERE i.ds_caderno_cci_integracao_indicador = ".str_escape($ds_caderno_cci_integracao_indicador).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_valor_projetado($ano, $cd_referencia_integracao)
	{
		$qr_sql = "
			SELECT p.nr_projetado 
			  FROM gestao.caderno_cci c
			  JOIN gestao.caderno_cci_projetado p
			    ON p.cd_caderno_cci = c.cd_caderno_cci
			 WHERE c.dt_exclusao IS NULL
			   AND p.dt_exclusao IS NULL
			   AND c.nr_ano                         = ".intval($ano)."
			   AND TRIM(p.cd_referencia_integracao) = ".str_escape($cd_referencia_integracao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_valor_indice($ano, $mes, $cd_referencia_integracao)
	{
		$qr_sql = "
			SELECT iv.nr_indice
			  FROM gestao.caderno_cci_indice i
			  JOIN gestao.caderno_cci_indice_valor iv
			    ON iv.cd_caderno_cci_indice = i.cd_caderno_cci_indice
			 WHERE i.dt_exclusao IS NULL
			   AND iv.dt_exclusao IS NULL  
			   AND TO_CHAR(iv.dt_referencia, 'YYYY') = '".intval($ano)."'
			   AND TO_CHAR(iv.dt_referencia, 'MM')   = ".str_escape($mes)."
			   AND TRIM(i.cd_referencia_integracao)  = ".str_escape($cd_referencia_integracao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_valor_benchmark($ano, $mes, $cd_referencia_integracao)
	{
		$qr_sql = "
			SELECT bv.nr_benchmark
			  FROM gestao.caderno_cci_benchmark b
			  JOIN gestao.caderno_cci_benchmark_valor bv
			    ON bv.cd_caderno_cci_benchmark = b.cd_caderno_cci_benchmark
			 WHERE b.dt_exclusao IS NULL
			   AND bv.dt_exclusao IS NULL  
			   AND TO_CHAR(bv.dt_referencia, 'YYYY') = '".intval($ano)."'
			   AND TO_CHAR(bv.dt_referencia, 'MM')   = ".str_escape($mes)."
			   AND TRIM(b.cd_referencia_integracao)  = ".str_escape($cd_referencia_integracao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_valor_estrutura($ano, $mes, $cd_referencia_integracao)
	{
		$qr_sql = "
			SELECT ev.nr_rentabilidade
			  FROM gestao.caderno_cci_estrutura e
			  JOIN gestao.caderno_cci_estrutura_valor ev
			    ON ev.cd_caderno_cci_estrutura = e.cd_caderno_cci_estrutura
			 WHERE e.dt_exclusao IS NULL
			   AND ev.dt_exclusao IS NULL  
			   AND TO_CHAR(ev.dt_referencia, 'YYYY') = '".intval($ano)."'
			   AND TO_CHAR(ev.dt_referencia, 'MM')   = ".str_escape($mes)."
			   AND TRIM(e.cd_referencia_integracao)  = ".str_escape($cd_referencia_integracao).";";

		return $this->db->query($qr_sql)->row_array();
	}
}