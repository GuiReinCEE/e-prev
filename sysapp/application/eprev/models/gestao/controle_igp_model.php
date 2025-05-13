<?php
class Controle_igp_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($args)
	{
		$qr_sql = "
			SELECT ci.cd_controle_igp,
			       ci.nr_ano,
			        TO_CHAR(ci.dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') as dt_fechamento,
			       funcoes.get_usuario_nome(cd_usuario_fechamento) as cd_usuario
			  FROM gestao.controle_igp ci
			 WHERE ci.dt_exclusao IS NULL
			   ".(trim($args['nr_ano']) != '' ? "AND ci.nr_ano = ".intval($args['nr_ano']) : "").";";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_controle_igp)
	{
		$qr_sql = "
			SELECT cd_controle_igp,
			       nr_ano,
			       dt_fechamento
			  FROM gestao.controle_igp  
			 WHERE cd_controle_igp = ".intval($cd_controle_igp).";";		

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_controle_igp = intval($this->db->get_new_id('gestao.controle_igp', 'cd_controle_igp'));

		$qr_sql = "
			INSERT INTO gestao.controle_igp
			     (
			       cd_controle_igp,
			       nr_ano,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_controle_igp).",
			        ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);

		return $cd_controle_igp;
	}

	public function get_indicador_igp($cd_controle_igp, $cd_indicador = 0)
	{
		$qr_sql = "
			SELECT i.cd_indicador AS value,
				   i.ds_indicador AS text
			  FROM indicador.indicador i	
			 WHERE i.dt_exclusao IS NULL
			   AND i.fl_igp = 'S'
			   AND (i.cd_indicador NOT IN (SELECT cii.cd_indicador
			                                FROM gestao.controle_igp_indicador cii
			                               WHERE cii.dt_exclusao IS NULL
			                               	 AND cii.cd_controle_igp = ".intval($cd_controle_igp).")
			       OR 
			       i.cd_indicador = ".intval($cd_indicador).")
			 ORDER BY i.ds_indicador;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ordem($cd_controle_igp)
	{
		$qr_sql = "
			SELECT (nr_ordem + 1) AS nr_ordem
			  FROM gestao.controle_igp_indicador
			 WHERE dt_exclusao IS NULL
               AND cd_controle_igp = ".intval($cd_controle_igp)."
			 ORDER BY nr_ordem DESC;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_controle_indicador($args = array())
	{
		$cd_controle_igp_indicador = intval($this->db->get_new_id('gestao.controle_igp_indicador', 'cd_controle_igp_indicador'));

		$qr_sql = "
			INSERT INTO gestao.controle_igp_indicador
				 (
            		cd_controle_igp_indicador, 
            		cd_controle_igp, 
            		nr_ordem,
            		cd_indicador, 
            		cd_controle_igp_categoria, 
            		cd_responsavel, 
            		nr_peso, 
            		ds_consulta,
            		cd_usuario_inclusao,
            		cd_usuario_alteracao
            	  )
             VALUES 
                  (
                	".intval($cd_controle_igp_indicador).",
                	".intval($args['cd_controle_igp']).",
                	".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                	".(trim($args['cd_indicador']) != '' ? intval($args['cd_indicador']) : "DEFAULT").",
                	".(trim($args['cd_controle_igp_categoria']) != '' ? intval($args['cd_controle_igp_categoria']) : "DEFAULT").",
                	".(trim($args['cd_responsavel']) != '' ? str_escape($args['cd_responsavel']) : "DEFAULT").",
                	".(trim($args['nr_peso']) != '' ? floatval($args['nr_peso']) : "DEFAULT").",
                	".(trim($args['ds_consulta']) != '' ? str_escape($args['ds_consulta']) : "DEFAULT").",
                	".intval($args['cd_usuario']).",
                	".intval($args['cd_usuario'])."
                  );";

		$this->db->query($qr_sql);

		return $cd_controle_igp_indicador;
	}

	public function atualizar_controle_indicador($cd_controle_igp_indicador, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.controle_igp_indicador
   			   SET nr_ordem                  = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
   			   	   cd_indicador              = ".(trim($args['cd_indicador']) != '' ? intval($args['cd_indicador']) : "DEFAULT").",
                   cd_controle_igp_categoria = ".(trim($args['cd_controle_igp_categoria']) != '' ? intval($args['cd_controle_igp_categoria']) : "DEFAULT").",
                   cd_responsavel            = ".(trim($args['cd_responsavel']) != '' ? str_escape($args['cd_responsavel']) : "DEFAULT").",
                   nr_peso                   = ".(trim($args['nr_peso']) != '' ? floatval($args['nr_peso']) : "DEFAULT").",
                   cd_usuario_alteracao      = ".intval($args['cd_usuario']).",
                   dt_alteracao              = CURRENT_TIMESTAMP
             WHERE cd_controle_igp_indicador = ".intval($cd_controle_igp_indicador).";";

		$this->db->query($qr_sql);
	}

	public function get_anos()
	{
		$qr_sql = "
			SELECT ci.cd_controle_igp,
			       ci.nr_ano
			  FROM gestao.controle_igp ci
			 WHERE ci.dt_exclusao IS NULL
			   AND (SELECT COUNT (*)
			          FROM gestao.controle_igp_indicador_mes ciim
			         WHERE ciim.dt_exclusao IS NULL
				       AND ciim.cd_controle_igp = ci.cd_controle_igp) > 0
			 ORDER BY ci.nr_ano DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function referencia_ano_fechado($cd_controle_igp)
	{
		$qr_sql = "
			SELECT cd_controle_igp_indicador_mes AS
			  FROM gestao.controle_igp_indicador_mes 
			 WHERE dt_exclusao IS NULL
			   AND cd_controle_igp = ".intval($cd_controle_igp)."
			 ORDER BY dt_referencia DESC
			 LIMIT 1";

		return $this->db->query($qr_sql)->row_array();
	}

	public function referencia_mes_fechado($cd_controle_igp)
	{
		$qr_sql = "
			SELECT cd_controle_igp_indicador_mes,
			       TO_CHAR(dt_referencia, 'YYYY/MM') AS ds_referenfcia
			  FROM gestao.controle_igp_indicador_mes 
			 WHERE dt_exclusao IS NULL
			   AND cd_controle_igp = ".intval($cd_controle_igp)."
			 ORDER BY dt_referencia DESC
			 LIMIT 1";

		return $this->db->query($qr_sql)->row_array();
	}

	public function referencia_indicador($cd_controle_igp)
	{
		$qr_sql = "
			SELECT cd_controle_igp
  			  FROM gestao.controle_igp
             WHERE dt_exclusao IS NULL
               AND cd_controle_igp != ".intval($cd_controle_igp)."
             ORDER BY nr_ano DESC 
             LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
 	}

 	public function referencia_ano()
 	{
 		$qr_sql = "
 			SELECT (nr_ano + 1) AS nr_ano
 			  FROM gestao.controle_igp
 			 ORDER BY nr_ano DESC 
 			 LIMIT 1;";

 		return $this->db->query($qr_sql)->row_array();
 	}

	public function listar_indicadores($cd_controle_igp)
	{
		$qr_sql = "
			SELECT cii.cd_controle_igp_indicador,
				   cii.cd_controle_igp,
			       cii.nr_peso,
			       cii.nr_ordem,
			       cii.cd_controle_igp_categoria,
			       cii.cd_indicador,
			       i.tp_analise,
			       cia.ds_controle_igp_categoria AS ds_categoria,
			       cii.cd_responsavel,
			       (CASE WHEN i.tp_analise = '-' THEN 'Menor'
			  	         WHEN i.tp_analise = '+' THEN 'Maior'
			  	         ELSE '' 
			  	   END) AS ds_analise,
			  	   ium.ds_indicador_unidade_medida, 
			       i.ds_indicador AS indicador,
			       (CASE WHEN cii.ds_consulta IS NULL THEN 'Não'
			  	         WHEN cii.ds_consulta IS NOT NULL THEN 'Sim'
			  	         ELSE '' 
			  	   END) AS ds_status_consulta,
			  	   (CASE WHEN cii.ds_consulta IS NULL THEN 'important'
			  	         WHEN cii.ds_consulta IS NOT NULL THEN 'success'
			  	         ELSE '' 
			  	   END) AS class_status_consulta,
			  	   cii.ds_consulta
			  FROM gestao.controle_igp_indicador cii
			  JOIN gestao.controle_igp_categoria cia
			    ON cia.cd_controle_igp_categoria = cii.cd_controle_igp_categoria
			  JOIN indicador.indicador i
			    ON i.cd_indicador = cii.cd_indicador
			  JOIN indicador.indicador_unidade_medida ium
			    ON ium.cd_indicador_unidade_medida = i.cd_indicador_unidade_medida

			 WHERE cii.dt_exclusao IS NULL
			   AND cii.cd_controle_igp = ".intval($cd_controle_igp)."
			   ORDER BY cii.nr_ordem ASC ;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_controle_indicador($cd_controle_igp_indicador)
	{
		$qr_sql = "
			SELECT cd_controle_igp,
				   cd_controle_igp_indicador,
			       nr_ordem,
			       nr_peso,
			       cd_responsavel,
			       cd_controle_igp_categoria,
			       cd_indicador 
			  FROM gestao.controle_igp_indicador 
			 WHERE cd_controle_igp_indicador = ".intval($cd_controle_igp_indicador).";";		

		return $this->db->query($qr_sql)->row_array();
	}

	public function set_ordem($cd_controle_igp_indicador, $nr_ordem, $cd_usuario)
    {
      $qr_sql = "
        UPDATE gestao.controle_igp_indicador
           SET nr_ordem             = ".(trim($nr_ordem) != '' ? intval($nr_ordem) : 'DEFAULT').",
               cd_usuario_alteracao = ".intval($cd_usuario).", 
               dt_alteracao         = CURRENT_TIMESTAMP
         WHERE cd_controle_igp_indicador = ".intval($cd_controle_igp_indicador).";";

      $this->db->query($qr_sql);
    }

    public function fechar($cd_controle_igp, $cd_usuario)
    {
      $qr_sql = "
          UPDATE gestao.controle_igp
             SET dt_fechamento         = CURRENT_TIMESTAMP, 
                 cd_usuario_fechamento = ".intval($cd_usuario)." 
           WHERE cd_controle_igp = ".intval($cd_controle_igp).";";

       $this->db->query($qr_sql);
    }

    public function excluir_indicador($cd_controle_igp_indicador, $cd_usuario)
    {
      $qr_sql = "
        UPDATE gestao.controle_igp_indicador
           SET cd_usuario_exclusao  = ".intval($cd_usuario).",
               dt_exclusao          = CURRENT_TIMESTAMP 
         WHERE cd_controle_igp_indicador = ".intval($cd_controle_igp_indicador).";";

      $this->db->query($qr_sql);
    }

    public function listar_resultado($cd_controle_igp, $args = array())
    {
    	$qr_sql = "
    		SELECT cii.nr_ordem,
			       cia.ds_controle_igp_categoria,
			       i.ds_indicador AS indicador,
			       cii.cd_responsavel,
			       i.tp_analise,
			       (CASE WHEN i.tp_analise = '-' THEN 'Menor'
			      		 WHEN i.tp_analise = '+' THEN 'Maior'
			             ELSE '' 
			       END) AS ds_analise,
			       cii.nr_peso,
			       cii.cd_controle_igp,
			       cii.cd_controle_igp_indicador,
			       ium.ds_indicador_unidade_medida,
			       ic.ds_indicador_controle,			  	    
			       (SELECT nr_resultado 
			       	  FROM gestao.get_sql_execute_controle_igp(REPLACE(cii.ds_consulta,'[P_DT_REFERENCIA]', '".trim($args['nr_ano'])."'||'-'||'".trim($args['nr_mes'])."'||'-01'::TEXT))) AS nr_resultado_indicador,
			       (SELECT nr_meta 
			          FROM gestao.get_sql_execute_controle_igp(REPLACE(cii.ds_consulta,'[P_DT_REFERENCIA]', '".trim($args['nr_ano'])."'||'-'||'".trim($args['nr_mes'])."'||'-01'::TEXT))) AS nr_meta_indicador,
			       (SELECT ds_referencia 
			          FROM gestao.get_sql_execute_controle_igp(REPLACE(cii.ds_consulta,'[P_DT_REFERENCIA]', '".trim($args['nr_ano'])."'||'-'||'".trim($args['nr_mes'])."'||'-01'::TEXT))) AS ds_referencia_indicador
			  FROM gestao.controle_igp_indicador cii
			  JOIN gestao.controle_igp_categoria cia
			    ON cia.cd_controle_igp_categoria = cii.cd_controle_igp_categoria
			  JOIN indicador.indicador i
			    ON i.cd_indicador = cii.cd_indicador
			  JOIN indicador.indicador_unidade_medida ium  
			    ON ium.cd_indicador_unidade_medida = i.cd_indicador_unidade_medida
			  JOIN indicador.indicador_controle ic
			    ON i.cd_indicador_controle = ic.cd_indicador_controle
			 WHERE cii.dt_exclusao IS NULL
			   AND cii.cd_controle_igp = ".intval($cd_controle_igp)."
			 ORDER BY cii.nr_ordem ASC ;"; 

		return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_fechar_mes($args = array())
    {
    	$qr_sql = "
    		INSERT INTO gestao.controle_igp_indicador_resultado
    		(
    			cd_controle_igp_indicador,
    			cd_controle_igp_indicador_mes,
    			ds_referencia_indicador, 
                nr_resultado_indicador,
                nr_meta_indicador,
                nr_calculo,
                nr_resultado,
                nr_resultado_ponderado,
                cd_usuario_inclusao,
                cd_usuario_alteracao 
    		)
    		VALUES
    		(
    			".intval($args['cd_controle_igp_indicador']).",
            	".intval($args['cd_controle_igp_indicador_mes']).",
            	".(trim($args['ds_referencia']) != '' ? str_escape($args['ds_referencia']) : "DEFAULT").",
            	".(trim($args['nr_resultado_indicador']) != '' ? floatval($args['nr_resultado_indicador']) : "DEFAULT").",
            	".(trim($args['nr_meta_indicador']) != '' ? floatval($args['nr_meta_indicador']) : "DEFAULT").",
            	".(trim($args['nr_calculo']) != '' ? floatval($args['nr_calculo']) : "DEFAULT").",
            	".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
            	".(trim($args['nr_resultado_ponderado']) != '' ? floatval($args['nr_resultado_ponderado']) : "DEFAULT").",
            	".intval($args['cd_usuario']).",
            	".intval($args['cd_usuario'])."
    		);";

    	$this->db->query($qr_sql); 
	}

	public function referencia($cd_controle_igp, $mes)
	{
		$qr_sql = "
			SELECT ciim.cd_controle_igp_indicador_mes,
				   TO_CHAR(ciim.dt_referencia, 'MM') AS nr_mes,
				   TO_CHAR(ciim.dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao
			  FROM gestao.controle_igp ci
			  JOIN gestao.controle_igp_indicador_mes ciim
			  	ON ci.cd_controle_igp = ciim.cd_controle_igp
			 WHERE ciim.dt_exclusao IS NULL 
			   AND ciim.cd_controle_igp = ".intval($cd_controle_igp)."
			   AND TO_CHAR(ciim.dt_referencia, 'MM') =  '".trim($mes)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function fechar_mes($args = array())
    {
    	$cd_controle_igp_indicador_mes = intval($this->db->get_new_id('gestao.controle_igp_indicador_mes', 'cd_controle_igp_indicador_mes'));

    	$qr_sql = "
    		INSERT INTO gestao.controle_igp_indicador_mes
    		(
    			cd_controle_igp_indicador_mes,
    			cd_controle_igp,
    			dt_referencia, 
                cd_usuario_inclusao,
                cd_usuario_alteracao
    		)
    		VALUES
    		(
    			".intval($cd_controle_igp_indicador_mes).",
    			".intval($args['cd_controle_igp']).",
            	".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
            	".intval($args['cd_usuario']).",
            	".intval($args['cd_usuario'])."
    		);";

    	$this->db->query($qr_sql); 

    	return $cd_controle_igp_indicador_mes;
	}

	public function resultado($cd_controle_igp, $cd_controle_igp_indicador_mes)
	{
		$qr_sql = "
    		SELECT  cii.nr_ordem,
    		        ci.nr_ano,
		            cia.ds_controle_igp_categoria,
		            i.ds_indicador AS indicador,
		            cii.cd_responsavel,
		            i.tp_analise,
		            (CASE WHEN i.tp_analise = '-' THEN 'Menor'
		                  WHEN i.tp_analise = '+' THEN 'Maior'
		                  ELSE '' 
		            END) AS ds_analise,
		            cii.nr_peso,
		            cii.cd_controle_igp,
		            cii.cd_controle_igp_indicador,
		            ciir.ds_referencia_indicador,
		            ciir.nr_resultado_indicador,
		            ciir.nr_meta_indicador,
		            ciir.nr_calculo,
		            ium.ds_indicador_unidade_medida,
		            ciir.nr_resultado AS nr_resultado_igp,
			  	    ciir.nr_resultado_ponderado,
			  	    ic.ds_indicador_controle,
			  	    i.ds_indicador AS indicador
		       FROM gestao.controle_igp_indicador_resultado ciir
		       JOIN gestao.controle_igp_indicador cii
			     ON ciir.cd_controle_igp_indicador = cii.cd_controle_igp_indicador
			   JOIN gestao.controle_igp ci
			     ON ci.cd_controle_igp = cii.cd_controle_igp
			   JOIN indicador.indicador i
			     ON i.cd_indicador = cii.cd_indicador
			   JOIN indicador.indicador_unidade_medida ium
			     ON ium.cd_indicador_unidade_medida = i.cd_indicador_unidade_medida
			   JOIN indicador.indicador_controle ic
			     ON i.cd_indicador_controle = ic.cd_indicador_controle
			   JOIN gestao.controle_igp_categoria cia
			     ON cia.cd_controle_igp_categoria = cii.cd_controle_igp_categoria
			  WHERE ciir.dt_exclusao IS NULL
			    AND cii.cd_controle_igp                = ".intval($cd_controle_igp)."
			    AND ciir.cd_controle_igp_indicador_mes = ".trim($cd_controle_igp_indicador_mes)."
			  ORDER BY cii.nr_ordem  ASC;"; 
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function resultado_anual($cd_controle_igp)
	{
		$qr_sql = "
			SELECT ci.nr_ano,
			       SUM(nr_peso) AS nr_peso, 
			       SUM(nr_resultado_ponderado) AS nr_resultado_ponderado
			  FROM gestao.controle_igp_indicador_resultado ciir
			  JOIN gestao.controle_igp_indicador cii
			    ON cii.cd_controle_igp_indicador = ciir.cd_controle_igp_indicador
			  JOIN gestao.controle_igp_indicador_mes ciim
			    ON ciim.cd_controle_igp_indicador_mes = ciir.cd_controle_igp_indicador_mes
			  JOIN gestao.controle_igp ci
			    ON ci.cd_controle_igp = ciim.cd_controle_igp
			 WHERE ciir.dt_exclusao IS NULL
			   AND ciim.dt_exclusao IS NULL
			   AND ci.dt_exclusao IS NULL 
			   AND ci.cd_controle_igp  = ".intval($cd_controle_igp)."
			 GROUP BY ci.nr_ano, ciim.dt_referencia
			 ORDER BY ciim.dt_referencia DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}
}