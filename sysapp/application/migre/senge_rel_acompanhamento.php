<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/tabela_cor.php');	
	require('inc/fpdf153/fpdf.php');
	
	//echo "<PRE>";	print_r($ar_tabela_cor); 	exit;
	
	function formataNumero($vl_valor)
	{
		return number_format($vl_valor,2,",",".");
	}
	
	
	function getBalancoPolicentro($cd_conta, $dt_atual, $dt_anterior, $fl_sinal = false)
	{
		GLOBAL $db;

		$nr_sinal = "";
		if($fl_sinal)
		{
			$nr_sinal = " * -1";
		}
		
		$qr_sql = "
					SELECT x.cd_conta,
					       (SUM(x.vl_saldo_atual) ".$nr_sinal.")::NUMERIC AS vl_saldo_atual,
					       (SUM(x.vl_saldo_anterior) ".$nr_sinal.")::NUMERIC AS vl_saldo_anterior
					  FROM (
							SELECT cr.cd_conta,
							       cr.vl_saldo_atual,
							       0 AS vl_saldo_anterior
							  FROM public.ct_razao cr
							 WHERE cr.cd_empresa   = 17
							   AND cr.cd_plano     = 8
							   AND cr.cd_conta     = '".$cd_conta."'
							   AND cr.dt_ref_razao = TO_DATE('".$dt_atual."','YYYY-MM-DD')
							   
							 UNION
							 
							SELECT cr.cd_conta,
							       0 AS vl_saldo_atual,
							       cr.vl_saldo_atual AS vl_saldo_anterior
							  FROM public.ct_razao cr
							 WHERE cr.cd_empresa   = 17
							   AND cr.cd_plano     = 8
							   AND cr.cd_conta     = '".$cd_conta."'
							   AND cr.dt_ref_razao = TO_DATE('".$dt_anterior."','YYYY-MM-DD')
					       ) x
					  GROUP BY x.cd_conta	
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);		
		
		return $ar_reg;
	}
	
	function getResultadoPolicentro($cd_conta, $dt_atual, $dt_anterior, $fl_sinal = false)
	{
		GLOBAL $db;
		
		$nr_sinal = "";
		if($fl_sinal)
		{
			$nr_sinal = " * -1";
		}
		
		$qr_sql = "
					SELECT CASE WHEN DATE_TRUNC('year', TO_DATE('".$dt_atual."','YYYY-MM-DD')) = DATE_TRUNC('year', CURRENT_DATE)
								THEN 'S'
								ELSE 'N'
						   END AS fl_ano_corrente
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$fl_ano_corrente = "";
		if($ar_reg['fl_ano_corrente'] == "N")
		{
			$fl_ano_corrente = "_anterior";
		}
		
		$qr_sql = "
					SELECT x.cd_conta,
					       (SUM(x.vl_saldo_atual) ".$nr_sinal.")::NUMERIC AS vl_saldo_atual,
					       (SUM(x.vl_saldo_anterior) ".$nr_sinal.")::NUMERIC AS vl_saldo_anterior
					  FROM (
							SELECT cr.cd_conta,
							       cr.vl_saldo_atual,
							       0 AS vl_saldo_anterior
							  FROM public.ct_razao".$fl_ano_corrente." cr
							 WHERE cr.cd_empresa   = 17
							   AND cr.cd_plano     = 8
							   AND cr.cd_conta     = '".$cd_conta."'
							   AND cr.dt_ref_razao = TO_DATE('".$dt_atual."','YYYY-MM-DD')
							   
							 UNION
							 
							SELECT cr.cd_conta,
							       0 AS vl_saldo_atual,
							       cr.vl_saldo_atual AS vl_saldo_anterior
							  FROM public.ct_razao_anterior cr
							 WHERE cr.cd_empresa   = 17
							   AND cr.cd_plano     = 8
							   AND cr.cd_conta     = '".$cd_conta."'
							   AND cr.dt_ref_razao = TO_DATE('".$dt_anterior."','YYYY-MM-DD')
					       ) x
					  GROUP BY x.cd_conta		
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);	

		return $ar_reg;
	}	
	
	function ConvertSize($size=5,$maxsize=0)
	{
		if ( stristr($size,'px') ) $size *= 0.2645; //pixels
		elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
		elseif ( stristr($size,'mm') ) $size += 0; //millimeters
		elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
		elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
		elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
		elseif ( stristr($size,'%') )
		{
			$size += 0; //make "90%" become simply "90" 
			$size *= $maxsize/100;
		}
		else $size *= 0.2645; //nothing == px

		return $size;
	}

	class PDF extends FPDF
	{
		var $widths;
		var $aligns;
		var $legends;
		var $wLegend;
		var $sum;
		var $NbVal;		
		
		function Header()
		{
			$this->SetTopMargin(15);
			$ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,102,0)));
			$this->RoundedRect(10, 10, 190, 277, 3.50,'1111', 'DF',$ar_estilo, array(255, 255, 255));	
			$this->Image('img/logo_plano_7_marca_dagua.png', 45, 230, ConvertSize(583,$ob_pdf->pgwidth), ConvertSize(242,$ob_pdf->pgwidth),'','',false);
			$this->SetX(15);
			$this->SetLineWidth(0);
			$this->SetDrawColor(0,0,0);		
		}
		
		function novaPagina()
		{
			
			$this->AddPage();	
		
		}		
		
		

		function SetWidths($w)
		{
		    //Set the array of column widths
		    $this->widths=$w;
		}

		function SetAligns($a)
		{
		    //Set the array of column alignments
		    $this->aligns=$a;
		}

		function Row($data)
		{
		    //Calculate the height of the row
		    $nb=0;
		    for($i=0;$i<count($data);$i++)
		        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		    $h=5*$nb;
		    //Issue a page break first if needed
		    $this->CheckPageBreak($h);
		    //Draw the cells of the row
		    for($i=0;$i<count($data);$i++)
		    {
		        $w=$this->widths[$i];
		        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
		        //Save the current position
		        $x=$this->GetX();
		        $y=$this->GetY();
		        //Draw the border
		        # function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
				$this->Rect($x,$y,$w,$h);
		        //Print the text
		        $this->MultiCell($w,5,$data[$i],0,$a);
		        //Put the position to the right of the cell
		        $this->SetXY($x+$w,$y);
		    }
		    //Go to the next line
		    $this->Ln($h);
		}

		function CheckPageBreak($h)
		{
		    //If the height h would cause an overflow, add a new page immediately
		    if($this->GetY()+$h > $this->PageBreakTrigger)
		        #$this->AddPage($this->CurOrientation); 
				$this->novaPagina();
		}

		function NbLines($w,$txt)
		{
		    //Computes the number of lines a MultiCell of width w will take
		    $cw=&$this->CurrentFont['cw'];
		    if($w==0)
		        $w=$this->w-$this->rMargin-$this->x;
		    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		    $s=str_replace("\r",'',$txt);
		    $nb=strlen($s);
		    if($nb>0 and $s[$nb-1]=="\n")
		        $nb--;
		    $sep=-1;
		    $i=0;
		    $j=0;
		    $l=0;
		    $nl=1;
		    while($i<$nb)
		    {
		        $c=$s[$i];
		        if($c=="\n")
		        {
		            $i++;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		            continue;
		        }
		        if($c==' ')
		            $sep=$i;
		        $l+=$cw[$c];
		        if($l>$wmax)
		        {
		            if($sep==-1)
		            {
		                if($i==$j)
		                    $i++;
		            }
		            else
		                $i=$sep+1;
		            $sep=-1;
		            $j=$i;
		            $l=0;
		            $nl++;
		        }
		        else
		            $i++;
		    }
		    return $nl;
		}		
		
	    // Sets line style
	    // Parameters:
	    // - style: Line style. Array with keys among the following:
	    //   . width: Width of the line in user units
	    //   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
	    //   . join: miter, round or bevel
	    //   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
	    //           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
	    //                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
	    //   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
	    //   . color: Draw color. Array with components (red, green, blue)
	    function SetLineStyle($style) {
	        extract($style);
	        if (isset($width)) {
	            $width_prev = $this->LineWidth;
	            $this->SetLineWidth($width);
	            $this->LineWidth = $width_prev;
	        }
	        if (isset($cap)) {
	            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
	            if (isset($ca[$cap]))
	                $this->_out($ca[$cap] . ' J');
	        }
	        if (isset($join)) {
	            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
	            if (isset($ja[$join]))
	                $this->_out($ja[$join] . ' j');
	        }
	        if (isset($dash)) {
	            $dash_string = '';
	            if ($dash) {
	                if(ereg('^.+,', $dash))
	                    $tab = explode(',', $dash);
	                else
	                    $tab = array($dash);
	                $dash_string = '';
	                foreach ($tab as $i => $v) {
	                    if ($i > 0)
	                        $dash_string .= ' ';
	                    $dash_string .= sprintf('%.2f', $v);
	                }
	            }
	            if (!isset($phase) || !$dash)
	                $phase = 0;
	            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
	        }
	        if (isset($color)) {
	            list($r, $g, $b) = $color;
	            $this->SetDrawColor($r, $g, $b);
	        }
	    }

	    // Draws a line
	    // Parameters:
	    // - x1, y1: Start point
	    // - x2, y2: End point
	    // - style: Line style. Array like for SetLineStyle
	    function Line($x1, $y1, $x2, $y2, $style = null) {
	        if ($style)
	            $this->SetLineStyle($style);
	        parent::Line($x1, $y1, $x2, $y2);
	    }

	    // Draws a rectangle
	    // Parameters:
	    // - x, y: Top left corner
	    // - w, h: Width and height
	    // - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
	    // - border_style: Border style of rectangle. Array with some of this index
	    //   . all: Line style of all borders. Array like for SetLineStyle
	    //   . L: Line style of left border. null (no border) or array like for SetLineStyle
	    //   . T: Line style of top border. null (no border) or array like for SetLineStyle
	    //   . R: Line style of right border. null (no border) or array like for SetLineStyle
	    //   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $border_style = null;
	                parent::Rect($x, $y, $w, $h, $style);
	                break;
	            case 'DF': case 'FD':
	                if (!$border_style || isset($border_style['all'])) {
	                    if (isset($border_style['all'])) {
	                        $this->SetLineStyle($border_style['all']);
	                        $border_style = null;
	                    }
	                } else
	                    $style = 'F';
	                parent::Rect($x, $y, $w, $h, $style);
	                break;
	            default:
	                if (!$border_style || isset($border_style['all'])) {
	                    if (isset($border_style['all']) && $border_style['all']) {
	                        $this->SetLineStyle($border_style['all']);
	                        $border_style = null;
	                    }
	                    parent::Rect($x, $y, $w, $h, $style);
	                }
	                break;
	        }
	        if ($border_style) {
	            if (isset($border_style['L']) && $border_style['L'])
	                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
	            if (isset($border_style['T']) && $border_style['T'])
	                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
	            if (isset($border_style['R']) && $border_style['R'])
	                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
	            if (isset($border_style['B']) && $border_style['B'])
	                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
	        }
	    }

	    // Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
	    // Parameters:
	    // - x0, y0: Start point
	    // - x1, y1: Control point 1
	    // - x2, y2: Control point 2
	    // - x3, y3: End point
	    // - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
	    // - line_style: Line style for curve. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $op = 'f';
	                $line_style = null;
	                break;
	            case 'FD': case 'DF':
	                $op = 'B';
	                break;
	            default:
	                $op = 'S';
	                break;
	        }
	        if ($line_style)
	            $this->SetLineStyle($line_style);

	        $this->_Point($x0, $y0);
	        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
	        $this->_out($op);
	    }

	    // Draws an ellipse
	    // Parameters:
	    // - x0, y0: Center point
	    // - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
	    // - angle: Orientation angle (anti-clockwise)
	    // - astart: Start angle
	    // - afinish: Finish angle
	    // - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
	    // - line_style: Line style for ellipse. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - nSeg: Ellipse is made up of nSeg Bézier curves
	    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
	        if ($rx) {
	            if (!(false === strpos($style, 'F')) && $fill_color) {
	                list($r, $g, $b) = $fill_color;
	                $this->SetFillColor($r, $g, $b);
	            }
	            switch ($style) {
	                case 'F':
	                    $op = 'f';
	                    $line_style = null;
	                    break;
	                case 'FD': case 'DF':
	                    $op = 'B';
	                    break;
	                case 'C':
	                    $op = 's'; // small 's' means closing the path as well
	                    break;
	                default:
	                    $op = 'S';
	                    break;
	            }
	            if ($line_style)
	                $this->SetLineStyle($line_style);
	            if (!$ry)
	                $ry = $rx;
	            $rx *= $this->k;
	            $ry *= $this->k;
	            if ($nSeg < 2)
	                $nSeg = 2;

	            $astart = deg2rad((float) $astart);
	            $afinish = deg2rad((float) $afinish);
	            $totalAngle = $afinish - $astart;

	            $dt = $totalAngle/$nSeg;
	            $dtm = $dt/3;

	            $x0 *= $this->k;
	            $y0 = ($this->h - $y0) * $this->k;
	            if ($angle != 0) {
	                $a = -deg2rad((float) $angle);
	                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
	                $x0 = 0;
	                $y0 = 0;
	            }

	            $t1 = $astart;
	            $a0 = $x0 + ($rx * cos($t1));
	            $b0 = $y0 + ($ry * sin($t1));
	            $c0 = -$rx * sin($t1);
	            $d0 = $ry * cos($t1);
	            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
	            for ($i = 1; $i <= $nSeg; $i++) {
	                // Draw this bit of the total curve
	                $t1 = ($i * $dt) + $astart;
	                $a1 = $x0 + ($rx * cos($t1));
	                $b1 = $y0 + ($ry * sin($t1));
	                $c1 = -$rx * sin($t1);
	                $d1 = $ry * cos($t1);
	                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
	                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
	                            ($a1 - ($c1 * $dtm)) / $this->k,
	                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
	                            $a1 / $this->k,
	                            $this->h - ($b1 / $this->k));
	                $a0 = $a1;
	                $b0 = $b1;
	                $c0 = $c1;
	                $d0 = $d1;
	            }
	            $this->_out($op);
	            if ($angle !=0)
	                $this->_out('Q');
	        }
	    }

	    // Draws a circle
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius
	    // - astart: Start angle
	    // - afinish: Finish angle
	    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
	    // - line_style: Line style for circle. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - nSeg: Ellipse is made up of nSeg Bézier curves
	    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
	        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
	    }

	    // Draws a polygon
	    // Parameters:
	    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
	        $np = count($p) / 2;
	        if (!(false === strpos($style, 'F')) && $fill_color) {
	            list($r, $g, $b) = $fill_color;
	            $this->SetFillColor($r, $g, $b);
	        }
	        switch ($style) {
	            case 'F':
	                $line_style = null;
	                $op = 'f';
	                break;
	            case 'FD': case 'DF':
	                $op = 'B';
	                break;
	            default:
	                $op = 'S';
	                break;
	        }
	        $draw = true;
	        if ($line_style)
	            if (isset($line_style['all']))
	                $this->SetLineStyle($line_style['all']);
	            else { // 0 .. (np - 1), op = {B, S}
	                $draw = false;
	                if ('B' == $op) {
	                    $op = 'f';
	                    $this->_Point($p[0], $p[1]);
	                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
	                        $this->_Line($p[$i], $p[$i + 1]);
	                    $this->_Line($p[0], $p[1]);
	                    $this->_out($op);
	                }
	                $p[$np * 2] = $p[0];
	                $p[($np * 2) + 1] = $p[1];
	                for ($i = 0; $i < $np; $i++)
	                    if (!empty($line_style[$i]))
	                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
	            }

	        if ($draw) {
	            $this->_Point($p[0], $p[1]);
	            for ($i = 2; $i < ($np * 2); $i = $i + 2)
	                $this->_Line($p[$i], $p[$i + 1]);
	            $this->_Line($p[0], $p[1]);
	            $this->_out($op);
	        }
	    }

	    // Draws a regular polygon
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius of circumscribed circle
	    // - ns: Number of sides
	    // - angle: Orientation angle (anti-clockwise)
	    // - circle: Draw circumscribed circle or not
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
	        if ($ns < 3)
	            $ns = 3;
	        if ($circle)
	            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
	        $p = null;
	        for ($i = 0; $i < $ns; $i++) {
	            $a = $angle + ($i * 360 / $ns);
	            $a_rad = deg2rad((float) $a);
	            $p[] = $x0 + ($r * sin($a_rad));
	            $p[] = $y0 + ($r * cos($a_rad));
	        }
	        $this->Polygon($p, $style, $line_style, $fill_color);
	    }

	    // Draws a star polygon
	    // Parameters:
	    // - x0, y0: Center point
	    // - r: Radius of circumscribed circle
	    // - nv: Number of vertices
	    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
	    // - angle: Orientation angle (anti-clockwise)
	    // - circle: Draw circumscribed circle or not
	    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
	    // - line_style: Line style. Array with one of this index
	    //   . all: Line style of all lines. Array like for SetLineStyle
	    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
	    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
	    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
	    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
	        if ($nv < 2)
	            $nv = 2;
	        if ($circle)
	            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
	        $p2 = null;
	        $visited = null;
	        for ($i = 0; $i < $nv; $i++) {
	            $a = $angle + ($i * 360 / $nv);
	            $a_rad = deg2rad((float) $a);
	            $p2[] = $x0 + ($r * sin($a_rad));
	            $p2[] = $y0 + ($r * cos($a_rad));
	            $visited[] = false;
	        }
	        $p = null;
	        $i = 0;
	        do {
	            $p[] = $p2[$i * 2];
	            $p[] = $p2[($i * 2) + 1];
	            $visited[$i] = true;
	            $i += $ng;
	            $i %= $nv;
	        } while (!$visited[$i]);
	        $this->Polygon($p, $style, $line_style, $fill_color);
	    }

	    // Draws a rounded rectangle
	    // Parameters:
	    // - x, y: Top left corner
	    // - w, h: Width and height
	    // - r: Radius of the rounded corners
	    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
	    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
	    // - border_style: Border style of rectangle. Array like for SetLineStyle
	    // - fill_color: Fill color. Array with components (red, green, blue)
	    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
	        if ('0000' == $round_corner) // Not rounded
	            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
	        else { // Rounded
	            if (!(false === strpos($style, 'F')) && $fill_color) {
	                list($red, $g, $b) = $fill_color;
	                $this->SetFillColor($red, $g, $b);
	            }
	            switch ($style) {
	                case 'F':
	                    $border_style = null;
	                    $op = 'f';
	                    break;
	                case 'FD': case 'DF':
	                    $op = 'B';
	                    break;
	                default:
	                    $op = 'S';
	                    break;
	            }
	            if ($border_style)
	                $this->SetLineStyle($border_style);

	            $MyArc = 4 / 3 * (sqrt(2) - 1);

	            $this->_Point($x + $r, $y);
	            $xc = $x + $w - $r;
	            $yc = $y + $r;
	            $this->_Line($xc, $y);
	            if ($round_corner[0])
	                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
	            else
	                $this->_Line($x + $w, $y);

	            $xc = $x + $w - $r ;
	            $yc = $y + $h - $r;
	            $this->_Line($x + $w, $yc);

	            if ($round_corner[1])
	                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
	            else
	                $this->_Line($x + $w, $y + $h);

	            $xc = $x + $r;
	            $yc = $y + $h - $r;
	            $this->_Line($xc, $y + $h);
	            if ($round_corner[2])
	                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
	            else
	                $this->_Line($x, $y + $h);

	            $xc = $x + $r;
	            $yc = $y + $r;
	            $this->_Line($x, $yc);
	            if ($round_corner[3])
	                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
	            else {
	                $this->_Line($x, $y);
	                $this->_Line($x + $r, $y);
	            }
	            $this->_out($op);
	        }
	    }

	    /* PRIVATE METHODS */

	    // Sets a draw point
	    // Parameters:
	    // - x, y: Point
	    function _Point($x, $y) {
	        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
	    }

	    // Draws a line from last draw point
	    // Parameters:
	    // - x, y: End point
	    function _Line($x, $y) {
	        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
	    }

	    // Draws a Bézier curve from last draw point
	    // Parameters:
	    // - x1, y1: Control point 1
	    // - x2, y2: Control point 2
	    // - x3, y3: End point
	    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
	        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	    }		
		
		function Footer()
		{
		    //Go to 1.5 cm from bottom
		    $this->SetY(-15);
		    //Select Arial italic 8
		    $this->SetFont('Courier','I',8);
		    //Print current and total page numbers
		    //$this->Cell(0,10,$this->PageNo().'/{nb}',0,0,'C');
		}
		
		function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
		{
			if($cw){
				$d = $b;
				$b = $o - $a;
				$a = $o - $d;
			}else{
				$b += $o;
				$a += $o;
			}
			$a = ($a%360)+360;
			$b = ($b%360)+360;
			if ($a > $b)
				$b +=360;
			$b = $b/360*2*M_PI;
			$a = $a/360*2*M_PI;
			$d = $b-$a;
			if ($d == 0 )
				$d =2*M_PI;
			$k = $this->k;
			$hp = $this->h;
			if($style=='F')
				$op='f';
			elseif($style=='FD' or $style=='DF')
				$op='b';
			else
				$op='s';
			if (sin($d/2))
				$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
			//first put the center
			$this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
			//put the first point
			$this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
			//draw the arc
			if ($d < M_PI/2){
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
			}else{
				$b = $a + $d/4;
				$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
				$a = $b;
				$b = $a + $d/4;
				$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
							$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
							$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
							$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
							$xc+$r*cos($b),
							$yc-$r*sin($b)
							);
			}
			//terminate drawing
			$this->_out($op);
		}

		function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
		{
			$h = $this->h;
			$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
				$x1*$this->k,
				($h-$y1)*$this->k,
				$x2*$this->k,
				($h-$y2)*$this->k,
				$x3*$this->k,
				($h-$y3)*$this->k));
		}			
		
		function PieChart($w, $h, $data, $format, $colors=null)
		{
			$this->SetFont('Courier', '', 10);
			$this->SetLegends($data,$format);

			$XPage = $this->GetX();
			$YPage = $this->GetY();
			$margin = 2;
			$hLegend = 5;
			$radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
			$radius = floor($radius / 2);
			$XDiag = $XPage + $margin + $radius;
			$YDiag = $YPage + $margin + $radius;
			if($colors == null) {
				for($i = 0;$i < $this->NbVal; $i++) {
					$gray = $i * intval(255 / $this->NbVal);
					$colors[$i] = array($gray,$gray,$gray);
				}
			}

			//Sectors
			$this->SetLineWidth(0.2);
			$angleStart = 0;
			$angleEnd = 0;
			$i = 0;
			foreach($data as $val) {
				$angle = floor(($val * 360) / doubleval($this->sum));
				if ($angle != 0) {
					$angleEnd = $angleStart + $angle;
					$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
					$this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
					$angleStart += $angle;
				}
				$i++;
			}
			if ($angleEnd != 360) {
				$this->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
			}

			//Legends
			$this->SetFont('Courier', '', 10);
			$x1 = $XPage + 2 * $radius + 4 * $margin;
			$x2 = $x1 + $hLegend + $margin;
			$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
			for($i=0; $i<$this->NbVal; $i++) {
				$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
				$this->SetXY($x2,$y1);
				$this->Cell(0,$hLegend,$this->legends[$i]);
				$y1+=$hLegend + $margin;
			}
		}

		function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
		{
			$this->SetFont('Courier', '', 10);
			$this->SetLegends($data,$format);

			$XPage = $this->GetX();
			$YPage = $this->GetY();
			$margin = 2;
			$YDiag = $YPage + $margin;
			$hDiag = floor($h - $margin * 2);
			$XDiag = $XPage + $margin * 2 + $this->wLegend;
			$lDiag = floor($w - $margin * 3 - $this->wLegend);
			if($color == null)
				$color=array(155,155,155);
			if ($maxVal == 0) {
				$maxVal = max($data);
			}
			$valIndRepere = ceil($maxVal / $nbDiv);
			$maxVal = $valIndRepere * $nbDiv;
			$lRepere = floor($lDiag / $nbDiv);
			$lDiag = $lRepere * $nbDiv;
			$unit = $lDiag / $maxVal;
			$hBar = floor($hDiag / ($this->NbVal + 1));
			$hDiag = $hBar * ($this->NbVal + 1);
			$eBaton = floor($hBar * 80 / 100);

			$this->SetLineWidth(0.2);
			$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

			$this->SetFont('Courier', '', 10);
			$this->SetFillColor($color[0],$color[1],$color[2]);
			$i=0;
			foreach($data as $val) {
				//Bar
				$xval = $XDiag;
				$lval = (int)($val * $unit);
				$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
				$hval = $eBaton;
				$this->Rect($xval, $yval, $lval, $hval, 'DF');
				//Legend
				$this->SetXY(0, $yval);
				$this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
				$i++;
			}

			//Scales
			for ($i = 0; $i <= $nbDiv; $i++) {
				$xpos = $XDiag + $lRepere * $i;
				$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
				$val = $i * $valIndRepere;
				$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
				$ypos = $YDiag + $hDiag - $margin;
				$this->Text($xpos, $ypos, $val);
			}
		}

		function SetLegends($data, $format)
		{
			$this->legends=array();
			$this->wLegend=0;
			$this->sum=array_sum($data);
			$this->NbVal=count($data);
			foreach($data as $l=>$val)
			{
				$p=sprintf('%.2f',$val/$this->sum*100).'%';
				$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
				$this->legends[]=$legend;
				$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
			}
		}		
		
	}		

	$AR_MESES = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$nr_largura = 190;
	$nr_espaco = 4;
	$ds_tema = 'pastel';
	
	if((trim($_REQUEST['ano']) == "") or ($_REQUEST['ano'] < 2007))
	{
		echo "informe o ano";
		exit;
	}

	if((trim($_REQUEST['mes']) == "") or ($_REQUEST['mes'] < 1) or ($_REQUEST['mes'] > 12))
	{
		echo "informe o mês";
		exit;
	}
	
	$ANO_ATUAL    = $_REQUEST['ano'];
	$DT_ATUAL    = $_REQUEST['ano']."-".$_REQUEST['mes']."-01";
	$DT_ANTERIOR = ($_REQUEST['ano'] - 1).'-12-01';
	$dt_impressao = date('d')." de ".$AR_MESES[date('m') -1]." de ".date('Y');
	
	$ob_pdf = new PDF();

	$ob_pdf->novaPagina();
	################################################## CAPA ##################################################
	$ob_pdf->SetX(10);
	$ob_pdf->Image('img/logo_plano_7.png', 30, 20, ConvertSize(583,$ob_pdf->pgwidth), ConvertSize(242,$ob_pdf->pgwidth),'','',false);
	
	$ob_pdf->SetY(130);
	$ob_pdf->SetFont('Courier','B',22);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Relatório de Acompanhamento",0,"C");
	$ob_pdf->SetY($ob_pdf->GetY() + 15);
	$ob_pdf->MultiCell(190, 4, "Mês de referência ".$_REQUEST['mes']."/".$_REQUEST['ano'],0,"C");
	
	
	$ob_pdf->SetFont('Courier','',12);
	$ob_pdf->SetY(272);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, $dt_impressao, 0, "C");	

	################################################# TABELA ####################################################
	$ob_pdf->novaPagina();
	$qr_sql = "
				SELECT TO_CHAR(x.dt_mes,'YYYY-MM') AS dt_mes,
				       SUM(x.qt_ingresso) AS qt_ingresso,
				       SUM(x.qt_desligamento) AS qt_desligamento,
				       SUM(x.qt_participante) - SUM(x.qt_desligamento) AS qt_participante,
				       SUM(x.vl_patrimonio_liquido) AS vl_patrimonio_liquido,
                       SUM(x.vl_contribuicao_normal) AS vl_contribuicao_normal,
					   SUM(x.qt_paga) AS qt_paga,
					   SUM(x.qt_nao_paga) AS qt_nao_paga
				  FROM (
						-- DESLIGAMENTO
						SELECT (CASE WHEN t.dt_digita_desligamento IS NOT NULL THEN DATE_TRUNC('month',t.dt_digita_desligamento)
							    WHEN p.dt_obito IS NOT NULL THEN DATE_TRUNC('month',p.dt_obito) 
						       END)::DATE AS dt_mes,
						       0 AS qt_ingresso,
						       COUNT(*) AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,
                               0 AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga	       	       
						  FROM participantes p,
						       titulares t
						 WHERE p.cd_empresa            = t.cd_empresa
						   AND p.cd_registro_empregado = t.cd_registro_empregado
						   AND p.seq_dependencia       = t.seq_dependencia
						   AND p.cd_empresa = 7
						   AND p.cd_plano   = 0
						 GROUP BY dt_mes

						UNION

						-- INGRESSO
						SELECT DATE_TRUNC('month',t.dt_digita_ingresso)::DATE AS dt_mes,
						       COUNT(*) AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,
                               0 AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga
						  FROM participantes p,
						       titulares t
						 WHERE p.cd_empresa            = t.cd_empresa
						   AND p.cd_registro_empregado = t.cd_registro_empregado
						   AND p.seq_dependencia       = t.seq_dependencia
						   AND p.cd_empresa = 7
						 GROUP BY dt_mes

						UNION


						-- TOTAL PARTICIPANTES ATE O MES
						SELECT s.dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       COUNT(*) AS qt_participante,
						       0 AS vl_patrimonio_liquido,
                               0 AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga
						  FROM participantes p,
						       titulares t,
						       (SELECT dt_mes 
							  FROM funcoes.gera_mes(2005, TO_CHAR(CURRENT_DATE,'YYYY')::INTEGER) AS (dt_mes DATE)
							 WHERE dt_mes <= DATE_TRUNC('month',CURRENT_DATE)) s              
						 WHERE p.cd_empresa            = t.cd_empresa
						   AND p.cd_registro_empregado = t.cd_registro_empregado
						   AND p.seq_dependencia       = t.seq_dependencia
						   AND p.cd_empresa = 7
						   AND DATE_TRUNC('month',t.dt_digita_ingresso)::DATE <= s.dt_mes
						   AND (
							(DATE_TRUNC('month',t.dt_digita_desligamento)::DATE >= s.dt_mes OR t.dt_digita_desligamento IS NULL)
							AND 
							(DATE_TRUNC('month',p.dt_obito)::DATE >= s.dt_mes OR p.dt_obito IS NULL)
						       )
						 GROUP BY dt_mes 

						UNION

						--SENGE PATRIMONIO LIQUIDO
						SELECT l.dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       (SUM(l.vl_saldo_atual)*-1) AS vl_patrimonio_liquido,
						       0 AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga
						  FROM (SELECT DATE_TRUNC('month',cr.dt_ref_razao)::DATE AS dt_mes,
						               vl_saldo_atual
						          FROM public.ct_razao cr
						         WHERE cr.cd_empresa   = 17
						           AND cr.cd_plano     = 8
						           AND cr.cd_conta     = '2300000000000'
						         UNION 
						        SELECT DATE_TRUNC('month',cr.dt_ref_razao)::DATE AS dt_mes,
						               vl_saldo_atual
						          FROM public.ct_razao cr
						         WHERE cr.cd_empresa   = 17
						           AND cr.cd_plano     = 8
						           AND cr.cd_conta     = '2400000000000') l
						         GROUP BY l.dt_mes

						UNION

						-- TOTAL CONTRIBUIÇÃO NORMAL MÊS
						SELECT DATE_TRUNC('month',cr.dt_ref_razao)::DATE AS dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,						
						       ((cr.vl_debito + cr.vl_credito) * -1) AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga
						  FROM public.ct_razao cr
						 WHERE cr.cd_empresa   = 17
						   AND cr.cd_plano     = 8
						   AND cr.cd_conta     = '3112010101000'
                           AND TO_CHAR(cr.dt_ref_razao,'MM') <> '12'
						 UNION
						SELECT DATE_TRUNC('month',cr.dt_ref_razao)::DATE AS dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,						
						       ((cr.vl_debito + cr.vl_credito) * -1) AS vl_contribuicao_normal,
							   0 AS qt_paga,
							   0 AS qt_nao_paga
						  FROM public.ct_razao_anterior cr
						 WHERE cr.cd_empresa   = 17
						   AND cr.cd_plano     = 8
						   AND cr.cd_conta     = '3112010101000'
                           AND TO_CHAR(cr.dt_ref_razao,'MM') = '12'	

						UNION
						
						-- PAGA NO MÊS
						SELECT DATE_TRUNC('month',dt_lanc)::DATE AS dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,
						       0 AS vl_contribuicao_normal,
						       COUNT(*) AS qt_paga,
							   0 AS qt_nao_paga
						  FROM cf_contab
						 WHERE conta_ct     = '311201010100'
						   AND cod_emp_qt   = 17
						   AND cod_plano_qt = 8
						   AND deb_cred     = 'C'
						GROUP BY dt_mes
						
						UNION
						
						-- NÃO PAGA ATÉ O MÊS
						SELECT DATE_TRUNC('month',m.dt_lancamento)::DATE AS dt_mes,
						       0 AS qt_ingresso,
						       0 AS qt_desligamento,
						       0 AS qt_participante,
						       0 AS vl_patrimonio_liquido,
						       0 AS vl_contribuicao_normal,
							   0 AS qt_paga,
						       COUNT(*) AS qt_nao_paga
						  FROM cobrancas c,
						       (SELECT DISTINCT(dt_lancamento) AS dt_lancamento
						          FROM cobrancas 
						         WHERE cd_empresa        = 7
						           AND cd_plano          = 7
						           AND codigo_lancamento = 2400
						           AND ano_competencia   > 2006) m
						 WHERE c.cd_empresa        = 7
						   AND c.cd_plano          = 7
						   AND c.codigo_lancamento = 2400
						   AND c.valor_pago        = 0
						   AND c.dt_lancamento     = m.dt_lancamento
						   AND c.dt_lancamento     > TO_DATE(c.ano_competencia||'-'||c.mes_competencia||'-'||TO_CHAR(c.dt_lancamento,'DD'),'YYYY-MM-DD')
						 GROUP BY dt_mes						   
				 
					) x
			  WHERE x.dt_mes BETWEEN TO_DATE('".$ANO_ATUAL."-01-01','YYYY-MM-DD') AND TO_DATE('".$DT_ATUAL."','YYYY-MM-DD')
			  GROUP BY x.dt_mes
			  ORDER BY x.dt_mes
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	


	#### RODAPÉ ####
	$ob_pdf->SetFont('Courier','',8);
	$ob_pdf->SetY(272);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, $dt_impressao, 0, "C");
	
	#### CABEÇALHO ####
	$ob_pdf->SetY(20);
	$ob_pdf->SetFont('Courier','B',22);
	$ob_pdf->MultiCell(190, 4, "Mês de referência ".$_REQUEST['mes']."/".$_REQUEST['ano'],0,"C");		
	
	$ob_pdf->SetXY(15, $ob_pdf->GetY() + 10);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(74,80,25));
	$ob_pdf->SetAligns(array('C','C','C'));
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->Row(array("PARTICIPANTES", "CONTRIBUIÇÕES",'PATRIMÔNIO'));		
	
	$ob_pdf->SetX(15);
	$ob_pdf->SetWidths(array(12,15,20,27,25,30,25,25));
	$ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->Row(array("Mês", "Total", "Ingressos", "Desligamentos", "Normal", "Não pagas até o mês", "Média", "Líquido"));		
	$ob_pdf->SetFont('Courier','',10);	
	$ob_pdf->SetAligns(array('L','C','C','C','R','C','R','R'));
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		#### LINHAS ####
		$ob_pdf->SetX(15);
		$ar_tmp = explode("-",$ar_reg['dt_mes']);
		#### TRAZ SOMENTE ATÉ O MES CORRENTE ####
		if (!(($ar_tmp[0] == date('Y')) and ($ar_tmp[1] > (date('m')-1))))
		{
			$ob_pdf->Row(array(substr(trim($AR_MESES[($ar_tmp[1]-1)]),0,3), 
			                   $ar_reg['qt_participante'], 
							   $ar_reg['qt_ingresso'], 
							   $ar_reg['qt_desligamento'], 
							   formataNumero($ar_reg['vl_contribuicao_normal']), 
							   $ar_reg['qt_nao_paga'], 
							   formataNumero($ar_reg['vl_contribuicao_normal'] / $ar_reg['qt_paga']),
							   formataNumero($ar_reg['vl_patrimonio_liquido']))
						);		
		}
	}	
	
	###### DEMONSTRAÇÃO DE RESULTADOS ######
	$ob_pdf->SetXY(15,$ob_pdf->GetY() + 10);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(104,35,40));
	$ob_pdf->SetAligns(array('C','C','C'));
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->Row(array("DEMONSTRAÇÃO DE RESULTADOS", "EXERCÍCIO ATUAL",'EXERCÍCIO ANTERIOR'));	

	$ob_pdf->SetAligns(array('L','R','R'));	
	$vl_total_saldo_atual    = 0;
	$vl_total_saldo_anterior = 0;
	
	$ar_reg = getResultadoPolicentro('3100000000003', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg['vl_saldo_anterior'];
	if(($ar_reg['vl_saldo_atual'] != 0) or ($ar_reg['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(+) RECURSOS COLETADOS", formataNumero($ar_reg['vl_saldo_atual']), formataNumero($ar_reg['vl_saldo_anterior'])));	
	}

	$ar_reg = getResultadoPolicentro('3200000000000', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg['vl_saldo_anterior'];
	if(($ar_reg['vl_saldo_atual'] != 0) or ($ar_reg['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(-) BENEFÍCIOS", formataNumero($ar_reg['vl_saldo_atual']), formataNumero($ar_reg['vl_saldo_anterior'])));
	}
	
	$ar_reg = getResultadoPolicentro('6100000000000', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg['vl_saldo_anterior'];
	
	if(($ar_reg['vl_saldo_atual'] != 0) or ($ar_reg['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(+/-) RENDIMENTOS DAS APLICAÇÕES", formataNumero($ar_reg['vl_saldo_atual']), formataNumero($ar_reg['vl_saldo_anterior'])));
	}
	
	if(($vl_total_saldo_atual != 0) or ($vl_total_saldo_anterior != 0))
	{	
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(=) RECURSOS LÍQUIDOS", formataNumero($vl_total_saldo_atual), formataNumero($vl_total_saldo_anterior)));
	}


	$ar_reg1 = getResultadoPolicentro('5200000000000', $DT_ATUAL, $DT_ANTERIOR, true);
	$ar_reg2 = getResultadoPolicentro('5100000000004', $DT_ATUAL, $DT_ANTERIOR, true);
	if((($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual']) != 0) or (($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior']) != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(-) DESPESAS COM ADMINISTRAÇÃO", formataNumero($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual']), formataNumero($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior'])));
	}
	
	$ar_reg1 = getResultadoPolicentro('3300000000006', $DT_ATUAL, $DT_ANTERIOR, true);
	$ar_reg2 = getResultadoPolicentro('5300000000007', $DT_ATUAL, $DT_ANTERIOR, true);
	$ar_reg3 = getResultadoPolicentro('6300000000002', $DT_ATUAL, $DT_ANTERIOR, true);
	if((($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual'] + $ar_reg3['vl_saldo_atual']) != 0) or (($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior'] + $ar_reg3['vl_saldo_anterior']) != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(-/+) FORMAÇÃO (UTILIZAÇÃO) DE VALORES EM LITÍGIO", formataNumero($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual'] + $ar_reg3['vl_saldo_atual']), formataNumero($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior'] + $ar_reg3['vl_saldo_anterior'])));
	}
	
	$ar_reg = getResultadoPolicentro('3500000000009', $DT_ATUAL, $DT_ANTERIOR, true);
	if(($ar_reg['vl_saldo_atual'] != 0) or ($ar_reg['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(-/+) FORMAÇÃO (UTILIZAÇÃO) DOS COMPROMISSOS COM PARTICIPANTES E ASSISTIDOS", formataNumero($ar_reg['vl_saldo_atual']), formataNumero($ar_reg['vl_saldo_anterior'])));
	}
	
	$ar_reg1 = getResultadoPolicentro('3600000000005', $DT_ATUAL, $DT_ANTERIOR, true);
	$ar_reg2 = getResultadoPolicentro('5600000000006', $DT_ATUAL, $DT_ANTERIOR, true);
	$ar_reg3 = getResultadoPolicentro('6600000000001', $DT_ATUAL, $DT_ANTERIOR, true);
	if((($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual'] + $ar_reg3['vl_saldo_atual']) != 0) or (($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior'] + $ar_reg3['vl_saldo_anterior']) != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("(-/+) FORMAÇÃO (UTILIZAÇÃO) DE FUNDOS PARA RISCOS FUTUROS", formataNumero($ar_reg1['vl_saldo_atual'] + $ar_reg2['vl_saldo_atual'] + $ar_reg3['vl_saldo_atual']), formataNumero($ar_reg1['vl_saldo_anterior'] + $ar_reg2['vl_saldo_anterior'] + $ar_reg3['vl_saldo_anterior'])));
	}
	
	###### DEMONSTRAÇÃO PATRIMONIAL ######
	$ob_pdf->SetXY(15,$ob_pdf->GetY() + 10);
	$ob_pdf->SetLineWidth(0);
	$ob_pdf->SetDrawColor(0,0,0);
	$ob_pdf->SetWidths(array(104,35,40));
	$ob_pdf->SetAligns(array('C','C','C'));
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->Row(array("DEMONSTRAÇÃO PATRIMONIAL", "EXERCÍCIO ATUAL",'EXERCÍCIO ANTERIOR'));	
	
	#### ATIVO ####
	$vl_total_saldo_atual    = 0;
	$vl_total_saldo_anterior = 0;	
	$vl_aplica_saldo_atual    = 0;
	$vl_aplica_saldo_anterior = 0;
	
	$ar_reg_disponivel = getBalancoPolicentro('1100000000002', $DT_ATUAL, $DT_ANTERIOR);
	$vl_total_saldo_atual   += $ar_reg_disponivel['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_disponivel['vl_saldo_anterior'];	

	$ar_reg_conta_receber1 = getBalancoPolicentro('1210000000006', $DT_ATUAL, $DT_ANTERIOR);
	$ar_reg_conta_receber2 = getBalancoPolicentro('1230000000000', $DT_ATUAL, $DT_ANTERIOR);	
	$vl_total_saldo_atual   += ($ar_reg_conta_receber1['vl_saldo_atual'] + $ar_reg_conta_receber2['vl_saldo_atual']);
	$vl_total_saldo_anterior+= ($ar_reg_conta_receber1['vl_saldo_anterior'] + $ar_reg_conta_receber2['vl_saldo_anterior']);		
	
	$ar_reg_renda_fixa = getBalancoPolicentro('1241000000006', $DT_ATUAL, $DT_ANTERIOR);
	$vl_total_saldo_atual   += $ar_reg_renda_fixa['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_renda_fixa['vl_saldo_anterior'];	
	$vl_aplica_saldo_atual   += $ar_reg_renda_fixa['vl_saldo_atual'];
	$vl_aplica_saldo_anterior+= $ar_reg_renda_fixa['vl_saldo_anterior'];		
	
	$ar_reg_renda_variavel = getBalancoPolicentro('1242000000004', $DT_ATUAL, $DT_ANTERIOR);
	$vl_total_saldo_atual   += $ar_reg_renda_variavel['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_renda_variavel['vl_saldo_anterior'];	
	$vl_aplica_saldo_atual   += $ar_reg_renda_variavel['vl_saldo_atual'];
	$vl_aplica_saldo_anterior+= $ar_reg_renda_variavel['vl_saldo_anterior'];	

	$ar_reg_imovel = getBalancoPolicentro('1243000000002', $DT_ATUAL, $DT_ANTERIOR);
	$vl_total_saldo_atual   += $ar_reg_imovel['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_imovel['vl_saldo_anterior'];	
	$vl_aplica_saldo_atual   += $ar_reg_imovel['vl_saldo_atual'];
	$vl_aplica_saldo_anterior+= $ar_reg_imovel['vl_saldo_anterior'];

	$ar_reg_emp = getBalancoPolicentro('1244000000000', $DT_ATUAL, $DT_ANTERIOR);
	$vl_total_saldo_atual   += $ar_reg_emp['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_emp['vl_saldo_anterior'];	
	$vl_aplica_saldo_atual   += $ar_reg_emp['vl_saldo_atual'];
	$vl_aplica_saldo_anterior+= $ar_reg_emp['vl_saldo_anterior'];	
	
	$ob_pdf->SetAligns(array('L','R','R'));	
	
	if(($vl_total_saldo_atual != 0) or ($vl_total_saldo_anterior != 0))
	{
	$ob_pdf->SetFont('Courier','B',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("ATIVO", formataNumero($vl_total_saldo_atual), formataNumero($vl_total_saldo_anterior)));
	}

	if(($ar_reg_disponivel['vl_saldo_atual'] != 0) or ($ar_reg_disponivel['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  DISPONÍVEL", formataNumero($ar_reg_disponivel['vl_saldo_atual']), formataNumero($ar_reg_disponivel['vl_saldo_anterior'])));
	}
	
	if((($ar_reg_conta_receber1['vl_saldo_atual'] + $ar_reg_conta_receber2['vl_saldo_atual']) != 0) 
	 or (($ar_reg_conta_receber1['vl_saldo_anterior'] + $ar_reg_conta_receber2['vl_saldo_anterior']) != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  CONTAS A RECEBER", formataNumero($ar_reg_conta_receber1['vl_saldo_atual'] + $ar_reg_conta_receber2['vl_saldo_atual']), formataNumero($ar_reg_conta_receber1['vl_saldo_anterior'] + $ar_reg_conta_receber2['vl_saldo_anterior'])));
	}
	
	if(($vl_aplica_saldo_atual != 0) or ($vl_aplica_saldo_anterior != 0))
	{	
	$ob_pdf->SetFont('Courier','B',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  APLICAÇÕES", formataNumero($vl_aplica_saldo_atual), formataNumero($vl_aplica_saldo_anterior))); 
	}
	
	if(($ar_reg_renda_fixa['vl_saldo_atual'] != 0) or ($ar_reg_renda_fixa['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("    Renda Fixa", formataNumero($ar_reg_renda_fixa['vl_saldo_atual']), formataNumero($ar_reg_renda_fixa['vl_saldo_anterior'])));
	}
	
	if(($ar_reg_renda_variavel['vl_saldo_atual'] != 0) or ($ar_reg_renda_variavel['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("    Renda Variável", formataNumero($ar_reg_renda_variavel['vl_saldo_atual']), formataNumero($ar_reg_renda_variavel['vl_saldo_anterior'])));
	}
	
	if(($ar_reg_imovel['vl_saldo_atual'] != 0) or ($ar_reg_imovel['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("    Imóveis", formataNumero($ar_reg_imovel['vl_saldo_atual']), formataNumero($ar_reg_imovel['vl_saldo_anterior'])));
	}

	if(($ar_reg_emp['vl_saldo_atual'] != 0) or ($ar_reg_emp['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("    Empréstimos/Financiamentos", formataNumero($ar_reg_emp['vl_saldo_atual']), formataNumero($ar_reg_emp['vl_saldo_anterior'])));
	}
	
	#### PASSIVO ####
	$vl_total_saldo_atual    = 0;
	$vl_total_saldo_anterior = 0;	
	
	$ar_reg_conta = getBalancoPolicentro('2100000000008', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_conta['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_conta['vl_saldo_anterior'];		

	$ar_reg_litigio = getBalancoPolicentro('2200000000004', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_litigio['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_litigio['vl_saldo_anterior'];	
	
	$ar_reg_compromisso = getBalancoPolicentro('2300000000000', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_compromisso['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_compromisso['vl_saldo_anterior'];	

	$ar_reg_fundo = getBalancoPolicentro('2420000000001', $DT_ATUAL, $DT_ANTERIOR, true);	
	$vl_total_saldo_atual   += $ar_reg_fundo['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_fundo['vl_saldo_anterior'];
	
	$ar_reg_equilibrio = getBalancoPolicentro('2410000000004', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_equilibrio['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_equilibrio['vl_saldo_anterior'];

	$ar_reg_realizado = getBalancoPolicentro('2411000000002', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_realizado['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_realizado['vl_saldo_anterior'];

	$ar_reg_superavit = getBalancoPolicentro('2411010000005', $DT_ATUAL, $DT_ANTERIOR, true);
	$vl_total_saldo_atual   += $ar_reg_superavit['vl_saldo_atual'];
	$vl_total_saldo_anterior+= $ar_reg_superavit['vl_saldo_anterior'];	
	
	if(($vl_total_saldo_atual != 0) or ($vl_total_saldo_anterior != 0))
	{	
	$ob_pdf->SetFont('Courier','B',9);
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("PASSIVO", formataNumero($vl_total_saldo_atual), formataNumero($vl_total_saldo_anterior)));	
	}

	if(($ar_reg_conta['vl_saldo_atual'] != 0) or ($ar_reg_conta['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  CONTAS A PAGAR", formataNumero($ar_reg_conta['vl_saldo_atual']), formataNumero($ar_reg_conta['vl_saldo_anterior'])));
	}
	
	if(($ar_reg_litigio['vl_saldo_atual'] != 0) or ($ar_reg_litigio['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);	
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  VALORES EM LITÍGIO", formataNumero($ar_reg_litigio['vl_saldo_atual']), formataNumero($ar_reg_litigio['vl_saldo_anterior'])));	
	}
	
	if(($ar_reg_compromisso['vl_saldo_atual'] != 0) or ($ar_reg_compromisso['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  COMPROMISSOS COM PARTICIPANTES E ASSISTIDOS", formataNumero($ar_reg_compromisso['vl_saldo_atual']), formataNumero($ar_reg_compromisso['vl_saldo_anterior'])));	
	}
	
	if(($ar_reg_fundo['vl_saldo_atual'] != 0) or ($ar_reg_fundo['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  FUNDOS", formataNumero($ar_reg_fundo['vl_saldo_atual']), formataNumero($ar_reg_fundo['vl_saldo_anterior'])));	
	}
	
	if(($ar_reg_equilibrio['vl_saldo_atual'] != 0) or ($ar_reg_equilibrio['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("  EQUILIBRIO TÉCNICO", formataNumero($ar_reg_equilibrio['vl_saldo_atual']), formataNumero($ar_reg_equilibrio['vl_saldo_anterior'])));	
	}
	
	if(($ar_reg_realizado['vl_saldo_atual'] != 0) or ($ar_reg_realizado['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("    Resultados Realizados", formataNumero($ar_reg_realizado['vl_saldo_atual']), formataNumero($ar_reg_realizado['vl_saldo_anterior'])));
	}
	
	if(($ar_reg_superavit['vl_saldo_atual'] != 0) or ($ar_reg_superavit['vl_saldo_anterior'] != 0))
	{	
	$ob_pdf->SetFont('Courier','',9);		
	$ob_pdf->SetX(15);
	$ob_pdf->Row(array("      Superávit Técnico Acumulado", formataNumero($ar_reg_superavit['vl_saldo_atual']), formataNumero($ar_reg_superavit['vl_saldo_anterior'])));
	}
	
	
	################################################# GRAFICOS ####################################################
	$ob_pdf->novaPagina();
	#### RODAPÉ ####
	$ob_pdf->SetFont('Courier','',8);
	$ob_pdf->SetY(272);
	$ob_pdf->MultiCell($nr_largura, $nr_espaco, $dt_impressao, 0, "C");	

	$fl_gera_arquivo = true;
	include("senge_rel_acompanhamento_grafico_cotas.php");
	$ob_pdf->Image($arquivo, 20, 30, ConvertSize(650,$ob_pdf->pgwidth), ConvertSize(400,$ob_pdf->pgwidth),'','',false);
	$ob_pdf->SetY(ConvertSize(400,$ob_pdf->pgwidth) + 50);	

	$fl_gera_arquivo = true;
	include("senge_rel_acompanhamento_grafico_cotas_indicadores.php");
	$ob_pdf->Image($arquivo, 20, $ob_pdf->GetY(), ConvertSize(650,$ob_pdf->pgwidth), ConvertSize(400,$ob_pdf->pgwidth),'','',false);
	$ob_pdf->SetY(ConvertSize(400,$ob_pdf->pgwidth) + 50);		

	

	if($_REQUEST['fl_gera_pdf'] == "S")
	{
		$ob_pdf->Output('/u/www/upload/senge_relatorios/rel_acompanhamento_'.$_REQUEST['ano']."-".$_REQUEST['mes'].'.pdf');
	}
	else
	{
		$ob_pdf->Output();
	}
	

	
?>