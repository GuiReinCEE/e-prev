<?php
class Menu extends Controller
{
	function __construct()
	{
		parent::Controller();
		session_start();

		if($this->session->userdata('cd_menu')=='' OR $this->session->userdata('cd_menu')==507)
		{
			$this->session->set_userdata( array('cd_menu'=>3) );
			$_SESSION['cd_menu'] = 3;
		}
	}

	/**
	 * Método para acesso via POST
	 *
	 * @param string cd_menu ???
	 * @param string ds_funcao ???
	 */
	function load()
	{
		$mmmm = $this->input->post('cd_menu');
		if( $mmmm!='' )
		{
			if($mmmm==507) $mmmm=3;
			$this->session->set_userdata(array('cd_menu'=>$mmmm));
			$_SESSION['cd_menu'] = 3;
		}

		$this->getMenu( $this->session->userdata('cd_menu') );
		$_SESSION['cd_menu'] = $this->session->userdata('cd_menu');
		$_SESSION['usuario'] = $this->session->userdata('usuario');
	}

	function getMenu($cd_menu)
	{
		$cd_menu_pai = $cd_menu;

		$ar_menu[] = array($cd_menu, $cd_menu_pai);
		$fl_continua = true;
		while ($fl_continua)
		{
			$ob_menu = $this->getPai($cd_menu_pai);
			$cd_menu_pai = $ob_menu->cd_menu_pai;
			$cd_menu = $ob_menu->cd_menu;
			$ar_menu[] = array($cd_menu, $cd_menu_pai);
			if($this->getPai($cd_menu_pai)->cd_menu_pai == null)
			{
				$fl_continua = false;
			}
		}

		$nr_fim = (count($ar_menu) - 1);
		$nr_nivel = 1;
		while($nr_fim > -1)
		{
			echo $this->getMenuNivel($ar_menu[$nr_fim][0],$ar_menu[$nr_fim][1],$nr_nivel);
			$nr_fim--;
			$nr_nivel++;
		}
	}

	function getPai($cd_menu)
	{
		$qr_sql = " 
				SELECT cd_menu_pai,
				       cd_menu
				  FROM projetos.menu 
				 WHERE dt_desativado IS NULL AND cd_menu = ".$cd_menu."
			   ";

		$q = $this->db->query($qr_sql);
		return $q->row();
	}

	function getMenuNivel($cd_menu, $cd_menu_pai, $nr_nivel)
	{
		$qr_sql = " 
					SELECT a.*
					  FROM projetos.menu a
					 WHERE dt_desativado IS NULL AND cd_menu_pai = ".$cd_menu_pai."
					 ORDER BY nr_ordem;
				   ";
		$q = $this->db->query($qr_sql);

		$nr_conta = 0;

		$id_opcao = "";
		$opcao = "";

		foreach ( $q->result_array() as $ar_reg ) 
		{
			$ativo = "";
			if($cd_menu == $ar_reg['cd_menu'])
			{
				$ativo = 'class="menu_nivel_'.$nr_nivel.'-active" ';
			}

			// Tratamento do link
			$onclick = "";
			$href = "href='javascript:void(null)'";
			$target = "";

			if($ar_reg['ds_href']=="#")
			{
				$onclick = 'onclick="getMenu('.$ar_reg['cd_menu'].',{OPCAO}, \'\', \''.base_url().'index.php/'.'\');"';
			}
			else if( substr($ar_reg['ds_href'], 0, 7)=='http://' OR substr($ar_reg['ds_href'], 0, 8)=='https://' )
			{
				$onclick = "onclick=\"window.open('" . $ar_reg['ds_href'] . "');\"";
			}
			else if( strpos( $ar_reg['ds_href'],'.php') )
			{
				$protocolo = (isset($_SERVER['HTTPS']))?"https":"http";
				$link = $protocolo . "://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/" . $ar_reg['ds_href'];
				$onclick = 'onclick="getMenu('.$ar_reg['cd_menu'].',{OPCAO}, \'' . $link . '\', \''.base_url().'index.php/'.'\');"';
			}
			else
			{
				$link = base_url() . 'index.php/' . $ar_reg['ds_href'];
				$onclick = 'onclick="getMenu('.$ar_reg['cd_menu'].',{OPCAO}, \'' . $link . '\', \''.base_url().'index.php/'.'\')";';
			}

			$opcao.= '
			<li '.$ativo.' id="li_menu_'.$ar_reg['cd_menu'].'"><a ' . $href . ' ' . $onclick . ' ' . $target . '>' . $ar_reg['cd_menu'] . '-' . strtoupper($ar_reg['ds_menu']) . '</a></li>';

			$nr_conta++;
		}
		$botao = "";

		$opcao = str_replace("{OPCAO}","''",$opcao);

		$retorno = "";
		if($nr_conta>0 OR $nr_nivel==2)
		{
			$retorno = '<div id="menu_nivel_'.$nr_nivel.'">
						<ul>';
			$retorno.= $botao.$opcao."</ul></div>";	
		}
			
		return $retorno;
	}

	function manager($cd)
	{
		echo '
			<script>
				function salvar(c,l){
					window.open("", "managersave");
					
					document.forms[0].cd_menu.value = c;
					document.forms[0].link.value = l;
					document.forms[0].target = "managersave";
					document.forms[0].submit();
				}
			</script>
			<form method="POST" action="'.base_url().'/index.php/menu/manager_save">
			<input name=cd_menu><input name=link><br><br>
		';
		$this->load_menu_manager($cd);
		echo '</form>';
	}

	private function load_menu_manager($cd)
	{
		$q = $this->db->query('
			SELECT * 
			FROM projetos.menu 
			WHERE dt_desativado IS NULL AND cd_menu_pai='.$cd.' 
			ORDER BY nr_ordem
		');

		if( sizeof( $q->result() ) > 0 )
		{
			echo '<BLOCKQUOTE>
			';
			foreach($q->result() as $r)
			{
				echo '<input id=cd_'.$r->cd_menu.' size=3 value="'.$r->cd_menu.'">';
				echo '<input id=nome_'.$r->cd_menu.' value="'.$r->ds_menu.'">';
				echo '<input id=href_'.$r->cd_menu.' value="'.$r->ds_href.'" style="width:500px;">';
				echo '<input type=button value=save onclick="salvar('.$r->cd_menu.',document.getElementById(\'href_'.$r->cd_menu.'\').value);"><br />
				';
				$this->load_menu_manager($r->cd_menu);
			}
			echo '</BLOCKQUOTE>
			';
		}
	}

	function manager_save()
	{
		echo 'código = ' . $this->input->post('cd_menu');
		echo '<br />';
		echo 'link = ' . $this->input->post('link');

		$s='
			UPDATE projetos.menu
			SET ds_href=' . $this->db->escape($this->input->post('link')) . '  
			WHERE cd_menu='.$this->input->post('cd_menu')
		;
		$this->db->query($s);
		echo $s;
	}

	function classic_menu_load($cd_menu=0)
	{
		$this->load->helper('menu_helper');
		echo menu_classic_start($cd_menu);		
	}

	function classic_menu_mais_usado()
	{
		$this->load->helper('menu_helper');
		echo menu_mais_usados();		
	}
}
?>