<?php
class Controladoria_obrigacoes_legais_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_controladoria_obrigacoes_legais,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   fl_media,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_fgts = 1 THEN 'Não'
				        WHEN nr_fgts = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS fgts,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_inss = 1 THEN 'Não'
				        WHEN nr_inss = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS inss,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_balancete = 1 THEN 'Não'
				        WHEN nr_balancete = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS balancete,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_demostracoes = 1 THEN 'Não'
				        WHEN nr_demostracoes = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS demostracoes,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_dctf = 1 THEN 'Não'
				        WHEN nr_dctf = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS dctf,
				   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_di = 1 THEN 'Não'
				        WHEN nr_di = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS di,
                   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_raiz = 1 THEN 'Não'
				        WHEN nr_raiz = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS raiz,
                   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_dirf = 1 THEN 'Não'
				        WHEN nr_dirf = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS dirf,
                   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_caged = 1 THEN 'Não'
				        WHEN nr_caged = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS caged,
                   CASE WHEN fl_media = 'S' THEN ''
				        WHEN nr_tce = 1 THEN 'Não'
				        WHEN nr_tce = 2 THEN 'Sim'
				        ELSE 'Não se aplica'
                   END AS nr_tce,
				   nr_resultado,
				   nr_meta,
				   nr_obr_previstas,
  				   nr_obr_cumpridas,
  				   nr_raiz,
                   nr_dirf,
				   nr_caged,
				   observacao
			  FROM indicador_plugin.controladoria_obrigacoes_legais 
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela, $nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.controladoria_obrigacoes_legais
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.controladoria_obrigacoes_legais 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_controladoria_obrigacoes_legais)
	{
		$qr_sql = "
			SELECT cd_controladoria_obrigacoes_legais,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_fgts,
				   nr_inss,
				   nr_balancete,
				   nr_demostracoes,
				   nr_dctf,
				   nr_di,
				   nr_meta,
				   nr_raiz,
				   nr_dirf,
                   nr_caged,
                   nr_tce,
				   observacao
			  FROM indicador_plugin.controladoria_obrigacoes_legais 
			 WHERE dt_exclusao IS NULL
			   AND cd_controladoria_obrigacoes_legais = ".intval($cd_controladoria_obrigacoes_legais)."
			 ORDER BY dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.controladoria_obrigacoes_legais
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
				   nr_fgts,
				   nr_inss,
				   nr_balancete,
				   nr_demostracoes,
				   nr_dctf,
				   nr_di,
				   nr_raiz,
				   nr_dirf,
                   nr_caged,
                   nr_tce, 
				   nr_meta, 
                   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_fgts']) != '' ? intval($args['nr_fgts']) : "DEFAULT").",
				   ".(trim($args['nr_inss']) != '' ? intval($args['nr_inss']) : "DEFAULT").",
				   ".(trim($args['nr_balancete']) != '' ? intval($args['nr_balancete']) : "DEFAULT").",
				   ".(trim($args['nr_demostracoes']) != '' ? intval($args['nr_demostracoes']) : "DEFAULT").",
				   ".(trim($args['nr_dctf']) != '' ? intval($args['nr_dctf']) : "DEFAULT").",
				   ".(trim($args['nr_di']) != '' ? intval($args['nr_di']) : "DEFAULT").",
				   ".(trim($args['nr_raiz']) != '' ? intval($args['nr_raiz']) : "DEFAULT").",
				   ".(trim($args['nr_dirf']) != '' ? intval($args['nr_dirf']) : "DEFAULT").",
                   ".(trim($args['nr_caged']) != '' ? intval($args['nr_caged']) : "DEFAULT").",
                   ".(trim($args['nr_tce']) != '' ? intval($args['nr_tce']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function atualizar($cd_controladoria_obrigacoes_legais, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.controladoria_obrigacoes_legais
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
			       nr_fgts              = ".(trim($args['nr_fgts']) != '' ? intval($args['nr_fgts']) : "DEFAULT").",
				   nr_inss              = ".(trim($args['nr_inss']) != '' ? intval($args['nr_inss']) : "DEFAULT").",
				   nr_balancete         = ".(trim($args['nr_balancete']) != '' ? intval($args['nr_balancete']) : "DEFAULT").",
				   nr_demostracoes      = ".(trim($args['nr_demostracoes']) != '' ? intval($args['nr_demostracoes']) : "DEFAULT").",
				   nr_dctf              = ".(trim($args['nr_dctf']) != '' ? intval($args['nr_dctf']) : "DEFAULT").",
				   nr_di                = ".(trim($args['nr_di']) != '' ? intval($args['nr_di']) : "DEFAULT").",
				   nr_raiz              = ".(trim($args['nr_raiz']) != '' ? intval($args['nr_raiz']) : "DEFAULT").",
				   nr_dirf              = ".(trim($args['nr_dirf']) != '' ? intval($args['nr_dirf']) : "DEFAULT").",
                   nr_caged             = ".(trim($args['nr_caged']) != '' ? intval($args['nr_caged']) : "DEFAULT").", 
                   nr_tce               = ".(trim($args['nr_tce']) != '' ? intval($args['nr_tce']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_controladoria_obrigacoes_legais = ".intval($cd_controladoria_obrigacoes_legais).";";

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_controladoria_obrigacoes_legais, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_obrigacoes_legais 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_controladoria_obrigacoes_legais = ".intval($cd_controladoria_obrigacoes_legais).";"; 

		$this->db->query($qr_sql);
	}	

	public function fechar_ano($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.controladoria_obrigacoes_legais
			     (
                   dt_referencia, 
                   cd_indicador_tabela, 
                   fl_media, 
                   nr_obr_previstas,
                   nr_obr_cumpridas,
				   nr_meta, 
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
            VALUES 
			     (
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['nr_obr_previstas']) != '' ? intval($args['nr_obr_previstas']) : "DEFAULT").",
				   ".(trim($args['nr_obr_cumpridas']) != '' ? intval($args['nr_obr_cumpridas']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";


		$this->db->query($qr_sql);	
	}

	public function fechar_indicador($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";
			 
		$this->db->query($qr_sql);
	}
}