<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	require_once('inc/fpdf153/fpdf.php');
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');

	class PDF extends FPDF
	{
		var $widths;
		var $aligns;

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
		    $nr_h = 6;
			//Calculate the height of the row
		    $nb=0;
		    for($i=0;$i<count($data);$i++)
		        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		    $h=$nr_h *$nb;
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
		        $this->Rect($x,$y,$w,$h);
		        //Print the text
		        $this->MultiCell($w,$nr_h ,$data[$i],0,$a,1);
		        //Put the position to the right of the cell
		        $this->SetXY($x+$w,$y);
		    }
		    //Go to the next line
		    $this->Ln($h);
		}

		function CheckPageBreak($h)
		{
		    //If the height h would cause an overflow, add a new page immediately
		    if($this->GetY()+$h>$this->PageBreakTrigger)
		        $this->AddPage($this->CurOrientation);
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
	                if(preg_match('/^.+,/', $dash))
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
			$this->SetXY(5,-15);
			$this->SetFont('Courier','I',6);
			$this->Cell(0,10,date('d/m/Y G:i:s')." [WEB]",0,0,'L');
			
			$this->SetX(-10);
			$this->SetFont('Courier','I',6);
			$this->Cell(0,10,$this->PageNo(),0,0,'L');	
		}		
		
		function ConvertSize($size=5,$maxsize=0)
		{
			// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
			//Identify size (remember: we are using 'mm' units here)
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
			else 
			{
				$size *= 0.2645; //nothing == px
			}

			return $size;
		}		
	}
		
	
	#### LOG ####
	$sql = "
			INSERT INTO public.log_acessos_usuario 
			     (
				   sid,
				   hora,
				   pagina
				 ) 
			VALUES           
			     (
				   ".$_SESSION['SID'].", 
				   now(),
				   'CONTRA_CHEQUE'
				 )";
	@pg_query($db,$sql);  	
	

	$tabela_1 = '
<style>
	@media print {
		.logo_contra_cheque { display:block; }
		
		.sort-table table { border: 0px; }
		.sort-table tr { border: 0px; }
		.sort-table td { border: 0px; }
	}
	
	@media screen {
		.logo_contra_cheque { display:none; }
	}
	
</style>	
<script type="text/javascript" src="inc/sort_table/sortabletable.js"></script>

<table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
	<thead>
		<tr>
			<td colspan="2" style="background: #4E7A4E; color:white;">
				<b>Contracheque - {dt_mes}</b>
			</td>
		</tr>
	</thead>	
	<tbody>	
		<tr>
			<td colspan="2">
				<img src="img/logo_contra_cheque.jpg" border="0" class="logo_contra_cheque">
			</td>	
		</tr>		
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>CNPJ:</b></td><td>90.884.412/0001-24</td></tr>		
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Empresa:</b></td><td>{empresa}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>RE.d/seq:</b></td><td>{re}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Participante:</b></td><td>{participante}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Endereço:</b></td><td>{endereco}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Tipo Folha:</b></td><td>{cd_tipo_folha} - {desc_folha}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Valor Benefício:</b></td><td>{vl_valor_beneficio_cc}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Mês/Ano:</b></td><td>{dt_mes}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Banco:</b></td><td>{cd_banco}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Agência:</b></td><td>{cd_agencia}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Conta:</b></td><td>{nr_conta}</td></tr>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);"><td valign="top"><b>Data de Pagamento:</b></td><td><b>{dt_pagamento}</b></td></tr>
	</tbody>	
</table>
	
';


	$tabela_2 = '
<table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
	<thead>
		<tr>
			<td style="background: #4E7A4E; color:white;">Cód</td>
			<td style="background: #4E7A4E; color:white;">Descrição</td>
			<td style="background: #4E7A4E; color:white;">Ref</td>
			<td style="background: #4E7A4E; color:white;">Valor</td>
		</tr>
	</thead>	
	<tbody>	


	

				';
	$periodos_disponiveis = '<p>Períodos disponíveis:</p>{periodos}';
	

	$folha = 0;  
	$qr_sql = "
				  SELECT tipo_folha AS folha 
					FROM public.participantes 
				   WHERE cd_empresa            = ".$_SESSION['EMP']."
					 AND cd_registro_empregado = ".$_SESSION['RE']."
					 AND seq_dependencia       = ".$_SESSION['SEQ']."
					 AND tipo_folha            NOT IN (0,1,11,12,13,16)
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	if(pg_num_rows($ob_resul) > 0)
	{
		$reg_part = pg_fetch_array($ob_resul);
		$folha = intval($reg_part['folha']);
	}
	
	if (intval($folha) == 0) 
	{
		$qr_sql = "
					SELECT ff.valor,
						   ff.ano_competencia,
						   ff.mes_competencia,
						   ff.tipo_folha AS folha
					  FROM public.fichas_financeiras ff
					 WHERE ff.verba = (SELECT DISTINCT v.verba
									     FROM public.verbas v
									    WHERE v.id_totalizador = 'L'
									      AND v.tipo_folha IN (2, 3, 4, 5, 9, 14, 15, 20, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85))
					   AND ff.cd_empresa            = ".$_SESSION['EMP']."
					   AND ff.cd_registro_empregado = ".$_SESSION['RE']."
					   AND ff.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND (
								CAST(DATE_TRUNC('month', ff.dt_pagamento) AS DATE) = CAST(DATE_TRUNC('month', CURRENT_DATE) AS DATE) -- MES ATUAL
								OR
								CAST(DATE_TRUNC('month', ff.dt_pagamento) AS DATE) = (CAST(DATE_TRUNC('month', CURRENT_DATE) AS DATE)  - '1 month'::INTERVAL) -- MES ANTERIOR
					       )
					   AND ff.tipo_folha IN (2, 3, 4, 5, 9, 14, 15, 20, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85)				   				   
				  ";
		$ob_resul = pg_query($db, $qr_sql);
		$reg_part = pg_fetch_array($ob_resul);
		$folha = intval($reg_part['folha']);
	}
	
	#### VERIFICA RECEBIMENTO RISCO INVALIDEZ #####
	if((in_array($_SESSION['EMP'],array(7,8,10))) and (in_array($folha,array(17,18))))
	{
		$qr_sql = "
					SELECT COUNT(*) AS fl_risco
					  FROM public.afastados a
					 WHERE a.cd_empresa            = ".$_SESSION['EMP']."
					   AND a.cd_registro_empregado = ".$_SESSION['RE']."
					   AND a.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND a.tipo_afastamento      = 96
					   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE))) 		
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$reg_afa = pg_fetch_array($ob_resul);	
		
		if(intval($reg_afa["fl_risco"]) > 0)
		{
			$folha = 65;
		}
	}
	
	
	#ECHO "<!-- F  => ".$folha." -->";
// ------------------------------------------------------------------------------
	$ar_folha = array(2, 3, 4, 5, 9, 14, 15, 20, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85);
	if(!in_array($folha,$ar_folha))
	{
		
		$conteudo = "
						<br><br><br>
						<center>
							<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
								Somente ASSISTIDOS podem consultar o contracheque.
							</h1>
						</center>
						<br><br><br>
					";
	} 
	else 
	{
		#### MONTA CONTRACHEQUE ####
		$qr_sel = " 
					SELECT fifi.cd_empresa, 
					       patr.nome_empresa, 
					       fifi.cd_registro_empregado, 
					       fifi.seq_dependencia, 
					       UPPER(part.nome) AS nome, 
					       funcoes.remove_acento(UPPER(part.logradouro)) AS logradouro, 
						   part.endereco,
						   part.nr_endereco,
						   part.complemento_endereco,
						   part.bairro, 
						   part.cidade, 
						   part.unidade_federativa AS uf, 						   
					       TO_CHAR(part.cep,'FM00000') || '-' || TO_CHAR(part.complemento_cep,'FM000') AS cep,	
						   part.cd_instituicao,
						   part.cd_agencia,
						   part.conta_folha,						   
					       fifi.tipo_folha, 
					       tifo.Descricao_Folha, 
					       fifi.dt_pagamento, 
						   (SELECT TO_CHAR(pf.data_pagamento,'DD/MM/YYYY')
						      FROM public.periodos_folha pf
						     WHERE CASE WHEN TO_CHAR(pf.data_ficha_financeira,'DD/MM') = '20/12' 
						                THEN pf.tipo = 'B' -- ABONO
						                ELSE pf.tipo = 'M' -- MENSAL
						           END
							   AND pf.data_ficha_financeira = fifi.dt_pagamento
						       AND pf.mes             = fifi.mes_competencia
						       AND pf.ano             = fifi.ano_competencia
						       AND pf.tifo_tipo_folha = fifi.tipo_folha) AS data,

						   
						   (SELECT TO_CHAR(pf.data_pagamento,'YYYY-MM-') ||  (CASE WHEN TO_CHAR(pf.data_ficha_financeira,'DD/MM') = '20/12' 
						                THEN 'B' -- ABONO
						                WHEN TO_CHAR(pf.data_ficha_financeira,'MM/YYYY') = '03/2019' OR TO_CHAR(pf.data_ficha_financeira,'MM/YYYY') = '04/2019'
						                THEN (
						                	CASE WHEN part.cd_plano IN (1, 2, 6)
						                	     THEN 'M'
						                	     ELSE 'MI'
						                	END)
						                WHEN TO_CHAR(pf.data_ficha_financeira,'MM/YYYY') = '07/2021' 
						                THEN (
						                	CASE WHEN part.cd_plano = 1 AND part.cd_empresa = 3
						                	     THEN 'M_CGTEE'
						                	     ELSE 'M'
						                	END)
						                ELSE 'M' -- MENSAL
						           END)
						      FROM public.periodos_folha pf
						     WHERE pf.data_ficha_financeira = fifi.dt_pagamento
						       AND pf.mes             = fifi.mes_competencia
						       AND pf.ano             = fifi.ano_competencia
						       AND pf.tifo_tipo_folha = fifi.tipo_folha) AS imagem_cc,
							   
					       fifi.Verba, 
						   CASE WHEN fifi.verba IN(525,664,665,526,552) --VERBAS EMPRESTIMO
						        THEN verb.descricao || ' ' || fifi.referencia
						        ELSE verb.descricao
						   END AS descricao,
						   
					       TO_CHAR(fifi.mes_competencia,'FM00') || '/' || TO_CHAR(fifi.ano_competencia,'FM0000') AS dt_mes,
					       fifi.Ano_Competencia, 
					       fifi.Mes_Competencia, 
					       fifi.Id_Acerto, 
					       fifi.Valor, 
					       verb.Tipo_verba,
						   part.cd_plano
					  FROM public.fichas_financeiras fifi, 
					       public.tipo_folhas tifo, 
					       public.patrocinadoras patr, 
					       public.participantes part, 
					       --public.verbas verb 
						   oracle.descricoes_verba_vw verb
					 WHERE tifo.tipo_folha            = fifi.tipo_folha 
					   AND patr.Cd_Empresa            = fifi.Cd_Empresa
					   AND part.Cd_Registro_Empregado = fifi.Cd_Registro_Empregado
					   AND part.Seq_Dependencia       = fifi.Seq_Dependencia
					   AND part.Cd_Empresa            = fifi.Cd_Empresa
					   AND verb.tipo_folha            = fifi.tipo_folha 
					   AND verb.Verba                 = fifi.Verba 
					   AND fifi.tipo_folha            IN (2, 3, 4, 5, 9, 14, 15, 20, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85)
					   AND fifi.tipo_folha            = (CASE WHEN ".$folha." IN (9, 35, 70) THEN ".$folha." ELSE fifi.tipo_folha END)
					   AND tifo.tipo_pagamento        = 'A' 
					   AND fifi.Cd_Empresa            = ".$_SESSION['EMP']."
					   AND fifi.Cd_Registro_Empregado = ".$_SESSION['RE']."
					   AND fifi.Seq_Dependencia       = ".$_SESSION['SEQ']."
					   {WHERE_dt_pagamento}
					   AND ((verb.tipo_verba IN ('D','P')) OR (fifi.verba IN (400, 800, 801)))
					   AND (TO_CHAR(fifi.dt_pagamento,'DD'))::INTEGER IN (19,20,25,26,27,28,29,30)
				     ORDER BY  fifi.Verba					   
			      ";

		
		
		#eCHO "<!-- SQL => <PRE>";print_r(getDataContraCheque($folha)); echo "</PRE>-->";
		#exit;
		#ECHO "<!-- SQL => <PRE>".$qr_sel."</PRE>-->"; 
		#exit;

####################################################################################################
		
#### PERIODOS DISPONIVEL ####
		$periodo = "";
		$periodo.= "
					<form id='formContracheque' width='100%' method='post' action='auto_atendimento_contra_cheque.php'>
					<input type='hidden' name='cd_secao'  value='".$_REQUEST['cd_secao']."'>
					<input type='hidden' name='cd_artigo' value='".$_REQUEST['cd_artigo']."'>
					<input type='hidden' name='fl_gera_pdf' id='fl_gera_pdf' value='N'>
					
					<table border='0' cellspacing='3'> 
					<tr>
						<td class='texto1'>
							Selecione o Ano-Mês:
						</td>
						<td>
							<select name='dp' id='dp' style='width:150px;'>
								{PERIODO}
							</select>
						</td>
						<td>
							<input type='submit' value='Ok' class='botao' style='width:80px;'>
						</td>
					</tr>
					</table>
					</form>
					<hr>
		           ";
				   
		$periodo_mdl = "
							<option value='{VALOR}' {FL_SELECIONADO}>{ROTULO}</option>
		               ";
		$periodo_opt = "";
		
		if($_REQUEST['fl_gera_pdf'] != "S")
		#if(1 == 0)
		{		
			$qr_sql_2 = " 
						SELECT DISTINCT pd.dt_pagamento, 
							   TO_CHAR(pd.dt_pagamento,'YYYY-MM') AS dt_formatada
						  FROM (".$qr_sel.") pd
						 ORDER BY pd.dt_pagamento DESC
				   ";		
			$ar_data = getDataContraCheque($folha);
			
			#ECHO "<!-- PER 1 SQL => <PRE>".$qr_sql."</PRE>-->"; exit;
			
			if((date("d/m/Y") >= $ar_data['DT_LIBERA']) and (trim($ar_data['DT_LIBERA']) != ""))
			{
				$qr_sql_2 = str_replace("{WHERE_dt_pagamento}","AND dt_pagamento <= '".$ar_data['DT_PAGO']."'",$qr_sql_2);
			}
			elseif(trim($ar_data['DT_PAGO_ANT']) != "")
			{
				$qr_sql_2 = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento <= '".$ar_data['DT_PAGO_ANT']."'",$qr_sql_2);	
			}
			else
			{
				$qr_sql_2 = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento <= CURRENT_DATE",$qr_sql_2);	
			}		

			
			$ob_resul_2 = pg_query($db,$qr_sql_2);	
			#ECHO "<!-- PER 2 SQL => <PRE>".$qr_sql."</PRE>-->"; exit;

		
			if(pg_num_rows($ob_resul_2) == 0)
			{
				$conteudo = "
								<br><br><br>
								<center>
									<h1 style='font-family: Calibri, Arial; font-size: 15pt;'>
										Não há contracheque disponível.
									</h1>
								</center>
								<br><br><br>
							";		
			}
			
			$dt_pagamento = '';

			while ($reg = pg_fetch_array($ob_resul_2)) 
			{
				if(trim($dt_pagamento) == '')
				{
					$dt_pagamento = $reg['dt_pagamento'];
				}
				
				$periodo_tmp = $periodo_mdl;
				
				if($reg['dt_pagamento'] == $_REQUEST['dp'])
				{
					$periodo_tmp = str_replace('{FL_SELECIONADO}',"selected", $periodo_tmp);
				}
				else
				{
					$periodo_tmp = str_replace('{FL_SELECIONADO}',"", $periodo_tmp);
				}
				
				$periodo_tmp = str_replace('{VALOR}',$reg['dt_pagamento'], $periodo_tmp);
				$periodo_tmp = str_replace('{ROTULO}',$reg['dt_formatada'], $periodo_tmp);
				$periodo_opt.= $periodo_tmp;
			}					   
			
			$periodo = str_replace('{PERIODO}',$periodo_opt, $periodo);
		}	

		if(trim($_REQUEST['dp']) != "") 
		{
			$dt_pagamento = $_REQUEST['dp'];

			$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = '".$_REQUEST['dp']."'",$qr_sel);	
		}
		else
		{
			$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = '".trim($dt_pagamento)."'",$qr_sel);
			/*
			$ar_data = getDataContraCheque($folha);

			if($_SESSION['RE'] == 127060 AND $_SESSION['SEQ'] == 1 AND $_SESSION['EMP'] == 0)
			{
				$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = '2016-10-30'",$qr_sel);	
			}
			else if((date("d/m/Y") >= $ar_data['DT_LIBERA']) and (trim($ar_data['DT_LIBERA']) != ""))
			{
				$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = '".$ar_data['DT_PAGO']."'",$qr_sel);	
			}
			elseif(trim($ar_data['DT_PAGO_ANT']) != "")
			{
				$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = '".$ar_data['DT_PAGO_ANT']."'",$qr_sel);	
			}
			else
			{
				$qr_sql = str_replace("{WHERE_dt_pagamento}","AND fifi.dt_pagamento = CURRENT_DATE",$qr_sel);	
			}
			*/
			
		}
		
		
		
		
		#### MONTA DADOS CONTRACHEQUE ####

		$vl_verba_400 = 0; ### TOTAL DE PROVENTOS
		$vl_verba_800 = 0; ### TOTAL DE DESCONTOS
		$vl_verba_801 = 0; ### LIQUIDO A RECEBER	
		$ar_cabecalho = Array();
		$ar_provento  = Array();
		$ar_desconto  = Array();

		if(trim($dt_pagamento) != '')
		{	
			$qr_sql_pdf = $qr_sql; #### USADO PARA MONTAR VERSAO PDF

			$ob_resul  = pg_query($db,$qr_sql);
			
			while ($reg = pg_fetch_array($ob_resul)) 
			{
				if (intval($reg['verba']) == 400) ### TOTAL DE PROVENTOS
				{
					$vl_verba_400 = $reg['valor'];		

					if(count($ar_cabecalho) == 0) ### CABECALHO
					{
						$ar_cabecalho = $reg;
					}				
				} 			
				elseif (intval($reg['verba']) == 800) ### VERBA TOTAL DE DESCONTOS
				{
					$vl_verba_800 = $reg['valor'];
					
					if(count($ar_cabecalho) == 0) ### CABECALHO
					{
						$ar_cabecalho = $reg;
					}					
				} 			
				elseif (intval($reg['verba']) == 801) ### VERBA LIQUIDO A RECEBER
				{
					$vl_verba_801 = $reg['valor'];
					
					if(count($ar_cabecalho) == 0) ### CABECALHO
					{
						$ar_cabecalho = $reg;
					}					
				} 
				else 
				{
					if(strtoupper($reg['tipo_verba']) == "P") ### PROVENTOS
					{
						$ar_provento[] = $reg;
					}
					elseif(strtoupper($reg['tipo_verba']) == "D") ### DESCONTOS
					{
						$ar_desconto[] = $reg;
					}
				}
			}
		}

		

####################################################################################################
		if(count($ar_cabecalho) > 0)
		{
			#### BUSCA VALOR DO BENEFICIO ####
			$qr_ben = "
						SELECT oracle.pck_beneficio_fnc_busca_valor_beneficio(".$ar_cabecalho['cd_empresa'].",
																			  ".$ar_cabecalho['cd_registro_empregado'].",
																			  ".$ar_cabecalho['seq_dependencia'].",
																			  ".$ar_cabecalho['tipo_folha'].",
																			  TO_DATE('".$ar_cabecalho['data']."','DD/MM/YYYY')) AS vl_valor_beneficio_cc
					  ";
			$ob_resul_ben = @pg_query($db,$qr_ben);
			$ar_reg_ben = @pg_fetch_array($ob_resul_ben);	
			
			#### MONTA CABEÇALHO CONTRACHEQUE ####
			$tabela_1 = str_replace('{empresa}', $ar_cabecalho['cd_empresa']. " - " . $ar_cabecalho['nome_empresa'], $tabela_1);
			$tabela_1 = str_replace('{re}', $ar_cabecalho['cd_registro_empregado'].' / '.$ar_cabecalho['seq_dependencia'], $tabela_1);
			$tabela_1 = str_replace('{participante}', $ar_cabecalho['nome'], $tabela_1);
			$tabela_1 = str_replace('{endereco}',$ar_cabecalho['endereco'].", ".$ar_cabecalho['nr_endereco']." / ".$ar_cabecalho['complemento_endereco']." - ".$ar_cabecalho['bairro']."<BR>".$ar_cabecalho['cep']." - ".$ar_cabecalho['cidade']." - ".$ar_cabecalho['uf'], $tabela_1);			
			$tabela_1 = str_replace('{cd_banco}', $ar_cabecalho['cd_instituicao'], $tabela_1);
			$tabela_1 = str_replace('{cd_agencia}', $ar_cabecalho['cd_agencia'], $tabela_1);
			$tabela_1 = str_replace('{nr_conta}', $ar_cabecalho['conta_folha'], $tabela_1);
			$tabela_1 = str_replace('{dt_mes}', $ar_cabecalho['dt_mes'], $tabela_1);
			$tabela_1 = str_replace('{cd_tipo_folha}', $ar_cabecalho['tipo_folha'], $tabela_1);
			$tabela_1 = str_replace('{desc_folha}', $ar_cabecalho['descricao_folha'], $tabela_1);
			$tabela_1 = str_replace('{dt_pagamento}', $ar_cabecalho['data'], $tabela_1);
			$tabela_1 = str_replace('{vl_valor_beneficio_cc}', number_format($ar_reg_ben['vl_valor_beneficio_cc'],2,',','.'), $tabela_1);
			$conteudo = str_replace('{tabela_1}', $tabela_1, $conteudo);
			$v_ultimo_periodo = $ar_cabecalho['data'];
			
			#### BOLETIM CONTRACHEQUE ####
			$imagem_cc = $ar_cabecalho['imagem_cc'];
			
			if(($ar_cabecalho['dt_mes'] == "04/2013") and (in_array($ar_cabecalho['cd_empresa'], array(0,9))) and (in_array($ar_cabecalho['tipo_folha'],array(3,4,5,9,14,5))) and ($ar_cabecalho['cd_plano'] == 1))
			{
				#### GAMBIARRA PARA EXTRAORDINÁRIA ####
				$imagem_cc.= "_b";
			}	

		}

		#### PROVENTOS ####
		$tabela_2.=  '
					<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td></td>
						<td align="center"><b>PROVENTOS</b></td>
						<td></td>						
						<td></td>
					</tr>
				';		
		foreach($ar_provento as $ar_item)
		{
			$tabela_2.= '
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td>'.$ar_item['verba'].'</td>
							<td>'.$ar_item['descricao'].'</td>
							<td align="center">'.$ar_item['dt_mes'].'</td>
							<td align="right">'.number_format($ar_item['valor'],2,',','.').'</td>
						</tr>
					    ';			
		}
		
		#### DESCONTOS ####
		$tabela_2.=  '
					<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td></td>
						<td align="center"><b>DESCONTOS</b></td>
						<td></td>
						<td></td>
					</tr>
				';
		foreach($ar_desconto as $ar_item)
		{
			$tabela_2.= '
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td>'.$ar_item['verba'].'</td>
							<td>'.$ar_item['descricao'].'</td>
							<td align="center">'.$ar_item['dt_mes'].'</td>
							<td align="right">'.number_format($ar_item['valor'],2,',','.').'</td>
						</tr>
					    ';			
		}		

		$tabela_2.= '
				</tbody>	
			</table>	
			<table width="580" class="sort-table" align="center" cellspacing="2" cellpadding="2">
				<thead>
				<tr>
					<td>Total de Proventos</td>
					<td>Total de Descontos</td>
					<td>Líquido a Receber</td>
				</tr>
				</thead>
				<tbody>
				<tr  onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td align="right" style="font-size: 105%;"><b>'.number_format($vl_verba_400,2,',','.').'</b></td>
					<td align="right" style="font-size: 105%;"><b>'.number_format($vl_verba_800,2,',','.').'</b></td>
					<td align="right" style="font-size: 125%;"><b>'.number_format($vl_verba_801,2,',','.').'</b></td>
				</tr>			
				</tbody>
			</table>
			<br>
			';

####################################################################################################	
			
			
		
		
		
		$botoes = "";
		$arq_img = 'documentos/boletim_contracheque/'.$imagem_cc.'.jpg';

		$arq_fundo = "";

		if(file_exists($arq_img))
		{
			if(trim($imagem_cc) == '2019-09-M')
			{
				$arq_fundo .= '<a href="https://www.novafundacao.com.br/" target="_blank">';
			}

			$arq_fundo .= '<img src="'.$arq_img.'" border="0" width="700">';

			if(trim($imagem_cc) == '2019-09-M')
			{
				$arq_fundo .= '</a>';
			}
		}
		
		$botoes.= '
					<div style="text-align:center;" class="nao_imprimir">
						<input type="button" value="Imprimir" class="botao" onclick="window.print();" style="width:120px;">
						<input type="button" value="Salvar em PDF" class="botao" onclick="geraPDFContracheque();" style="width:120px;">
						<script>
							function geraPDFContracheque()
							{
								$("#fl_gera_pdf").val("S");
								$("#formContracheque").attr("target", "_blank");
								$("#formContracheque").submit();
							
								$("#fl_gera_pdf").val("N");
								$("#formContracheque").attr("target", "_self");
							}
						</script>
					</div>
					<BR><BR>
					'.$arq_fundo.'
					<BR><BR>
					<div style="width: 100%; font-family: verdana; font-size: 9pt;" class="nao_imprimir">
						Para salvar em PDF é necessário o <a href="http://get.adobe.com/reader/" target="_blank" style="font-size: 9pt;">Adobe Acrobat Reader</a>, clique no icone para fazer download.
						<BR>
						<a href="http://get.adobe.com/reader/" target="_blank" alt="Download Adobe Acrobat Reader" title="Download Adobe Acrobat Reader"><img src="img/get_adobe_reader.png" border="0"></a>
					</div>
					<BR><BR>
  				  ';
				  
			  
		
		$conteudo = str_replace('{msg}',"", $conteudo);	
		$conteudo = str_replace('{tabela_1}', $tabela_1, $conteudo);	
		$conteudo = str_replace('{tabela_2}', $tabela_2.$botoes, $conteudo);
		$conteudo = str_replace('{periodos_disponiveis}', $periodo, $conteudo);		
	}

####################################################################################################	
	
	#### VERSÃO EM PDF ####
	if($_REQUEST['fl_gera_pdf'] == "S")
	{
		$ob_pdf = new PDF();
		$ob_pdf->AddPage();
	
		#### LOGO ####
		$ob_pdf->setX(10);
		$ob_pdf->Image('img/logo_contra_cheque.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(200,$ob_pdf->pgwidth), $ob_pdf->ConvertSize(44,$ob_pdf->pgwidth),'','',false);
	
		#### DADOS PARTICIPANTE ####
		$ob_pdf->SetXY(10, 25);
		$ob_pdf->SetFillColor(255, 255, 255); 
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(255, 255, 255);
		$ob_pdf->SetWidths(array(50,140));
		$ob_pdf->SetAligns(array('L','L'));
		$ob_pdf->SetFont('Courier','',12);
		$ob_pdf->SetTextColor(0,0,0);
		
		$ob_pdf->Row(array("CNPJ:", "90.884.412/0001-24"));
		$ob_pdf->Row(array("Empresa:", $ar_cabecalho['cd_empresa']. " - " . $ar_cabecalho['nome_empresa']));
		$ob_pdf->Row(array("RE.d/seq:", $ar_cabecalho['cd_registro_empregado'].' / '.$ar_cabecalho['seq_dependencia']));
		$ob_pdf->Row(array("Participante:", $ar_cabecalho['nome']));
		$ob_pdf->Row(array("Endereço:", $ar_cabecalho['endereco'].", ".$ar_cabecalho['nr_endereco']." / ".$ar_cabecalho['complemento_endereco']." - ".$ar_cabecalho['bairro']."\n".$ar_cabecalho['cep']." - ".$ar_cabecalho['cidade']." - ".$ar_cabecalho['uf']));
		$ob_pdf->Row(array("Tipo Folha:", $ar_cabecalho['tipo_folha']." - ".$ar_cabecalho['descricao_folha']));
		$ob_pdf->Row(array("Valor Benefício:", number_format($ar_reg_ben['vl_valor_beneficio_cc'],2,',','.')));
		$ob_pdf->Row(array("Mês/Ano:", $ar_cabecalho['dt_mes']));
		$ob_pdf->Row(array("Banco:", $ar_cabecalho['cd_instituicao']));
		$ob_pdf->Row(array("Agência:", $ar_cabecalho['cd_agencia']));
		$ob_pdf->Row(array("Conta:", $ar_cabecalho['conta_folha']));
		$ob_pdf->Row(array("Data de Pagamento:", $ar_cabecalho['data']));
		
	
		#### CABEÇALHO ####
		$ob_pdf->SetXY(15, $ob_pdf->GetY() + 8);
		#$ob_pdf->SetFillColor(0, 102, 51); 
		$ob_pdf->SetFillColor(150, 150, 150); 
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(180,180,180);
		$ob_pdf->SetWidths(array(20,100,30,30));
		$ob_pdf->SetAligns(array('C','C','C','C'));
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->SetTextColor(255,255,255);
		$ob_pdf->Row(array("Verba", "Descrição Verba", "Ref", "Valor"));
		$ob_pdf->SetTextColor(0,0,0);

		
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->SetFillColor(255,255,255); 
		$ob_pdf->SetAligns(array('C','C','C','C'));
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array("","PROVENTOS","",""));			
		foreach($ar_provento as $ar_item)
		{
			$ob_pdf->SetX(15);
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->SetFillColor(255,255,255); 
			$ob_pdf->SetAligns(array('C','L','C','R'));
			$ob_pdf->Row(
							array(
									$ar_item['verba'],
									$ar_item['descricao'],
									$ar_item['dt_mes'],
									number_format($ar_item['valor'],2,',','.')
								 )
						);		
		}
		
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->SetFillColor(255,255,255); 
		$ob_pdf->SetAligns(array('C','C','C','C'));
		$ob_pdf->SetX(15);
		$ob_pdf->Row(array("","DESCONTOS","",""));		
		foreach($ar_desconto as $ar_item)
		{
			$ob_pdf->SetX(15);
			$ob_pdf->SetFont('Courier','',12);
			$ob_pdf->SetFillColor(255,255,255); 
			$ob_pdf->SetAligns(array('C','L','C','R'));
			$ob_pdf->Row(
							array(
									$ar_item['verba'],
									$ar_item['descricao'],
									$ar_item['dt_mes'],
									number_format($ar_item['valor'],2,',','.')
								 )
						);		
		}		
		
		#### RODAPE 
		$ob_pdf->SetXY(15, $ob_pdf->GetY() + 4);
		$ob_pdf->SetFillColor(150, 150, 150); 
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(180,180,180);
		$ob_pdf->SetWidths(array(60,60,60));
		$ob_pdf->SetAligns(array('C','C','C'));
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->SetTextColor(255,255,255);
		$ob_pdf->Row(array("Total de proventos", "Total de descontos", "Líquido a receber"));		
		
		$ob_pdf->SetX(15);
		$ob_pdf->SetFillColor(255,255,255); 
		$ob_pdf->SetTextColor(0,0,0);
		$ob_pdf->Row(array(number_format($vl_verba_400,2,',','.'), number_format($vl_verba_800,2,',','.'), number_format($vl_verba_801,2,',','.')));	
		
		$ob_pdf->Output();
		exit;
	}
	
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
	pg_close($db);
	
	//--------------------------------------------------------------------------------------------------

	
	function getDataContraCheque($tp_folha)
	{
		global $db;

		#### MES ATUAL ####
		$qr_libera = "
						SELECT TO_CHAR((funcoes.dia_util('ANTES', (pf.data_pagamento - (CASE WHEN TO_CHAR(CURRENT_DATE,'DD')::INTEGER <= 19 AND TO_CHAR(CURRENT_DATE,'MM')::INTEGER = 12 THEN 1 ELSE 4 END)), 1)),'DD/MM/YYYY') AS dt_libera_old, 
						       TO_CHAR(pf.dt_consulta_contracheque,'DD/MM/YYYY') AS dt_libera,
							   pf.data_ficha_financeira AS dt_pagamento
						  FROM public.periodos_folha pf
						 WHERE pf.mes = TO_CHAR(CURRENT_DATE,'MM')::INTEGER
						   AND pf.ano = TO_CHAR(CURRENT_DATE, 'YYYY')::INTEGER
						   AND pf.tifo_tipo_folha = ".$tp_folha."
						   AND CASE WHEN TO_CHAR(CURRENT_DATE,'MM')::INTEGER = 12 
                                    AND CURRENT_DATE >= (SELECT pf1.dt_consulta_contracheque 
                                                           FROM public.periodos_folha pf1
                                                          WHERE pf1.tipo = 'B'
								                            AND pf1.mes  = pf.mes
                                                            AND pf1.ano  = pf.ano
                                                            AND pf1.tifo_tipo_folha = pf.tifo_tipo_folha)
                                    AND CURRENT_DATE < (SELECT pf1.dt_consulta_contracheque 
                                                          FROM public.periodos_folha pf1
                                                         WHERE pf1.tipo = 'M'
								                           AND pf1.mes  = pf.mes
                                                           AND pf1.ano  = pf.ano
                                                           AND pf1.tifo_tipo_folha = pf.tifo_tipo_folha)
                                    THEN pf.tipo = 'B'
                                    ELSE pf.tipo = 'M'
                               END					   
					 ";
		$ob_resul  = pg_query($db, $qr_libera);
		$ar_reg    = pg_fetch_array($ob_resul);
		$ar_data['DT_LIBERA'] = $ar_reg['dt_libera'];
		$ar_data['DT_PAGO']   = $ar_reg['dt_pagamento'];
		
		#### MES ANTERIOR ####
		$qr_libera = "
						SELECT TO_CHAR((funcoes.dia_util('ANTES', (data_pagamento - 4), 1)),'DD/MM/YYYY') AS dt_libera_old, 
							   TO_CHAR(dt_consulta_contracheque,'DD/MM/YYYY') AS dt_libera,
							   data_ficha_financeira AS dt_pagamento
						  FROM periodos_folha
						 WHERE mes = TO_CHAR(CURRENT_DATE - '1 months'::interval,'mm')::int
						   AND ano = TO_CHAR(CURRENT_DATE - '1 months'::interval, 'yyyy')::int
						   AND tifo_tipo_folha = ".$tp_folha."
						   AND tipo = 'M'
					 ";		
		$ob_resul  = pg_query($db, $qr_libera);
		$ar_reg    = pg_fetch_array($ob_resul);
		$ar_data['DT_LIBERA_ANT'] = $ar_reg['dt_libera'];
		$ar_data['DT_PAGO_ANT']   = $ar_reg['dt_pagamento'];	
	
		return $ar_data;
	}
	
	
	
	
?>