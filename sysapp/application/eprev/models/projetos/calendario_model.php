<?php
class Calendario_model extends Model 
{
    function __construct()
    {
        parent::Model();
    }

    function datas(&$result, $args=array())
    {
        $tp_calendario = '';

        switch (trim($args['tipo'])) 
        {
            case 'F':
                $tp_calendario = "'C','F','T'";
                break;
            case 'P':
                $tp_calendario = "'P'";
                break;
            case 'E':
                $tp_calendario = "'E','EN'";
                break;
            case 'R':
                $tp_calendario = "'DE', 'CF', 'CD'";
                break;
        }

        $qr_sql = "
            SELECT cd_calendario,
                   TO_CHAR(dt_calendario,'DD/MM/YYYY') AS dt_feriado,
                   tp_calendario,
                   descricao || (CASE WHEN turno = 'M' THEN ' (MANHÃ)'
                                      WHEN turno = 'T' THEN ' (TARDE)'
                                      WHEN turno = 'N' THEN ' (NOITE)'
                                      ELSE COALESCE(turno,'')
                                 END) AS descricao,
                   ds_url
              FROM projetos.calendario
             WHERE dt_calendario BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY') 
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
			   AND tp_calendario IN (".$tp_calendario.")
               AND dt_exclusao IS NULL
             ORDER BY dt_calendario";
             
        $result = $this->db->query($qr_sql);
		
		/*
E => Evento, 
C => Feriado FCEEE
F => Feriado
T => Turno		
		*/
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_calendario,
                   TO_CHAR(dt_calendario,'DD/MM/YYYY') AS dt_calendario,
                   descricao,
                   (CASE WHEN tp_calendario = 'E'  THEN 'Evento'
                         WHEN tp_calendario = 'C'  THEN 'Feriado FCEEE'
                         WHEN tp_calendario = 'F'  THEN 'Feriado'
                         WHEN tp_calendario = 'T'  THEN 'Feriado  FCEEE  Meio Turno'
                         WHEN tp_calendario = 'P'  THEN 'Pagamento Colaboradores'
                         WHEN tp_calendario = 'DE' THEN 'Reunião Diretoria Executiva'
                         WHEN tp_calendario = 'CF' THEN 'Reunião Conselho Fiscal'
                         WHEN tp_calendario = 'CD' THEN 'Reunião Conselho Deliberativo'
                         WHEN tp_calendario = 'EN' THEN 'Evento Endomarketing'
                    END) AS tp_calendario,
                   (CASE WHEN turno = 'M' THEN '(MANHÃ)'
                         WHEN turno = 'T' THEN '(TARDE)'
                         WHEN turno = 'N' THEN '(NOITE)'
                         ELSE COALESCE(turno,'')
                    END) AS turno
              FROM projetos.calendario
             WHERE dt_calendario BETWEEN TO_DATE('01/01/".intval($args['ano'])."','DD/MM/YYYY') 
               AND TO_DATE('31/12/".intval($args['ano'])."','DD/MM/YYYY')
               ".(trim($args['tp_calendario']) != '' ? "AND tp_calendario = '".trim($args['tp_calendario'])."'" : '')."    
               AND dt_exclusao IS NULL
             ORDER BY dt_calendario";
        
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_calendario,
                   TO_CHAR(dt_calendario,'DD/MM/YYYY') AS dt_calendario,
                   descricao,
                   tp_calendario,
                   turno,
                   ds_url
              FROM projetos.calendario
             WHERE cd_calendario = ".intval($args['cd_calendario']);
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_calendario']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.calendario
                   SET dt_calendario = TO_DATE('".$args['dt_calendario']."', 'DD/MM/YYYY'),
                       descricao     = ".(trim($args['descricao']) != "" ? "'".trim($args['descricao'])."'" :'DEFAULT').",
                       tp_calendario = ".(trim($args['tp_calendario']) != "" ? "'".trim($args['tp_calendario'])."'" :'DEFAULT').",
                       turno         = ".(trim($args['turno']) != "" ? "'".trim($args['turno'])."'" :'DEFAULT').",
                       ds_url        = ".(trim($args['ds_url']) != "" ? "'".trim($args['ds_url'])."'" :'DEFAULT')."
                 WHERE cd_calendario = ". intval($args['cd_calendario']);
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.calendario
                     (
                       dt_calendario,
                       descricao,
                       tp_calendario,
                       turno,
                       ds_url,
                       cd_usuario_inclusao    
                     )
                VALUES
                     (
                        TO_DATE('".$args['dt_calendario']."', 'DD/MM/YYYY'),
                        ".(trim($args['descricao']) != "" ? "'".trim($args['descricao'])."'" :'DEFAULT').",
                        ".(trim($args['tp_calendario']) != "" ? "'".trim($args['tp_calendario'])."'" :'DEFAULT').",
                        ".(trim($args['turno']) != "" ? "'".trim($args['turno'])."'" :'DEFAULT').",
                        ".(trim($args['ds_url']) != "" ? "'".trim($args['ds_url'])."'" :'DEFAULT').",
                        ".intval($args['cd_usuario'])."
                     )";
        }
        
        $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE projetos.calendario
                   SET dt_exclusao = CURRENT_TIMESTAMP,
                       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
                 WHERE cd_calendario = ". intval($args['cd_calendario']);
        
        $this->db->query($qr_sql);
    }
}
?>