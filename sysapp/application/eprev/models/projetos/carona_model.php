<?php
class carona_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

    function listaCaronas(&$result, $args=array())
    {

        $qr_sql = "
                  SELECT ca.cd_carona,
                         ca.cd_usuario_inclusao,
                         uc.nome,
                         ca.trajeto_vinda,
                         ca.trajeto_retorno,
                         ca.nr_vaga
                    FROM projetos.carona ca
                    JOIN projetos.usuarios_controledi uc
                      ON ca.cd_usuario_inclusao = uc.codigo
                   WHERE ca.dt_exclusao IS NULL
                     ".($args['vagas'] == 'sim' ? 'AND ca.nr_vaga > 0' : '')."
                     ".($args['vagas'] == 'nao' ? 'AND ca.nr_vaga = 0' : '')."
                     ".($args['usuario'] != '' ? 'AND ca.cd_usuario_inclusao = '.intval($args['usuario'])  : '');
        $result = $this->db->query($qr_sql);
    }

    function listaCaroneiros(&$result, $args=array())
    {
        $qr_sql = "
                SELECT uc.nome,
                       cc.cd_carona_caroneiro,
                       cc.cd_usuario_inclusao,
                       TO_CHAR(COALESCE(cc.dt_inclusao),'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
                  FROM projetos.carona_caroneiro cc
                  JOIN projetos.usuarios_controledi uc
                    ON cc.cd_usuario_inclusao = uc.codigo
                 WHERE cc.dt_exclusao IS NULL
                   AND cc.cd_carona = ".intval($args['cd_carona'])
                  ;

        $result = $this->db->query($qr_sql);
    }

    function verificaCaroneiro(&$result, $args=array())
    {
        $qr_sql = "
                SELECT COUNT(cc.cd_usuario_inclusao) AS TOTAL
                  FROM projetos.carona_caroneiro cc
            INNER JOIN projetos.carona c
                    ON c.cd_carona = cc.cd_carona
                 WHERE cc.dt_exclusao IS NULL
                   AND c.dt_exclusao IS NULL
                   AND cc.cd_usuario_inclusao = ".$args['cd_usuario_inclusao'];

        $result = $this->db->query($qr_sql);
    }

    function carrega(&$result, $args=array())
    {
        $qr_sql = "
                  SELECT cd_carona,
                         trajeto_vinda,
                         trajeto_retorno,
                         nr_vaga
                    FROM projetos.carona
                   WHERE cd_carona =". intval($args['cd_carona']);
        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_carona']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.carona
                   SET trajeto_vinda   = '".trim($args['trajeto_vinda'])."',
                       trajeto_retorno = '".trim($args['trajeto_retorno'])."',
                       nr_vaga         = ".intval($args['nr_vaga'])."
                   WHERE cd_carona = ".intval($args['cd_carona'])."

            ";
        }
        else
        {

           $new_id = intval($this->db->get_new_id("projetos.carona", "cd_carona"));

           $qr_sql = "
                INSERT INTO projetos.carona
                       (
                         cd_carona,
                         trajeto_vinda,
                         trajeto_retorno,
                         nr_vaga,
                         dt_inclusao,
                         cd_usuario_inclusao
                       )
                       VALUES
                       (
                         ".intval($new_id).",
                         '".trim($args['trajeto_vinda'])."',
                         '".trim($args['trajeto_retorno'])."',
                         ".intval($args['nr_vaga']).",
                         CURRENT_TIMESTAMP,
                         ".intval($args['cd_usuario_inclusao'])."
                       )
            ";
        }
        

        $result = $this->db->query($qr_sql);
    }

    function entrar(&$result, $args=array())
    {

        $new_id = intval($this->db->get_new_id("projetos.carona_caroneiro", "cd_carona_caroneiro"));

        $qr_sql = "
                INSERT INTO projetos.carona_caroneiro
                       (
                         cd_carona_caroneiro,
                         cd_carona,
                         dt_inclusao,
                         cd_usuario_inclusao
                       )
                       VALUES
                       (
                       ".intval($new_id).",
                       ".intval($args['cd_carona']).",
                       CURRENT_TIMESTAMP,
                       ".intval($args['cd_usuario_inclusao'])."
                       )";

        $result = $this->db->query($qr_sql);
    }

    function sair(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.carona_caroneiro
                      SET dt_exclusao         = CURRENT_TIMESTAMP,
                          cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
                    WHERE cd_carona_caroneiro =".intval($args['cd_carona_caroneiro']);

      $result = $this->db->query($qr_sql);
    }

    function excluir(&$result, $args=array())
    {
        $qr_sql = "UPDATE projetos.carona
                      SET dt_exclusao         = CURRENT_TIMESTAMP,
                          cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
                    WHERE cd_carona =".intval($args['cd_carona']);

      $result = $this->db->query($qr_sql);
    }
}
?>