<?php
class tabelas_atualizar_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT tabela,
                   categoria_importa,			
	           TO_CHAR(dt_ult_atualizacao, 'DD/MM/YYYY HH24:MI:SS.MS') AS dt_atualizacao, 
		   TO_CHAR(dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
		   TO_CHAR(dt_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_final,
		   TO_CHAR(dt_final - dt_inicio,'HH24:MI:SS') AS hr_tempo,
		   CASE WHEN (dt_final - dt_inicio) > '00:00:00'::interval OR (dt_final - dt_inicio) IS NULL THEN ''
		        ELSE 'ERRO '
		   END AS fl_erro,
                   CASE WHEN tipo_bd = 'O' THEN 'ORACLE'
                        WHEN tipo_bd = 'R' THEN 'RT - EMAILS'
                        WHEN tipo_bd = 'U' THEN 'URA - TOI'
                        WHEN tipo_bd = 'T' THEN 'URA - TELEDATA'
                        WHEN tipo_bd = 'X' THEN 'URA - XCALLY'
                        ELSE 'NÃO ESPEC'
                   END AS bd_origem,							
                   num_registros, 
                   num_registros_atualizados,
                   CASE WHEN periodicidade = 'M' THEN 'Mensal'
                        WHEN periodicidade = 'D' THEN 'Diária'
                        WHEN periodicidade = 'E' THEN 'Eventual'
                        WHEN periodicidade = 'I' THEN 'Inativa'
                        WHEN periodicidade = 'S' THEN 'Sincronizada'
                        ELSE periodicidade
                    END AS periodicidade, 
                    truncar, 
                    CASE WHEN TRIM(condicao) = '' OR condicao IS NULL  THEN 'N'
                         ELSE 'S'
                    END AS condicao,
                    qt_total_registro
               FROM projetos.tabelas_atualizar 
              WHERE 1=1
                ".(trim($args['periodicidade']) != '' ? "AND periodicidade = '".trim($args['periodicidade'])."'" : '')."
                ".(trim($args['tipo_bd']) != '' ? "AND tipo_bd = '".trim($args['tipo_bd'])."'" : '')."
				
				".(((trim($args['dt_inicio_ini']) != "") and (trim($args['dt_inicio_fim']) != "")) ? " AND dt_inicio::DATE BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."
				
	      ORDER BY dt_inicio DESC";
		  
		  #echo "<PRE>".$qr_sql."</PRE>";
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio,
                   TO_CHAR(dt_final, 'DD/MM/YYYY HH24:MI:SS') AS dt_final,
                   TO_CHAR(dt_final - dt_inicio,'HH24:MI:SS') AS hr_tempo,
                   tabela, 
                   comando_inicial, 
                   comando_final,
                   comando, 
                   contagem, 
                   periodicidade, 
                   postgres, 
                   oracle, 
                   truncar,
                   access_callcenter, 
                   campo_controle_incremental, 
                   incrementar, 
                   condicao 
              FROM projetos.tabelas_atualizar 
	     WHERE UPPER(tabela) = UPPER('".trim($args['tabela'])."') ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(trim($args['codigo']) == '')
        {
            $qr_sql = "
                INSERT INTO projetos.tabelas_atualizar 
                     (
                        tabela,
                        comando,
			contagem,
			periodicidade,
			postgres,
			oracle,
			access_callcenter,
			comando_inicial,
			comando_final,
			incrementar,
			campo_controle_incremental,
			truncar,
			condicao
                     )
                VALUES
                     (
                        ".(trim($args['tabela']) != '' ? "'".trim($args['tabela'])."'" : "DEFAULT").",
                        ".(trim($args['comando']) != '' ? "'".pg_escape_string(trim($args['comando']))."'" : "DEFAULT").",
                        ".(trim($args['contagem']) != '' ? "'".pg_escape_string(trim($args['contagem']))."'" : "DEFAULT").",
                        ".(trim($args['periodicidade']) != '' ? "'".trim($args['periodicidade'])."'" : "DEFAULT").",
                        ".(trim($args['postgres']) != '' ? "'".trim($args['postgres'])."'" : "DEFAULT").",
                        ".(trim($args['oracle']) != '' ? "'".trim($args['oracle'])."'" : "DEFAULT").",
                        ".(trim($args['access_callcenter']) != '' ? "'".trim($args['access_callcenter'])."'" : "DEFAULT").",
                        ".(trim($args['comando_inicial']) != '' ? "'".pg_escape_string(trim($args['comando_inicial']))."'" : "DEFAULT").",
                        ".(trim($args['comando_final']) != '' ? "'".pg_escape_string(trim($args['comando_final']))."'" : "DEFAULT").", 
                        ".(trim($args['incrementar']) != '' ? "'".trim($args['incrementar'])."'" : "DEFAULT").",
                        ".(trim($args['campo_controle_incremental']) != '' ? "'".trim($args['campo_controle_incremental'])."'" : "DEFAULT").", 
                        ".(trim($args['truncar']) != '' ? "'".trim($args['truncar'])."'" : "DEFAULT").",
                        ".(trim($args['condicao']) != '' ? "'".pg_escape_string(trim($args['condicao']))."'" : "DEFAULT")."	
                     )";
        }
        else
        {
            $qr_sql = "
                 UPDATE	projetos.tabelas_atualizar 
		    SET comando                    = ".(trim($args['comando']) != '' ? "'".pg_escape_string(trim($args['comando']))."'" : "DEFAULT").",
			contagem                   = ".(trim($args['contagem']) != '' ? "'".pg_escape_string(trim($args['contagem']))."'" : "DEFAULT").",
			periodicidade              = ".(trim($args['periodicidade']) != '' ? "'".trim($args['periodicidade'])."'" : "DEFAULT").",
			postgres                   = ".(trim($args['postgres']) != '' ? "'".trim($args['postgres'])."'" : "DEFAULT").",
			oracle                     = ".(trim($args['oracle']) != '' ? "'".trim($args['oracle'])."'" : "DEFAULT").",
			access_callcenter          = ".(trim($args['access_callcenter']) != '' ? "'".trim($args['access_callcenter'])."'" : "DEFAULT").",
			comando_inicial            = ".(trim($args['comando_inicial']) != '' ? "'".pg_escape_string(trim($args['comando_inicial']))."'" : "DEFAULT").", 
			comando_final              = ".(trim($args['comando_final']) != '' ? "'".pg_escape_string(trim($args['comando_final']))."'" : "DEFAULT").", 
			incrementar                = ".(trim($args['incrementar']) != '' ? "'".trim($args['incrementar'])."'" : "DEFAULT").",
			campo_controle_incremental = ".(trim($args['campo_controle_incremental']) != '' ? "'".trim($args['campo_controle_incremental'])."'" : "DEFAULT").", 
			truncar                    = ".(trim($args['truncar']) != '' ? "'".trim($args['truncar'])."'" : "DEFAULT").",
			condicao                   = ".(trim($args['condicao']) != '' ? "'".pg_escape_string(trim($args['condicao']))."'" : "DEFAULT")."							
		  WHERE tabela = '".trim($args['codigo'])."'";
        }
        
        $result = $this->db->query($qr_sql);
    }
}
?>