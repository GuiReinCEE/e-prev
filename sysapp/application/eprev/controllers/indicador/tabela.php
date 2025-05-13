<?php
class tabela extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index($cd=0)
	{
		if(CheckLogin())
		{
			$data['cd_indicador_tabela_sel']=$cd;
			
			$q=$this->db->query("
			SELECT 
				it.cd_indicador_tabela as value, ig.ds_indicador_grupo || ' - ' || i.ds_indicador || ' - ' || ip.ds_periodo as text
			FROM 
					indicador.indicador i
					JOIN indicador.indicador_tabela it on i.cd_indicador=it.cd_indicador
					JOIN indicador.indicador_periodo ip on it.cd_indicador_periodo=ip.cd_indicador_periodo
					JOIN indicador.indicador_grupo ig on ig.cd_indicador_grupo=i.cd_indicador_grupo
			WHERE
					ig.dt_exclusao is null 
					AND ip.dt_exclusao is null 
					AND i.dt_exclusao IS NULL 
					AND it.dt_fechamento_periodo IS NULL 
					AND current_timestamp BETWEEN ip.dt_inicio AND ip.dt_fim 
					AND i.cd_usuario_responsavel=?
					AND it.cd_indicador_tabela=?
			ORDER BY i.nr_ordem
			" , array( usuario_id(), intval($cd) ) );

			$r=$q->row_array();
			$data['ds_nome_indicador'] = $r['text'];

			$this->load->view( 'indicador/tabela/index.php', $data );
		}
	}

	function salvar()
	{
		if( CheckLogin() && usuario_responsavel_indicador(usuario_id()) )
		{
			// *** indicador_tabela
			$cd_indicador_tabela = intval($this->input->post( 'cd_indicador_tabela' ));
			$ds_indicador_tabela = $this->input->post( 'ds_indicador_tabela' );

			//echo $cd_indicador_tabela;

			//exit;

			$q=$this->db->query( "SELECT cd_indicador FROM indicador.indicador_tabela WHERE cd_indicador_tabela=? AND dt_exclusao IS NULL", array($cd_indicador_tabela) );
			$r=$q->row_array();
			if($r) { $cd_indicador=intval($r['cd_indicador']); } else { $cd_indicador=0; }

			if( $cd_indicador_tabela==0 )
			{
				// *** insert
				$cd_indicador_tabela=$this->db->get_new_id("indicador.indicador_tabela", "cd_indicador_tabela");

				$sql="
				INSERT INTO indicador.indicador_tabela
				( cd_indicador_tabela, cd_indicador, ds_indicador_tabela, dt_inclusao, cd_usuario_inclusao )
				VALUES
				( {cd_indicador_tabela}, {cd_indicador}, '{ds_indicador_tabela}', CURRENT_TIMESTAMP, {cd_usuario_inclusao} )
				";
				esc('{cd_indicador_tabela}',$cd_indicador_tabela,$sql,'int');
				esc('{cd_indicador}',$cd_indicador,$sql,'int');
				esc('{ds_indicador_tabela}',$ds_indicador_tabela,$sql,'str',false);
				esc('{cd_usuario_inclusao}',usuario_id(),$sql,'int');

				$this->db->query($sql);
			}
			else
			{
				// *** update
				$sql="
				UPDATE indicador.indicador_tabela 
				SET ds_indicador_tabela = '{ds_indicador_tabela}' 
				WHERE cd_indicador_tabela = {cd_indicador_tabela} 
				";
				esc('{cd_indicador_tabela}', $cd_indicador_tabela, $sql, 'int');
				esc('{ds_indicador_tabela}', $ds_indicador_tabela, $sql, 'str', false);

				$this->db->query($sql);
			}

			if( intval( $cd_indicador_tabela ) )
			{
				$linhas=$this->input->post('linhas');
				$colunas=$this->input->post('colunas');

				$this->db->query( 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=?', array( $cd_indicador_tabela ) );
				for($i=0;$i<$linhas;$i++)
				{
					for($j=0;$j<$colunas;$j++)
					{
						$valor = $this->input->post( "tabela_cel_$i-$j" );
						$style= $this->input->post( "style_tabela_cel_$i-$j" );
						$m[$i][$j] = $valor;

						$this->db->query('INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES (?,?,?,?,?) '
						, array( $cd_indicador_tabela, intval($i), intval($j), $valor, $style ));

						echo "- Inseriu $i-$j:$valor<br>";
					}
				}

				echo '<script> parent.cd_indicador_tabela_event(); </script>';
			}
			else
			{
				echo 'Código do Indicador Tabela não encontrado';
			}
		}
	}

	function exibir_ajax()
	{
		if(CheckLogin() && usuario_responsavel_indicador(usuario_id()))
		{
			$this->load->helper('indicador');

			$alfa=array( 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			$cd_indicador_tabela = intval( $this->input->post('cd_indicador_tabela') );
			$cd_indicador=0;
			/*
			$cd_indicador = intval($this->input->post( 'cd_indicador' ));
			$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
			$r=$q->row_array();
			if($r)
			{
				$cd_indicador_tabela=intval($r['cd_indicador_tabela']);
			}
			else
			{
				$cd_indicador_tabela=0;
			}
			*/

			$linhas = intval($this->input->post( 'linhas' ));
			$colunas = intval($this->input->post( 'colunas' ));

			if( $linhas==0 && $colunas==0 && $cd_indicador_tabela>0 )
			{
				$q=$this->db->query("SELECT max(nr_linha) as maior_linha, max(nr_coluna) as maior_coluna, count(*) as quantos FROM indicador.indicador_parametro WHERE cd_indicador_tabela=? and dt_exclusao is null", array(intval($cd_indicador_tabela)));
				$r=$q->row_array();
				if(intval($r['quantos'])>0)
				{
					$linhas=intval($r['maior_linha'])+1;
					$colunas=intval($r['maior_coluna'])+1;
				}
			}

			$m = "<table border='0'>";
			$m.= "<tr><td></td>";
			for($j=0;$j<$colunas;$j++)
			{
				$m.= "<td title='Adicionar Coluna' style='background:#eee;' onmousemove='this.style.backgroundColor=\"green\"' onmouseout='this.style.backgroundColor=\"#eee\"' onclick='adicionar_coluna($cd_indicador, $j, true);'><img src='".base_url()."px.gif' width='5px' height='5px' /></td>";
				$m.= "<td style='text-align:center;font-size:12;font-family:courier new;'>
					<a href='javascript:void(0);' onclick='excluir_coluna($cd_indicador, $j);' title='Excluir coluna'>[X]</a>
					$alfa[$j]
				</td>";
				if( $j==$colunas-1 )
				{
					$m.= "<td title='Adicionar Coluna' style='background:#eee;' onmousemove='this.style.backgroundColor=\"green\"' onmouseout='this.style.backgroundColor=\"#eee\"' onclick='adicionar_coluna($cd_indicador, $j+1, false);'><img src='".base_url()."px.gif' width='5px' height='5px' /></td>";
				}
			}
			$m.= "</tr>";
			for($i=0;$i<$linhas;$i++)
			{
				$m.="<tr><td title='Adicionar Linha' style='background:#eee;' onmousemove='this.style.backgroundColor=\"green\"' onmouseout='this.style.backgroundColor=\"#eee\"' onclick='adicionar_linha( $cd_indicador, $i, true );'><img src='".base_url()."px.gif' width='5px' height='5px' /></td></tr>";
				$m .= "<tr><td style='text-align:center;font-size:12;font-family:courier new;'>
					<a href='javascript:void(0);' onclick='excluir_linha($cd_indicador, $i);' title='Excluir linha'>[X]</a>
					$i
				</td>";
				for($j=0;$j<$colunas;$j++)
				{
					$q=$this->db->query("SELECT * FROM indicador.indicador_parametro WHERE nr_linha=? AND nr_coluna=? AND cd_indicador_tabela=? and dt_exclusao is null", array($i,$j,intval($cd_indicador_tabela)));
					$r=$q->row_array();

					$ds_valor='';
					$ds_style='';
					if($r)
					{
						$ds_valor=$r['ds_valor'];
						$ds_style=$r['ds_style'];
					}

					// *** formula
					$input_formula="";$com_formula="";
					if(preg_match("/^=/", $ds_valor))
					{
						$input_formula="<input
							id='formula_tabela_cel_$i-$j'
							name='formula_tabela_cel_$i-$j'
							style=' display:none; $ds_style ; border-color:#FFD5D5; '
							disabled='disabled'
							type='text'
							class='tabela_cel formula'
							value='".indicador_tools::resultado_formula($cd_indicador_tabela,'',$j,$i)."'
							>";
						$com_formula=" com_formula";
					}

					$m.="
						<td></td>
						<td>
							<input 
							id='tabela_cel_$i-$j' 
							name='tabela_cel_$i-$j' 
							type='text' 
							class='tabela_cel celula$com_formula' 
							value='$ds_valor'
							style='$ds_style'
							/><input 
							id='style_tabela_cel_$i-$j' 
							name='style_tabela_cel_$i-$j' 
							onblur='aplicar_estilo($i,$j, this)' 
							class='tabela_cel estilo$com_formula' 
							style=' display:none; $ds_style' 
							type='text' 
							value='$ds_style'
							>$input_formula
						</td>
					";
				}
				$m.="</tr>";
				if($i==$linhas-1)
				{
					$m.="<tr><td title='Adicionar Linha' style='background:#eee;' onmousemove='this.style.backgroundColor=\"green\"' onmouseout='this.style.backgroundColor=\"#eee\"' onclick='adicionar_linha( $cd_indicador, $i+1, false );'><img src='".base_url()."px.gif' width='5px' height='5px' /></td></tr>";
				}
			}
			$m.="</table>";

			echo $m;
		}
	}

	function carregar_indicador_ajax()
	{
		if(CheckLogin() && usuario_responsavel_indicador(usuario_id()))
		{
			// $cd_indicador=$this->input->post('cd');
			// $this->load->model('projetos/Indicador_model','dbm');
			// $r=$this->dbm->carregar( intval($cd_indicador_tabela) );

			$cd_indicador_tabela=$this->input->post('cd');

			$query = $this->db->query( "
			SELECT it.*, i.cd_usuario_responsavel 
			FROM indicador.indicador_tabela it 
			JOIN indicador.indicador i ON i.cd_indicador=it.cd_indicador 
			WHERE it.cd_indicador_tabela=?
			", array($cd_indicador_tabela) );

			$result = $query->result_array();

			if(sizeof($result)>0)
			{
				$row['cd_indicador_tabela']=$result[0]['cd_indicador_tabela'];
				$row['ds_indicador_tabela']=utf8_encode($result[0]['ds_indicador_tabela']);
			}
			else
			{
				$row['cd_indicador_tabela']='';
				$row['ds_indicador_tabela']='';
			}
			$row['erro']='';
			
			if( $result[0]['cd_usuario_responsavel']!=usuario_id() )
			{
				$row=array();
				$row['erro']='Usuario nao e Responsavel pelo Indicador escolhido!';
			}
		}
		else
		{
			$row['erro']='Usuario nao esta configurado como responsavel por Indicadores';
		}

		echo json_encode(  $row  );
	}

	function linhas_e_colunas_ajax()
	{
		/*$cd_indicador=intval($this->input->post('cd_indicador'));
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);*/

		$cd_indicador_tabela=intval($this->input->post('cd_indicador_tabela'));
		$q=$this->db->query("SELECT max(nr_linha) as maior_linha, max(nr_coluna) as maior_coluna, count(*) as quantos FROM indicador.indicador_parametro WHERE cd_indicador_tabela=? and dt_exclusao is null", array(intval($cd_indicador_tabela)));
		$r=$q->row_array();

		$ret['linhas']=0;
		$ret['colunas']=0;

		if($r['quantos']>0)
		{
			$ret['linhas']=$r['maior_linha']+1;
			$ret['colunas']=$r['maior_coluna']+1;
		}
		
		echo json_encode($ret);
	}
	
	function executar_formula_ajax()
	{
		/*$cd_indicador=intval($this->input->post('cd_indicador'));
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);*/

		$cd_indicador_tabela=intval($this->input->post('cd_indicador'));
		
		$nr_coluna=$this->input->post('coluna');
		$nr_linha=$this->input->post('linha');
		
		$this->load->helper( 'indicador' );
		echo indicador_tools::resultado_formula( $cd_indicador_tabela, '', $nr_coluna, $nr_linha );
	}
	
	function teste_formula($nr_coluna,$nr_linha)
	{
		$cd_indicador=14;
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);
		$this->load->helper( 'indicador' );

		echo indicador_tools::resultado_formula( $cd_indicador_tabela, '', $nr_coluna, $nr_linha );
	}

	function criar_box_propriedade_estilo_ajax()
	{
		$this->load->helper('indicador');
		
		$style=$this->input->post('estilo');
		$astyle=explode(';', $style);
		$mstyle=array();

		foreach($astyle as $item)
		{
			if( trim($item)!='' )
			$mstyle[]=explode(':',$item);
		}
		
		indicador_box_estilo::listar_propriedades( $mstyle );
		
		// parray($mstyle);

		echo '<table border="0" style="width:100%;font-family:verdana;font-size:10;" cellpadding="0" cellspacing="0">';
		foreach($mstyle as $v)
		{
			if( is_array($v) & sizeof($v)==2 )
			{
				echo '<tr>';
				echo '<td style="text-align:left;width:150px;">'.indicador_box_estilo::propriedade( $v[0] ).': </td>';
				echo '<td>'.indicador_box_estilo::opcoes( $v[0], $v[1] ).'</td>';
				echo '</tr>';
			}
		}
		echo '</table>';
		
		echo "
<script>

function confirmar_estilo_wizard()
{
	s='';
	$('.estilo_objeto').each( function(){ if($(this).val()!='') { s+=$(this).attr('name')+':'+$(this).val()+';'; } } );
	$('#definir_estilo').val( s );
	$('#estilo_wizard_box').hide();
}

</script>
";
		
		echo br().'<center>'.comando( 'confirmar_estilo_wizard_btn', 'Confirmar', 'confirmar_estilo_wizard();' ).'</center>';
	}

	function test_style()
	{
		$this->load->helper('indicador');
		$style="background-color:#D5E4F5;background:url(../../skins/skin002/img/form/form-box-title-background.png); font-weight: bold;font-size:10;";

		$astyle=explode(';', $style);

		foreach($astyle as $item)
		{
			$mstyle[]=explode(':',$item);
		}

		echo '<table border="0" style="width:100%;font-family:verdana;font-size:10;" cellpadding="0" cellspacing="0">';
		foreach($mstyle as $v)
		{
			if( is_array($v) & sizeof($v)==2 )
			{
				echo '<tr>';
				echo '<td style="text-align:left;width:150px;">'. indicador_box_estilo::propriedade( $v[0] ) .': </td>';
				echo '<td><input style="width:100%;" value="'. $v[1] .'" /></td>';
				echo '</tr>';
			}
		}
		echo '</table>';
	}

	function test_inserir_linha($cd_indicador,$nr_linha,$final=false)
	{
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);

		if( ! $final )
		{
			$SELECT_INSERT = " SELECT cd_indicador_tabela, {nr_linha}, nr_coluna, '', ds_style
			FROM indicador.indicador_parametro
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_linha=({nr_linha}+1) AND dt_exclusao IS NULL ";
		}
		else
		{
			$SELECT_INSERT = " SELECT cd_indicador_tabela, {nr_linha}, nr_coluna, '', ds_style
			FROM indicador.indicador_parametro
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_linha=({nr_linha}-1) AND dt_exclusao IS NULL ";
		}

		$sql = "
			UPDATE indicador.indicador_parametro SET nr_linha=nr_linha+1 
			WHERE cd_indicador_tabela={cd_indicador_tabela} 
			AND nr_linha>={nr_linha} 
			AND dt_exclusao IS NULL;

			INSERT INTO indicador.indicador_parametro (cd_indicador_tabela, nr_linha, nr_coluna, ds_valor, ds_style) 
			$SELECT_INSERT;
		";

		esc( '{cd_indicador_tabela}', intval($cd_indicador_tabela), $sql, 'int');
		esc( '{nr_linha}', intval($nr_linha), $sql, 'int');

		$this->db->query( $sql );

		echo $sql;
	}

	function test_excluir_linha($cd_indicador,$nr_linha)
	{
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);

		$sql = "
			UPDATE indicador.indicador_parametro 
			SET dt_exclusao = current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_linha={nr_linha} AND dt_exclusao IS NULL;

			UPDATE indicador.indicador_parametro SET nr_linha=nr_linha-1 
			WHERE cd_indicador_tabela={cd_indicador_tabela} 
			AND nr_linha>={nr_linha} 
			AND dt_exclusao IS NULL;
		";

		esc( '{cd_usuario_exclusao}', intval(usuario_id()), $sql, 'int');
		esc( '{cd_indicador_tabela}', intval($cd_indicador_tabela), $sql, 'int');
		esc( '{nr_linha}', intval($nr_linha), $sql, 'int');

		$this->db->query( $sql );
	}

	function test_inserir_coluna($cd_indicador,$nr_coluna,$final=false)
	{
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);

		if( ! $final )
		{
			$SELECT_INSERT = " SELECT cd_indicador_tabela, nr_linha, {nr_coluna}, '', ds_style
			FROM indicador.indicador_parametro
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_coluna=({nr_coluna}+1) AND dt_exclusao IS NULL ";
		}
		else
		{
			$SELECT_INSERT = " SELECT cd_indicador_tabela, nr_linha, {nr_coluna}, '', ds_style
			FROM indicador.indicador_parametro
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_coluna=({nr_coluna}-1) AND dt_exclusao IS NULL ";
		}

		$sql = "
			UPDATE indicador.indicador_parametro SET nr_coluna=nr_coluna+1 
			WHERE cd_indicador_tabela={cd_indicador_tabela} 
			AND nr_coluna>={nr_coluna} 
			AND dt_exclusao IS NULL;

			INSERT INTO indicador.indicador_parametro (cd_indicador_tabela, nr_linha, nr_coluna, ds_valor, ds_style) 
			$SELECT_INSERT;
		";
		
		esc( '{cd_indicador_tabela}', intval($cd_indicador_tabela), $sql, 'int');
		esc( '{nr_coluna}', intval($nr_coluna), $sql, 'int');
		
		$this->db->query( $sql );
	}

	function test_excluir_coluna($cd_indicador,$nr_coluna)
	{
		$q=$this->db->query( "SELECT cd_indicador_tabela FROM indicador.indicador_tabela WHERE cd_indicador=? AND dt_exclusao IS NULL", array($cd_indicador) );
		$r=$q->row_array();
		$cd_indicador_tabela=intval($r['cd_indicador_tabela']);

		$sql = "
			UPDATE indicador.indicador_parametro 
			SET dt_exclusao = current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
			WHERE cd_indicador_tabela={cd_indicador_tabela} AND nr_coluna={nr_coluna} AND dt_exclusao IS NULL;

			UPDATE indicador.indicador_parametro SET nr_coluna=nr_coluna-1 
			WHERE cd_indicador_tabela={cd_indicador_tabela} 
			AND nr_coluna>={nr_coluna} 
			AND dt_exclusao IS NULL;
		";

		esc( '{cd_usuario_exclusao}', intval(usuario_id()), $sql, 'int');
		esc( '{cd_indicador_tabela}', intval($cd_indicador_tabela), $sql, 'int');
		esc( '{nr_coluna}', intval($nr_coluna), $sql, 'int');

		$this->db->query( $sql );
	}
}
