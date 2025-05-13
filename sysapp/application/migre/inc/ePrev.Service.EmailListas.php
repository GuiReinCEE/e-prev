<?php
include_once( "ePrev.DAL.DBConnection.php" );

/**
 * Classe de Serviço para acessar listas de email no PG
 * 
 * @access public
 * @package ePrev
 * @subpackage Service
 * @require ePrev.DAL.DBConnetion.php
 */
class EmailListas {

    // DAL
    private $dal;

    function EmailListas($_db) {
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $_db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    /**
     * Cria uma string com todos os emails concatenados separados 
     * por vírgula da lista informada no parâmetro $_listName.
     * -----------------------------------------------------------
     * Query: 
     *     SELECT c.codigo, c.nome, c.usu_email
     *       FROM projetos.lista_email a
     * INNER JOIN projetos.lista_email_usuario b 
     *            ON a.cd_lista_email = b.cd_lista_email
     * INNER JOIN projetos.usuarios_controledi c
     *            ON c.codigo = b.cd_usuario
     *      WHERE a.id_lista = '{id_lista}'
     *        AND c.tipo <> 'X'
     * -----------------------------------------------------------
     */
    public function getEmailsToString( $_listName )
    {
        $this->dal->createQuery("

           SELECT c.codigo, c.nome, c.usu_email
             FROM projetos.lista_email a
       INNER JOIN projetos.lista_email_usuario b
                  ON a.cd_lista_email = b.cd_lista_email
       INNER JOIN projetos.usuarios_controledi c
                  ON c.codigo = b.cd_usuario
            WHERE a.id_lista = '{id_lista}'
              AND c.tipo <> 'X'
              AND a.dt_exclusao IS NULL
              AND b.dt_exclusao IS NULL

        ");

        $this->dal->setAttribute( "{id_lista}", $_listName );
        $result = $this->dal->getResultset();

        $virgula = "";
        while ( $reg = pg_fetch_array( $result ) ) {
			$emails .= $virgula . $reg["usu_email"] . "@eletroceee.com.br";
            $virgula = "; ";
		}
        
        return $emails;
    }

}

?>