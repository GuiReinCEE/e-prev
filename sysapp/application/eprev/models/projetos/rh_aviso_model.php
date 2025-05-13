<?php
class Rh_aviso_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($cd_usuario, $args = array())
    {       
        $qr_sql = "
            SELECT ra.cd_rh_aviso,
                   ra.ds_descricao,
                   ra.cd_periodicidade,
				   ra.qt_dia,
                   (CASE WHEN ra.cd_periodicidade = 'E' 
                         THEN 'Eventual'
                         WHEN ra.cd_periodicidade = 'D' 
                         THEN 'Diária'
                         WHEN ra.cd_periodicidade = 'S' 
                         THEN 'Semanal'
                         WHEN ra.cd_periodicidade = 'M' 
                         THEN 'Mensal'
                         WHEN ra.cd_periodicidade = 'A' 
                         THEN 'Anual'
                         ELSE ''
                   END) AS ds_periodicidade,
                   (CASE WHEN ra.cd_periodicidade = 'E'
                         THEN 'label-warning'
                         WHEN ra.cd_periodicidade = 'D' 
                         THEN 'label-important'
                         WHEN ra.cd_periodicidade = 'S' 
                         THEN 'label-inverse'
                         WHEN ra.cd_periodicidade = 'M' 
                         THEN 'label-info'
                         WHEN ra.cd_periodicidade = 'A' 
                         THEN 'label-success'
                         ELSE ''
                   END) AS ds_class_periodicidade,						   
                   TO_CHAR(ra.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   (CASE WHEN ra.cd_periodicidade = 'S' THEN
						(CASE EXTRACT(DOW FROM ra.dt_referencia) 
							  WHEN 0 
                              THEN 'Domingo'
                              WHEN 1 
                              THEN 'Segunda'
                              WHEN 2 
                              THEN 'Terça'
                              WHEN 3 
                              THEN 'Quarta'
                              WHEN 4 
                              THEN 'Quinta'
                              WHEN 5 
                              THEN 'Sexta'
                              WHEN 6 
                              THEN 'Sábado'
                        END)
						ELSE ''
				   END) AS ds_dia,
                   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(ra.dt_confirmacao,'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
                   funcoes.get_usuario_nome(ra.cd_usuario_inclusao) AS ds_usuario,
                   funcoes.get_usuario_nome(ra.cd_usuario_conferencia) AS ds_usuario_conferencia,
                   (CASE WHEN ra.dt_confirmacao IS NULL AND ".intval($cd_usuario)." = ra.cd_usuario_conferencia
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_confirmar
              FROM projetos.rh_aviso ra
             WHERE ra.dt_exclusao IS NULL
			   ".(trim($args['cd_periodicidade']) != '' ? "AND ra.cd_periodicidade = '".trim($args['cd_periodicidade'])."'" : "").";";
        return $this->db->query($qr_sql)->result_array();
    }   

    public function get_usuario()
    {
        $qr_sql = "
           SELECT codigo AS value,
                  nome AS text
             FROM projetos.usuarios_controledi uc
            WHERE divisao  = 'GC'
              AND indic_09 = '*'
              AND tipo     <> 'X'
              AND codigo   NOT IN (170, 251)
            ORDER BY nome;";
    
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_rh_aviso)
    {       
        $qr_sql = "
            SELECT cd_rh_aviso,
                   ds_descricao,
                   funcoes.get_usuario(cd_usuario_conferencia) || '@eletroceee.com.br' AS ds_email,
                   funcoes.get_usuario(cd_usuario_inclusao) || '@eletroceee.com.br' AS ds_email_inclusao
              FROM projetos.rh_aviso
             WHERE cd_rh_aviso = ".intval($cd_rh_aviso).";";

        return $this->db->query($qr_sql)->row_array();
    } 

    public function salvar($args = array())
    {
		$cd_rh_aviso = intval($this->db->get_new_id('projetos.rh_aviso', 'cd_rh_aviso'));
		
		$qr_sql = "
            INSERT INTO projetos.rh_aviso
                 (
					cd_rh_aviso,
					ds_descricao,
					cd_periodicidade,
					qt_dia,
					dt_referencia,
                    cd_usuario_conferencia,
					cd_usuario_inclusao
                 )
            VALUES
                 (
                    ".intval($cd_rh_aviso).",
					".(trim($args['ds_descricao']) != '' ? "'".trim($args['ds_descricao'])."'" : "DEFAULT").",
                    ".(trim($args['cd_periodicidade']) != '' ? "'".trim($args['cd_periodicidade'])."'" : "DEFAULT").",
					".(trim($args['qt_dia']) != '' ? intval($args['qt_dia']) : "DEFAULT").",
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."','DD/MM/YYYY')" : "DEFAULT").",
                    ".(intval($args['cd_usuario_conferencia']) > 0 ? intval($args['cd_usuario_conferencia']) : "DEFAULT").",
                    ".intval($args['cd_usuario'])."
                 );";
        
		foreach($args['usuario'] as $item)
		{
			$qr_sql.= "
				INSERT INTO projetos.rh_aviso_usuario
					 (
						cd_rh_aviso,
						cd_usuario
					 )
				VALUES
					     (
						".intval($cd_rh_aviso).",
						".intval($item)."
					 );";
						 
		}

        $this->db->query($qr_sql);

        return $cd_rh_aviso;
    }
	
    public function excluir($cd_rh_aviso, $cd_usuario)
    {
		$qr_sql = "
            UPDATE projetos.rh_aviso
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_rh_aviso = ".intval($cd_rh_aviso).";";
        
        $this->db->query($qr_sql);
    }	

    public function confirmar($cd_rh_aviso, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.rh_aviso
               SET cd_usuario_confirmacao = ".intval($cd_usuario).",
                   dt_confirmacao         = CURRENT_TIMESTAMP
             WHERE cd_rh_aviso = ".intval($cd_rh_aviso).";

            SELECT rotinas.rh_aviso_verificacao();";
        
        $this->db->query($qr_sql);
    }
	
    public function verificar($cd_rh_aviso_verificacao)
    {       
        $qr_sql = "
            SELECT ra.cd_rh_aviso,
			       rav.cd_rh_aviso_verificacao,
                   ra.ds_descricao,
                   ra.cd_periodicidade,
                   (CASE WHEN ra.cd_periodicidade = 'E' 
                         THEN 'Eventual'
                         WHEN ra.cd_periodicidade = 'D' 
                         THEN 'Diária'
                         WHEN ra.cd_periodicidade = 'S' 
                         THEN 'Semanal'
                         WHEN ra.cd_periodicidade = 'M' 
                         THEN 'Mensal'
                         WHEN ra.cd_periodicidade = 'A' 
                         THEN 'Anual'
                         ELSE ''
                   END) AS ds_periodicidade,
                   TO_CHAR(rav.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
				   funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS ds_usuario,
				   'Este item foi verificado por ' || funcoes.get_usuario_nome(rav.cd_usuario_verificacao) || ' em ' || TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') || '.' AS ds_verificado
              FROM projetos.rh_aviso ra
			  JOIN projetos.rh_aviso_verificacao rav
				ON ra.cd_rh_aviso = rav.cd_rh_aviso					  
             WHERE ra.dt_exclusao              IS NULL
			   AND rav.cd_rh_aviso_verificacao = ".intval($cd_rh_aviso_verificacao).";";

        return $this->db->query($qr_sql)->row_array();
    } 

    public function listar_verificar($cd_rh_aviso)
    {       
        $qr_sql = "
            SELECT ra.cd_rh_aviso,
                   rav.cd_rh_aviso_verificacao,
                   ra.ds_descricao,
                   ra.cd_periodicidade,
                   (CASE WHEN ra.cd_periodicidade = 'E' 
                         THEN 'Eventual'
                         WHEN ra.cd_periodicidade = 'D' 
                         THEN 'Diária'
                         WHEN ra.cd_periodicidade = 'S' 
                         THEN 'Semanal'
                         WHEN ra.cd_periodicidade = 'M' 
                         THEN 'Mensal'
                         WHEN ra.cd_periodicidade = 'A' 
                         THEN 'Anual'
                         ELSE ''
                    END) AS ds_periodicidade,
                    TO_CHAR(rav.dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                    TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao,
                    funcoes.get_usuario_nome(rav.cd_usuario_verificacao) AS ds_usuario,
                    'Este item foi verificado por ' || funcoes.get_usuario_nome(rav.cd_usuario_verificacao) || ' em ' || TO_CHAR(rav.dt_verificacao,'DD/MM/YYYY HH24:MI:SS') || '.' AS ds_verificado
               FROM projetos.rh_aviso ra
               JOIN projetos.rh_aviso_verificacao rav
                 ON ra.cd_rh_aviso = rav.cd_rh_aviso           
              WHERE ra.dt_exclusao IS NULL
                AND ra.cd_rh_aviso = ".intval($cd_rh_aviso).";";

        return $this->db->query($qr_sql)->result_array();
    } 
	
    public function verificar_salvar($cd_rh_aviso_verificacao, $cd_usuario)
    {
		$qr_sql = "
            UPDATE projetos.rh_aviso_verificacao
			   SET cd_usuario_verificacao = ".intval($cd_usuario).",
                   dt_verificacao         = CURRENT_TIMESTAMP
			 WHERE cd_rh_aviso_verificacao = ".intval($cd_rh_aviso_verificacao).";";
        
        $this->db->query($qr_sql);
    }		
}