<?php
class Indisp_sistemas_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_indisp_sistemas,
                   TO_CHAR(dt_indisp_sistemas, 'MM/YYYY') AS ds_indisp_sistemas,
                   nr_dias,
                   TO_CHAR(dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento
              FROM informatica.indisp_sistemas
             WHERE dt_exclusao IS NULL
             ORDER BY dt_indisp_sistemas DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_indisp_sistemas)
    {
        $qr_sql = "
            SELECT cd_indisp_sistemas,
                   TO_CHAR(dt_indisp_sistemas, 'MM/YYYY') AS ds_indisp_sistemas,
                   TO_CHAR(dt_indisp_sistemas,'DD/MM/YYYY') AS dt_indisp_sistemas,
                   TO_CHAR(dt_indisp_sistemas, 'MM') AS nr_mes,
                   TO_CHAR(dt_indisp_sistemas, 'YYYY') AS nr_ano,
                   nr_dias,
                   TO_CHAR(dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento
              FROM informatica.indisp_sistemas
             WHERE cd_indisp_sistemas = ".intval($cd_indisp_sistemas).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$cd_indisp_sistemas = intval($this->db->get_new_id('informatica.indisp_sistemas', 'cd_indisp_sistemas'));

    	$qr_sql = "
    	    INSERT INTO informatica.indisp_sistemas
				 (
				    cd_indisp_sistemas, 
				    dt_indisp_sistemas, 
				    nr_dias,
				    cd_usuario_inclusao, 
                    cd_usuario_alteracao
				 )
			VALUES
			     (
			    	".intval($cd_indisp_sistemas).",
			    	".(trim($args['dt_indisp_sistemas']) != '' ? "TO_DATE('".trim($args['dt_indisp_sistemas'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
                    (
						SELECT COUNT(*) AS qt_dia
						  FROM (SELECT date_trunc('day', dd)::date AS dia
								  FROM generate_series((TO_DATE('".trim($args['dt_indisp_sistemas'])."', 'DD/MM/YYYY')::timestamp), (((date_trunc('month',((TO_DATE('".trim($args['dt_indisp_sistemas'])."', 'DD/MM/YYYY')::timestamp) + '1 month'))::date) - '1 day'::interval)::timestamp), '1 day'::interval) dd) AS d
						 WHERE EXTRACT(dow FROM d.dia) NOT IN (0, 6)
						   AND funcoes.fnc_feriado(d.dia, 'EMP') = FALSE					
					),
			    	".intval($args['cd_usuario']).",
                	".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);

        return $cd_indisp_sistemas;
	}

    public function atualizar($cd_indisp_sistemas, $args = array())
    {
        $qr_sql = "
            UPDATE informatica.indisp_sistemas
                SET dt_indisp_sistemas   = ".(trim($args['dt_indisp_sistemas']) != '' ? "TO_DATE('".trim($args['dt_indisp_sistemas'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
                    nr_dias              = ".(trim($args['nr_dias']) != '' ? intval($args['nr_dias']) : 'DEFAULT').",
                    cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                    dt_alteracao         = CURRENT_TIMESTAMP                   
                WHERE cd_indisp_sistemas = ".intval($cd_indisp_sistemas).";";
              
       $this->db->query($qr_sql);
    }

    public function listar_ocorrencia($cd_indisp_sistemas, $fl_energia = '')
    {
        $qr_sql = "
            SELECT o.cd_indisp_sistemas_ocorrencia,
                   TO_CHAR(o.dt_indisp_sistemas_ocorrencia, 'DD/MM/YYYY') AS dt_indisp_sistemas_ocorrencia,
                   o.cd_indisp_sistemas, 
                   o.nr_minuto, 
                   o.fl_energia,
                   CASE WHEN o.fl_energia = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END AS ds_energia,
                   o.cd_indisp_sistemas_tipo, 
                   t.ds_indisp_sistemas_tipo,
                   o.ds_indisp_sistemas_ocorrencia
              FROM informatica.indisp_sistemas_ocorrencia o
              JOIN informatica.indisp_sistemas_tipo t
                ON t.cd_indisp_sistemas_tipo = o.cd_indisp_sistemas_tipo
             WHERE o.dt_exclusao IS NULL
               AND o.cd_indisp_sistemas = ".intval($cd_indisp_sistemas)."
               ".(trim($fl_energia) == 'S' ? "AND o.fl_energia = 'S'" : "")."
               ".(trim($fl_energia) == 'N' ? "AND o.fl_energia = 'N'" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_ocorrencia($cd_indisp_sistemas_ocorrencia)
    {
        $qr_sql = "
            SELECT o.cd_indisp_sistemas_ocorrencia,
                   TO_CHAR(o.dt_indisp_sistemas_ocorrencia, 'DD/MM/YYYY') AS dt_indisp_sistemas_ocorrencia,
                   o.cd_indisp_sistemas, 
                   o.nr_minuto, 
                   o.fl_energia,
                   o.cd_indisp_sistemas_tipo, 
                   o.ds_indisp_sistemas_ocorrencia
              FROM informatica.indisp_sistemas_ocorrencia o
             WHERE o.dt_exclusao IS NULL
               AND o.cd_indisp_sistemas_ocorrencia = ".intval($cd_indisp_sistemas_ocorrencia).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_tipo()
    {
        $qr_sql = "
            SELECT cd_indisp_sistemas_tipo AS value,
                   CASE WHEN ds_observacao IS NULL THEN ds_indisp_sistemas_tipo
                        ELSE ds_indisp_sistemas_tipo || ' (' || ds_observacao || ')'
                   END AS text,
                   cd_indisp_sistemas_tipo,
                   ds_indisp_sistemas_tipo,
                   nr_peso
              FROM informatica.indisp_sistemas_tipo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_indisp_sistemas_tipo ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_ocorrencia($args = array())
    {
        $cd_indisp_sistemas_ocorrencia = intval($this->db->get_new_id('informatica.indisp_sistemas_ocorrencia', 'cd_indisp_sistemas_ocorrencia'));

        $qr_sql = "
            INSERT INTO informatica.indisp_sistemas_ocorrencia
                 (
                    cd_indisp_sistemas_ocorrencia, 
                    dt_indisp_sistemas_ocorrencia, 
                    cd_indisp_sistemas, 
                    nr_minuto, 
                    fl_energia, 
                    cd_indisp_sistemas_tipo, 
                    ds_indisp_sistemas_ocorrencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_indisp_sistemas_ocorrencia).",
                    ".(trim($args['dt_indisp_sistemas_ocorrencia']) != '' ? "TO_DATE('".trim($args['dt_indisp_sistemas_ocorrencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
                    ".(trim($args['cd_indisp_sistemas']) != '' ? intval($args['cd_indisp_sistemas']) : 'DEFAULT').",
                    ".(trim($args['nr_minuto']) != '' ? intval($args['nr_minuto']) : 'DEFAULT').",
                    ".(trim($args['fl_energia']) != '' ? "'".trim($args['fl_energia'])."'" : 'DEFAULT').",
                    ".(trim($args['cd_indisp_sistemas_tipo']) != '' ? intval($args['cd_indisp_sistemas_tipo']) : 'DEFAULT').",
                    ".(trim($args['ds_indisp_sistemas_ocorrencia']) != '' ? str_escape($args['ds_indisp_sistemas_ocorrencia']) : 'DEFAULT').",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_indisp_sistemas_ocorrencia;
    }

    public function atualizar_ocorrencia($cd_indisp_sistemas_ocorrencia, $args = array())
    {
        $qr_sql = "
            UPDATE informatica.indisp_sistemas_ocorrencia
                SET dt_indisp_sistemas_ocorrencia = ".(trim($args['dt_indisp_sistemas_ocorrencia']) != '' ? "TO_DATE('".trim($args['dt_indisp_sistemas_ocorrencia'])."', 'DD/MM/YYYY')" : 'DEFAULT').",
                    nr_minuto                     = ".(trim($args['nr_minuto']) != '' ? intval($args['nr_minuto']) : 'DEFAULT').",
                    fl_energia                    = ".(trim($args['fl_energia']) != '' ? "'".trim($args['fl_energia'])."'" : 'DEFAULT').",
                    cd_indisp_sistemas_tipo       = ".(trim($args['cd_indisp_sistemas_tipo']) != '' ? intval($args['cd_indisp_sistemas_tipo']) : 'DEFAULT').",
                    ds_indisp_sistemas_ocorrencia = ".(trim($args['ds_indisp_sistemas_ocorrencia']) != '' ? str_escape($args['ds_indisp_sistemas_ocorrencia']) : 'DEFAULT').",
                    cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
                    dt_alteracao                  = CURRENT_TIMESTAMP                   
                WHERE cd_indisp_sistemas_ocorrencia = ".intval($cd_indisp_sistemas_ocorrencia).";";
              
       $this->db->query($qr_sql);
    }

    public function get_tipo_mes($cd_indisp_sistemas, $fl_energia = '')
    {
        $qr_sql = "
            SELECT t.cd_indisp_sistemas_tipo,
                   t.ds_indisp_sistemas_tipo,
                   t.nr_peso,
                   COALESCE(SUM(o.nr_minuto), 0) AS tl_minuto
              FROM informatica.indisp_sistemas_tipo t
              LEFT JOIN informatica.indisp_sistemas_ocorrencia o
                ON o.cd_indisp_sistemas_tipo = t.cd_indisp_sistemas_tipo
               AND o.dt_exclusao IS NULL
               AND o.cd_indisp_sistemas      = ".intval($cd_indisp_sistemas)."
               ".(trim($fl_energia) == 'S' ? "AND o.fl_energia = 'S'" : "")."
               ".(trim($fl_energia) == 'N' ? "AND o.fl_energia = 'N'" : "")."
             WHERE 1 = 1
             GROUP BY t.cd_indisp_sistemas_tipo,
                   t.ds_indisp_sistemas_tipo,
                   t.nr_peso
             ORDER BY t.ds_indisp_sistemas_tipo ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir_ocorrencia($cd_indisp_sistemas_ocorrencia, $cd_usuario)
    {
        $qr_sql = "
            UPDATE informatica.indisp_sistemas_ocorrencia
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP                   
             WHERE cd_indisp_sistemas_ocorrencia = ".intval($cd_indisp_sistemas_ocorrencia).";";
              
       $this->db->query($qr_sql);
    }
}
?>