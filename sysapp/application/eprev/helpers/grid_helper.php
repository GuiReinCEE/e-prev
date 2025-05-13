<?php
/**
 * Classe para criação de um Grid a partir de arrays PHP
 * O grid é construído utilizando o framework javascrip SORTABLE TABLE
 */
class grid
{
	public $head=array();

	/**
	 * $body
	 *
	 * Parametro que determina o corpo da tabela o parametro é um array multidimensional contento as linhas e colunas.
	 * As colunas podem ser definidas apenas com o valor ou com argumentos de configuração.
	 *
	 * Por exemplo
	 *
	 *    $body[0][0] = 'Conteúdo da linha ZERO coluna ZERO';
	 *    $body[0][1] = array( 'Conteúdo da linha ZERO coluna UM', 'text-align:right;' );
	 *    $body[0][2] = array( 15, 'text-align:right;', 'int' );
	 *
	 * No exemplo é possível ver que as colunas podem ser configuradas com pelo menos 1 parametro e no máximo 3
	 *
	 * são eles:
	 *
	 * $body[0][2] = array( [VALUE], [STYLE], [SUM] );
	 *
	 *	[VALUE] : string	determina o valor que irá aparecer
	 *	[STYLE] : string	configura o atributo STYLE da TD onde o conteúdo vai aparecer
	 *	[SUM] :	  string	padrão: FALSE
							valores possíveis: int, float
							determina se a coluna será somada no final e qual o tipo de valor da coluna,
							se esse parametro conter o valor 'int' ou 'float' depois de imprimir todas as
							linhas, será criada um rodapé com a somatória dos valores das colunas indicadas.
	 *
	 */
	public $body=array();

	public $body_template=array();
	public $view_count=true;
	public $hide_sum=false;
	public $sums=array();
	public $count=0;
	public $id_tabela = "table-1";
    public $view_data = true;
	public $col_oculta = Array();
	public $col_window = Array();
	public $w_detalhe = false;
	public $w_detalhe_col_iniciar = 0;
	public $width = "100%";
	public $align = "center";

	function render()
	{
		$a_sum=array();

		if(!is_array($this->body)) $this->body=array();

		$this->count=count($this->body);

		$barra_opcao = "";
		if ($this->view_count)
		{
			$barra_opcao = '
							<caption style="'.(!$this->view_count ? "display:none;" : "").'">
								<div style="height: 5px;"></div>
								<ul id="grid-menu-bar-'.$this->id_tabela.'">
									<li><div>Quantidade: <span id="gridCount'.$this->id_tabela.'">'.count($this->body).'</span></div></li>
									<li><div>Procurar: <input type="text" id="'.$this->id_tabela.'FilterTextBox" name="'.$this->id_tabela.'FilterTextBox" style="width: 400px;"></div></li>
									<li><div><a href="javascript:void(0);" onclick="$(\'#'.$this->id_tabela.'\').table2CSV();" title="Download Excel" alt="Download Excel"><img src="'.base_url().'img/grid/excel.png" border="0" title="Download Excel" alt="Download Excel" style="cursor:pointer;"></a></div></li>
								</ul>
								<div style="height: 5px;"></div>
							</caption>			
			               ';
		}
		
		
		$out = '
		<style>
			#grid-menu-bar-'.$this->id_tabela.' {
				margin: 0px 0px 0px 0px;
				padding: 3px 3px 2px 3px;
				height: 28px;
				line-height: 100%;
				border-radius: 4px;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				box-shadow: 2px 2px 3px #666666;
				-webkit-box-shadow: 2px 2px 3px #666666;
				-moz-box-shadow: 2px 2px 3px #666666;
				background: #E3E3E3;
				border: solid 1px #D4D4D4;
				position:relative;
				/*z-index:999;*/
			}
			#grid-menu-bar-'.$this->id_tabela.' li {
				margin: 0px 0px 3px 0px;
				padding: 0px 3px 0px 3px;
				float: left;
				position: relative;
				list-style: none;
				display: table-cell;
				vertical-align: middle;
				height: 30px;
				line-height: 30px;
			}
			#grid-menu-bar-'.$this->id_tabela.' div {
				font-weight: normal;
				font-family: verdana;
				font-style: normal;
				font-size: 10px;
				text-decoration: none;
				display: block;
				padding: 3px 15px 3px 15px;
				margin: 0;
				margin-bottom: 6px;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				display: table-cell;
				vertical-align: middle;  
				height: 20px;
				line-height: 20px;
			}
			#grid-menu-bar-'.$this->id_tabela.' li ul li div {
			  margin: 0;
			}

			#grid-menu-bar-'.$this->id_tabela.' ul li {
			  float: none;
			  margin: 0;
			  padding: 0;
			}

			#grid-menu-bar-'.$this->id_tabela.' {
			  display: inline-block;
			}

			html[xmlns] #grid-menu-bar-'.$this->id_tabela.' {
			  display: block;
			}

			* html #grid-menu-bar-'.$this->id_tabela.' {
			  height: 1%;
			}				
		</style>		

<table class="sort-table" id="'.$this->id_tabela.'" align="'.$this->align.'" width="'.$this->width.'" cellspacing="2" cellpadding="2">
	'.$barra_opcao.'
	<thead>
	<tr>';
		#### JANELA DETALHE ####
		if($this->w_detalhe)
		{		
			$id_td = "0_0-".$this->id_tabela;
			$out .= '<th id="'.$id_td.'" coluna="0" linha="0"></th>';
			$i_col = 1;
		}
		else
		{
			$i_col = 0;
		}
		
		foreach($this->head as $it)
		{
			if(is_array($it))
			{
				$content=$it[0];
				$style=$it[1];
			}
			else
			{
				$content=$it;
				$style='';
			}
			
			$id_td = "0_".$i_col."-".$this->id_tabela;

			if(in_array($i_col, $this->col_oculta))
			{
				$out .= '<th id="'.$id_td.'" coluna="'.$i_col.'" linha="0" style="display:none;  '.$style.'">' . $content . '</th>';
			}
			else
			{
				$out .= '<th id="'.$id_td.'" coluna="'.$i_col.'" linha="0" style="'.$style.'">' . $content . '</th>';
			}
			
			$i_col++;
		}

		$out .= '</tr>
	</thead>
	<tbody>';

		$i_lin = 1;
		foreach($this->body as $subcol)
		{
			$out .= '<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">';
			
			#### JANELA DETALHE ####
			if($this->w_detalhe)
			{
				$id_td = $i_lin."_0-".$this->id_tabela;
				$out.='<td id="'.$id_td.'" coluna="0" linha="'.$i_lin.'" valign="top" style="'.$style.'" ><a href="javascript: void(0);" onclick="getGridRow(this,\''.$this->id_tabela.'\','.$this->w_detalhe_col_iniciar.');"><img src="'.base_url().'img/grid/zoom_detalhe.png" border="0" title="Ampliar/Detalhe"></a></td>';
				$i_col=1;
			}
			else
			{
				$i_col=0;
			}
			
			foreach($subcol as $it)
			{
				$sum=false;
				if(is_array($it))
				{
					$content=$it[0];
					$style=$it[1];

					if( sizeof($it)>2 )
					{
						$sum=$it[2];
						
					}
				}
				else
				{
					$content=$it;
					$style='text-align:center;';
				}

				if($sum)
				{
					if( !isset($a_sum[$i_col]) ) { $a_sum[$i_col]=''; }
					$value = $content;
					$value = str_replace( ".", "", $value );
					$value = str_replace( ",", ".", $value );

					$a_sum[$i_col] += floatval(strip_tags($value)); /*} else { $a_sum[$i_col]=''; */
				}

				$id_td = $i_lin."_".$i_col."-".$this->id_tabela;
				
				$gridWindow = "";
				if((in_array($i_col, $this->col_window)) and (trim($content) != ""))
				{
					$gridWindow = '<a id="'.$id_td.'_iconWindow" href="javascript:void(0);" onclick="gridWindowShow(\''.$this->head[$i_col].'\',\''.$id_td.'\');" title="Ampliar"><img src="'.base_url().'js/janela/zoom.gif" border="0" title="Ampliar"></a> ';
				}
				
				if(in_array($i_col, $this->col_oculta))
				{				
					$out.='<td id="'.$id_td.'" coluna="'.$i_col.'" linha="'.$i_lin.'" valign="top" style="display:none; '.$style.'" >'.$gridWindow.$content.'</td>';
				}
				else
				{
					$out.='<td id="'.$id_td.'" coluna="'.$i_col.'" linha="'.$i_lin.'" valign="top" style="'.$style.'" >'.$gridWindow.$content.'</td>';
				}
				
				$i_col++;
			}
			$out .= '</tr>';
			$i_lin++;
		}

		foreach($this->body_template as $subcol)
		{
			foreach($subcol as $it)
			{
				$out.=$it;
			}
		}

		$out .= '</tbody>';
	
		if(sizeof($a_sum)>0)
		{
			
			$this->sums=$a_sum;
			if( ! $this->hide_sum )
			{
				$out.="<tbody class='sort-totalizador'>";
	
				for($i_col=0;$i_col<sizeof($this->body[0]);$i_col++)
				{
					if( !isset($a_sum[$i_col]) ){ $a_sum[$i_col]=''; }
					$body_args = $this->body[0][$i_col];
		
					if( is_array($body_args) )
					{
						$style = $body_args[1];
						$tipo = (isset($body_args[2]))?$body_args[2]:FALSE;
					}
		
					$out.="<td id='".$this->id_tabela."-sort-totalizador-".$i_col."' style='".$style."'>".( (trim($a_sum[$i_col])!='')?number_format($a_sum[$i_col], ($tipo=='int')?0:2, ',', '.'):'' )."</td>";
				}
				$out.='</tbody>';
			}
		}

	$out.='</table>';

    if($this->view_data)
    {
        $out.='<div style="text-align:left; font-size: 7pt;">'.date("d/m/Y H:i:s").'</div>';
    }
	
	$out.='
			<script>
				function removeAccents_'.md5($this->id_tabela).'(s){
					var r = s.toLowerCase();
					non_asciis = {"a": "[àáâãäå]", "ae": "æ", "c": "ç", "e": "[èéêë]", "i": "[ìíîï]", "n": "ñ", "o": "[òóôõö]", "oe": "œ", "u": "[ùúûuü]", "y": "[ýÿ]"};
					for (i in non_asciis) 
					{ 
						r = r.replace(new RegExp(non_asciis[i], "g"), i); 
					}
					return r;
				};
				
				$(document).ready(function(){
					$("#'.$this->id_tabela.'FilterTextBox").keypress(function(event){
						if (event.keyCode == 13) 
						{
							event.preventDefault();
							return false;
						}
					});							
				
					//add index column with all content.
					$("#'.$this->id_tabela.' tbody tr:has(td)").each(function(){
						var t = $(this).text().toLowerCase(); //all row text
						$("<td class=\'indexColumn\' style=\'display:none;\'></td>").hide().text(removeAccents_'.md5($this->id_tabela).'(t)).appendTo(this);
					});//each tr
				 
					$("#'.$this->id_tabela.'FilterTextBox").keyup(function(event){
						if (event.keyCode == 27) 
						{
							$("#'.$this->id_tabela.'FilterTextBox").val("").keyup();
						}
						else
						{
							var s = $(this).val();
								s = removeAccents_'.md5($this->id_tabela).'(s);
								s = s.toLowerCase().split(" ");
								
							//show all rows.
							$("#'.$this->id_tabela.' tbody tr:hidden").show();
							$.each(s, function(){
								$("#'.$this->id_tabela.' tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
							});//each
							
							var rowCount = $("#'.$this->id_tabela.' tbody tr:visible").length;
							$("#gridCount'.$this->id_tabela.'").html(rowCount);
						}
					});//key up.
				});//document.ready				
			</script>	
	      ';
	
    $out.='<BR>';

		return $out;
	}
}