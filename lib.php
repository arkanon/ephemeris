<?php



    function adjust_in($lim, $val)	# se necessario, ajusta $val no intervalo [0,$lim) somando/subtraindo $lim
    {
      if ($val <     0) return $val + $lim;
      if ($val >= $lim) return $val - $lim;
      return $val;
    }



    function suntime($day, $month, $year, $lat10, $long10, $zenith, $rise, $precision)
    {

      /*

         Algoritmo do Nascimento/Ocaso do Sol
         <http://williams.best.vwh.net/sunrise_sunset_algorithm.htm>

         Traducao por Arkanon <arkanon@ceat.net>
         2009/04/22 (Qua) 10:02:12 (BRST)

         Fonte:
            Almanaque para Computadores, 1990
            publicado pelo Escritorio de Almanaque Nautico
            Observatorio Naval dos Estados Unidos
            Washington, DC 20392

         Entradas:
            $day, $month, $year: data do nascimento/ocaso
            $lat10, $long10:     coordenadas geograficas decimais para o nascimento/ocaso
            $zenith:             zenite do Sol para o nascimento/ocaso (crepusculo)

            NOTAS: a latitude/longitude eh positiva para o Norte/Leste e negativa para o Sul/Oeste

                   o algoritmo assume o uso de uma calculadora com funcoes trigonometricas em "graus"
                   (ao inves de "radianos"). Muitas linguagens de programacao assumem argumentos em
                   radiano, requerendo conversoes de ida e volta. O fator f eh 180/pi. Entao, por ex, a
                   equacao
                      RA = atan(0.91764 * tan(L))
                   deveria ser codificada como
                      RA = f*atan(0.91764 * tan(L/f))
                   para dar uma resposta em graus com a entrada L em graus.

       */

         $f = 180/pi();			# fator de conversao de radianos para graus

   #  1. calcular o dia do ano

   #  #  pelo algoritmo
   #     $N1 = floor( 275 * $month /  9 );
   #     $N2 = floor( ($month + 9) / 12 );
   #     $N3 = (1 + floor( ($year - 4 * floor( $year/4 ) + 2) / 3 ));
   #     $N  = $N1 - $N2*$N3 + $day - 30;

      #  em php
         $ts = mktime(0, 0, 0, $month, $day, $year);
         $N  = 1 + date('z', $ts);

   #  #  pelo unix
   #     $N  = `date +%j`;


   #  2. converter o valor da longitude para hora e calcular um tempo aproximado
         $longHour = $long10 / 15;
         $add      = $rise ? 0 : 12;	# se for desejada a hora do nascimento somar 0 senao somar 12
         $t        = $N + ((6 + $add - $longHour) / 24);


   #  3. calcular a anomalia media (mean anomaly, M) do Sol
         $M = (0.9856 * $t) - 3.289;


   #  4. calcular a longitude absoluta (true longitude, L) do Sol
         $L1 = $M + (1.916 * sin($M/$f)) + (0.020 * sin(2*$M/$f)) + 282.634;
         $L  = adjust_in(360, $L1);	# $L1 eh ajustado no intervalo [0,360)


   # 5a. calcular ascensao reta (right ascension, RA) do Sol
         $RA1 = $f * atan(0.91764 * tan($L/$f));
         $RA2 = adjust_in(360, $RA1);	# $RA1 eh ajustado no intervalo [0,360)


   # 5b. o valor da ascensao reta precisa estar no mesmo quadrante que a latitude absoluta ($L)
         $Lquadrant  = floor( $L  /90 ) * 90;
         $RAquadrant = floor( $RA2/90 ) * 90;
         $RA3        = $RA2 + ($Lquadrant - $RAquadrant);


   # 5c. o valor da ascensao reta precisa ser convertido em horas
         $RA = $RA3 / 15;


   #  6. calcular a declinacao do Sol
         $sinDec = 0.39782 * sin($L/$f);
         $cosDec = cos(asin($sinDec));


   # 7a. calcular o angulo da hora solar local (Sun's local hour angle)
         $cosH = (cos($zenith/$f) - ($sinDec * sin($lat10/$f))) / ($cosDec * cos($lat10/$f));
         if ($cosH < -1) return array( 0, "O Sol nunca se põe neste local<br>na data especificada." );
         if ($cosH >  1) return array( 0, "O Sol nunca nasce neste local<br>na data especificada." );


   # 7b. finalizar calculando $H (hora solar local) e converter em horas
         $H1 = $f*acos($cosH);		# se for desejada a hora do ocaso
         if ($rise)
            $H1 = 360 - $H1;		# se for desejada a hora do nascimento
         $H = $H1 / 15;


   #  8. calcular a hora local (local mean time) do nascimento/ocaso
         $T = $H + $RA - (0.06571 * $t) - 6.622;


   #  9. ajustar novamente para UTC (Coordinated Universal Time)
         $UT1 = $T - $longHour;
         $UT  = adjust_in(24, $UT1);	# $UT1 eh ajustado no intervalo [0,24)


   # 10. converter o valor de $UT para o fuso-horario local (local time zone) da latitude/longitude
         $localOffset = intval( $longHour );
         $localT      = $UT + $localOffset;


   # 11. converter a hora decimal (HH.DD) em sexagesimal (HH:MM:SS.DD)
         $dh = $localT;								# valor decimal da hora
         $ih = sprintf('%02d',intval($dh));					# valor inteiro da hora
         $dm = ($dh-$ih)*60;
         $im = sprintf('%02d',intval($dm));
         $ds = ($dm-$im)*60;
         $is = sprintf('%02d',intval($ds));
         if ($precision!=0) $dd = ltrim(number_format($ds-$is,$precision),0);	# parte decimal do segundo
         $localT60 = "$ih:$im:$is$dd";

      return array($localT, $localT60);

    }



?>
