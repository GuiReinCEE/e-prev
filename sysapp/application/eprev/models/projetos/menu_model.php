<?php
class menu_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function lista_menu(&$result, $args=array())
    {
        $qr_sql = "
            SELECT m1.cd_menu,
                   m1.ds_menu,
                   m1.dt_desativado,
                   (SELECT COUNT(*) 
                      FROM projetos.menu m2 
                     WHERE m2.cd_menu_pai = m1.cd_menu) AS sub_menu
              FROM projetos.menu m1 
             WHERE 1 = 1
             ".(trim($args['cd_menu']) != '' ? "AND m1.cd_menu  = ".intval($args['cd_menu']) : '')."
             ".(trim($args['cd_menu_pai']) != '' ? "AND m1.cd_menu_pai  = ".intval($args['cd_menu_pai']) : '')."
             ".(trim($args['fl_desativado']) == 'N' ? 'AND m1.dt_desativado IS NULL' : '')."
             ORDER BY m1.nr_ordem";
      #  echo '<pre>'.$qr_sql;
        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT m1.cd_menu,
                   m1.cd_menu_pai,
                   m1.ds_href,
                   m1.ds_resumo,
                   m1.ds_menu,
                   m1.dt_desativado,
                   m1.nr_ordem + 1 AS nr_ordem,
                   (SELECT COUNT(*) 
                      FROM projetos.menu m2 
                     WHERE m2.cd_menu_pai = m1.cd_menu) AS sub_menu
              FROM projetos.menu m1 
             WHERE m1.cd_menu  = ".intval($args['cd_menu']).";";
      #  echo '<pre>'.$qr_sql;
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['save']) == 0)
        {
            $qr_sql = "
                INSERT INTO projetos.menu 
                     (
                       cd_menu_pai,
                       ds_menu,
                       ds_href,
                       nr_ordem,
                       ds_resumo
                     ) 
                VALUES 
                     (
                       ".intval($args['cd_menu_pai']).",
                       ".(trim($args['ds_menu']) != '' ? "'". trim($args['ds_menu'])."'" : 'DEFAULT').",
                       ".(trim($args['ds_href']) != '' ? "'". trim($args['ds_href'])."'" : 'DEFAULT').",
					   COALESCE((SELECT MAX(nr_ordem)+1 FROM projetos.menu WHERE cd_menu_pai = ".intval($args['cd_menu_pai'])."),1),
                       ".(trim($args['ds_resumo']) != '' ? "'". trim($args['ds_resumo'])."'" : 'DEFAULT')."
                     );";
        }
        else
        {
            $qr_sql = "
                UPDATE projetos.menu 
                   SET cd_menu_pai = ".intval($args['cd_menu_pai']).",
                       ds_menu     = ".(trim($args['ds_menu']) != '' ? "'". trim($args['ds_menu'])."'" : 'DEFAULT').",
                       ds_href     = ".(trim($args['ds_href']) != '' ? "'". trim($args['ds_href'])."'" : 'DEFAULT').",
                       ds_resumo   = ".(trim($args['ds_resumo']) != '' ? "'". trim($args['ds_resumo'])."'" : 'DEFAULT')."
                 WHERE cd_menu = ".intval($args['cd_menu'])."";
        }

        $this->db->query($qr_sql);
    }
    
    function desativar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.menu 
               SET dt_desativado = CURRENT_TIMESTAMP 
             WHERE cd_menu = ".intval($args['cd_menu']);
        
        $this->db->query($qr_sql);
    }
    
    function ordenar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.menu 
               SET nr_ordem = ".intval($args['nr_ordem'])." 
             WHERE cd_menu = ".intval($args['cd_menu']);
        
        $this->db->query($qr_sql);
    }
}
?>