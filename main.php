<?php

  # access counter
    include('simple-counter.php');
    $hits        = counted();
    $created     = "11/05/2009 Seg 20:20:52";
    $counted     = "13/05/2009 Qua 08:04:30";

    include('lib.php');

    $data         = include('data.php');
    $months       = $data['months'];
    $definitions  = $data['definitions'];
    $ephemeris    = $data['ephemeris'];

  # $day          = 22;
  # $month        = 4;
  # $year         = 2009;

    $title        = "Cálculo de Eventos Astronômicos";

    $now_ts       = time();

    $def_day      = date('j', $now_ts);
    $def_month    = date('n', $now_ts);
    $def_year     = date('Y', $now_ts);

    $day          = str_pad( isset($_POST['day'  ]) && ! isset($_POST['today']) ? $_POST['day'  ] : ( isset($_GET['day'  ]) ? $_GET['day'  ] : $def_day   ) ,2,0,STR_PAD_LEFT);
    $month        = str_pad( isset($_POST['month']) && ! isset($_POST['today']) ? $_POST['month'] : ( isset($_GET['month']) ? $_GET['month'] : $def_month ) ,2,0,STR_PAD_LEFT);
    $year         =          isset($_POST['year' ]) && ! isset($_POST['today']) ? $_POST['year' ] : ( isset($_GET['year' ]) ? $_GET['year' ] : $def_year  ) ;

    $lastday      = date('t', mktime(0, 0, 0, $month, 1, $year));

    foreach(array_keys($months) as $m)
    {
      $selected    = $m==$month ? " selected=\"selected\"" : "";
      $month_list .= "<option value=\"$m\"$selected>{$months[$m]}</option>\n";
    }

    for($d=1; $d<=$lastday; $d++)
    {
      $selected  = $d==$day || $d==$lastday && $day>=$lastday ? " selected=\"selected\"" : "";
      if ($day>$lastday) $day = $lastday;
      $padded_d  = str_pad($d,2,0,STR_PAD_LEFT);
      $day_list .= "<option value=\"$d\"$selected>$padded_d</option>\n";
    }

  # CEAT
    $def_addr     = "Rua Alberto Torres, 297, Lajeado, RS, Brasil";
    $def_lat10    = -29.46351; # -29.4635114818812  /  29° 27' 48" (0.64133477232") S
    $def_long10   = -51.96433; # -51.9643307290972  /  51° 57' 51" (0.59062474992") W
    $def_lat60    = "29° 27' 48.64\" S";
    $def_long60   = "51° 57' 51.59\" W";
  # $def_lat10    = -(29 + 27/60 + 48/60/60 + 0.64133477232);
  # $def_long10   = -(51 + 57/60 + 51/60/60 + 0.59062474992);
    $def_zenith10 = "";
    $def_zenith60 = "";

    $lat10        = isset($_POST['lat10'   ]) ? $_POST['lat10'   ] : ( isset($_GET['lat10'   ]) ? $_GET['lat10'   ] : $def_lat10    ) ;
    $long10       = isset($_POST['long10'  ]) ? $_POST['long10'  ] : ( isset($_GET['long10'  ]) ? $_GET['long10'  ] : $def_long10   ) ;
    $zenith10     = isset($_POST['zenith10']) ? $_POST['zenith10'] : ( isset($_GET['zenith10']) ? $_GET['zenith10'] : $def_zenith10 ) ;

    $lat60        = isset($_POST['lat60'   ]) ? $_POST['lat60'   ] : htmlspecialchars($def_lat60);
    $long60       = isset($_POST['long60'  ]) ? $_POST['long60'  ] : htmlspecialchars($def_long60);
    $zenith60     = isset($_POST['zenith60']) ? $_POST['zenith60'] : htmlspecialchars($def_zenith60);

    $zoom         = 18;               # 0..19
    $ctrl_size    = "large";
#   $ctrl_size    = "small";

  # $maptype      = "normal";         # mapa de ruas comum (default)
    $maptype      = "satellite";      # imagens de satélite
  # $maptype      = "hybrid";         # camada transparente das principais ruas nas imagens de satélite
  # $maptype      = "physical";       # mapas com características físicas como terreno e vegetação. Este tipo de mapa não é exibido dentro dos controles de tipo de mapa por padrão
  # $maptype      = "moon_elevation"; # mapa plano sombreado na superfície da Lua, com altitudes diferenciadas por cores. Este tipo de mapa não é exibido dentro dos controles de tipo de mapa por padrão
  # $maptype      = "moon_visible";   # fotografias tiradas da órbita da Lua. Este tipo de mapa não é exibido dentro dos controles de tipo de mapa por padrão
  # $maptype      = "mars_elevation"; # mapa em relevo sombreado na superfície de Marte, com altitudes diferenciadas por cores. Este tipo de mapa não é exibido dentro dos controles de tipo de mapa por padrão
  # $maptype      = "mars_visible";   # fotografias tiradas da órbita de Marte. Este tipo de mapa não é exibido dentro dos controles de tipo de mapa por padrão
  # $maptype      = "mars_infrared";  # mapa infravermelho sombreado na superfície de Marte, no qual as áreas quentes aparecem brilhantes e as áreas frias aparecem escuras
  # $maptype      = "sky_visible";    # mosaico do céu, abrangendo toda a esfera celeste

    $zeniths10    = array( 'p' => $zenith10 , 'o' => 90+50/60  , 'c' => 96    , 'n' => 102    , 'a' => 108    );
    $zeniths60    = array( 'p' => $zenith60 , 'o' => "90° 50'" , 'c' => "96°" , 'n' => "102°" , 'a' => "108°" );
    $rise         = array( 'r' => true, 's' => false, );

    $prec         = 0;

#   $gmapi_key    = "ABQIAAAA-BORWsCJ20Pw-hql1POcbRQvDi3NjDgQt-Dt7uX1yK-KFt-x-hRRcuHWEV1uN6eOj6XaCF5cSe2HhQ"; # www.ceat.net
    $gmapi_key    = "ABQIAAAA21rqgulyZCST-4ccFmPfBxQniU8S7LSA_A2MOczAakZ9cd0-UxSJG5iiKBi7_pYsZ4G4eR2z-yrZww"; # svl.lsd.org.br
    $width        = 500;
    $height       = 500;

    $ctrl_size    = ucfirst($ctrl_size);
    $maptype      = 'G_'.strtoupper($maptype).'_MAP';

#   $help         = "image/help.png";
    $help         = "image/bluetooth.png";

    foreach (array_keys($zeniths10) as $z)
    {
      foreach (array_keys($rise) as $r) $t[$z][$r] = suntime($day, $month, $year, $lat10, $long10, $zeniths10[$z], $rise[$r], $prec);
      $l[$z][0] = $t[$z]['s'][0] - $t[$z]['r'][0];
      $dh = $l[$z][0];
      $ih = sprintf('%02d',intval($dh));
      $dm = ($dh-$ih)*60;
      $im = sprintf('%02d',intval($dm));
      $ds = ($dm-$im)*60;
      $is = sprintf('%02d',intval($ds));
      if ($prec!=0) $dd = ltrim(number_format($ds-$is,$prec),0);
      $l[$z][1] = "$ih:$im:$is$dd";
    }


    foreach (array_keys($definitions) as $key)
    {
      $url = $definitions[$key][1]=='wp' ? "pt.wikipedia.org/wiki/".$definitions[$key][0] : $definitions[$key][1];
      $def_divs .= "<div id=\"$key\" class=\"popup tooltip\" onmouseover=\"ppopen('$key',0,event)\" onmouseout=\"ppclose('$key',false)\"><a href=\"http://$url\" target=\"_blank\" class=\"definicao\">{$definitions[$key][0]}</a>{$definitions[$key][2]}</div>\n";
    }


    session_start();
    $_SESSION['ctrl_size'] = $ctrl_size;
    $_SESSION['lat10'    ] = $lat10;
    $_SESSION['long10'   ] = $long10;
    $_SESSION['zoom'     ] = $zoom;
    $_SESSION['maptype'  ] = $maptype;


    print "
<html>

<head>
  <title>[Audax do Vale] $title</title>
  <script type=\"text/javascript\" src=\"http://maps.google.com/maps?file=api&v=2&key=$gmapi_key\"></script>
  <script type=\"text/javascript\" src=\"lib.js\"></script>
  <link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" media=\"screen\" />
  <style>
    html,body,input,select,td,th{ font-size:8pt }
  </style>
</head>

<body onload=\"load()\" onunload=\"GUnload()\">

<h1 style=\"margin:0\">$title</h1>

<div style=\"margin:0 0 20px 20px\">
  Arkanon &lt;arkanon@lsd.org.br&gt;<br />
  v1.0 - 2009/10/14 (Qua) 20:14:42 (BRS)
</div>

<div id=\"getcoord\" class=\"popup\">
  <form name=\"find_form\" method=\"post\" onmouseover=\"document.onkeypress=processKey(document.find_form.find)\" onsubmit=\"showAddress(this.address.value); return false\">
    <input type=\"text\" size=\"54\" name=\"address\" id=\"address\" value=\"\" onmouseover=\"ppopen('usage',5,event)\" onmouseout=\"ppclose('usage',false)\" />
    <input type=\"submit\" id=\"find\" value=\"Localizar\" />
    <input type=\"button\" onclick=\"ppclose('getcoord',true)\" value=\"Fechar\" />
  </form>
  <table>
  <tr>
    <td><b>Latitude</b></td>  <td id=\"lat\"></td>
    <td width=20></td>
    <td><b>Longitude</b></td> <td id=\"lng\"></td>
  </tr>
  </table>
  <div align=\"center\" id=\"map\" style=\"width:{$width}px; height:{$height}px\"><br/></div>
</div>

<div id=\"usage\" class=\"popup tooltip\">
  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr> <td rowspan=\"2\" valign=\"top\" style=\"padding-right:10px\"><b>Ex:</b></td> <td style=\"padding-right:10px; text-align:right;\"><i>(endereço)</i></td>  <td>$def_addr</td>               </tr>
  <tr>                                                                               <td style=\"padding-right:10px; text-align:right;\"><i>(lat, long)</i></td> <td>$def_lat10, $def_long10</td> </tr>
  </table>
</div>

<form name=\"input_form\" method=\"post\" onmouseover=\"document.onkeypress=processKey(document.input_form.exec)\">
  <table border=\"0\" cellpadding=\"0\" cellspacing=\"3\">
  <tr>
    <td>Data</td>
    <td colspan=\"3\" style=\"padding:0\">
      <input type=\"text\" name=\"year\" value=\"$year\" size=\"4\" maxlength=\"4\" tabindex=\"1\" style=\"width:2.6em\">
      <select name=\"month\" onchange=\"submit()\" tabindex=\"2\">\n$month_list</select>
      <select name=\"day\" tabindex=\"3\">\n$day_list</select>
      <input type=\"submit\" name=\"today\" value=\"Hoje\" tabindex=\"11\">
    </td>
  </tr>
  <tr> <td>Sistema</td>                                                                    <td style=\"padding:0\">Decimal</td>                                                                                                                  <td colspan=\"2\">Sexagesimal<img src=\"$help\" onclick=\"ppopen('sexagesimal' ,0,event)\" /></td>                                                                        </tr>
  <tr> <td>Latitude<img      src=\"$help\" onclick=\"ppopen('latitude' ,0,event)\" /></td> <td style=\"padding:0\"><input type=\"text\" name=\"lat10\"    value=\"$lat10\"    tabindex=\"4\" id=\"lat10\"    style=\"width:4.7em\" onclick=\"select_field(this,'lat60'   )\" onkeyup=\"document.getElementById('lat60'   ).value=coord10to60(this.value)\" /></td> <td><input type=\"text\" name=\"lat60\"    value=\"$lat60\"    tabindex=\"5\" id=\"lat60\"    style=\"width:7.5em\" onclick=\"select_field(this,'lat10'   )\" onkeyup=\"test60convertto10(event,'lat'   )\" class=\"unselected\" /></td> <td style=\"padding:0\" rowspan=\"2\"><img src=\"image/earth_api.gif\" onclick=\"ppopen('getcoord',0,event)\" alt=\"[Earth API]\"\" /></td> </tr>
  <tr> <td>Longitude<img     src=\"$help\" onclick=\"ppopen('longitude',0,event)\" /></td> <td style=\"padding:0\"><input type=\"text\" name=\"long10\"   value=\"$long10\"   tabindex=\"6\" id=\"long10\"   style=\"width:4.7em\" onclick=\"select_field(this,'long60'  )\" onkeyup=\"document.getElementById('long60'  ).value=coord10to60(this.value)\" /></td> <td><input type=\"text\" name=\"long60\"   value=\"$long60\"   tabindex=\"7\" id=\"long60\"   style=\"width:7.5em\" onclick=\"select_field(this,'long10'  )\" onkeyup=\"test60convertto10(event,'long'  )\" class=\"unselected\" /></td>                                         </tr>
  <tr> <td>Dist. Zenital<img src=\"$help\" onclick=\"ppopen('zenite'   ,0,event)\" /></td> <td style=\"padding:0\"><input type=\"text\" name=\"zenith10\" value=\"$zenith10\" tabindex=\"8\" id=\"zenith10\" style=\"width:4.7em\" onclick=\"select_field(this,'zenith60')\" onkeyup=\"document.getElementById('zenith60').value=coord10to60(this.value)\" /></td> <td><input type=\"text\" name=\"zenith60\" value=\"$zenith60\" tabindex=\"9\" id=\"zenith60\" style=\"width:7.5em\" onclick=\"select_field(this,'zenith10')\" onkeyup=\"test60convertto10(event,'zenith')\" class=\"unselected\" /></td> <td style=\"padding:0\">(opcional)</td> </tr>
  <tr> <td>Key Code</td> <td style=\"padding:0\" colspan=\"3\"><input type=\"text\" name=\"kc\" value=\"\" tabindex=\"12\" id=\"kc\" style=\"width:3em\" readonly=\"readonly\" /></td> </tr>
  <tr> <td align=\"center\" colspan=\"4\"><input type=\"submit\" name=\"exec\" value=\"Calcular\" tabindex=\"10\" style=\"width:29.3em\" /></td> </tr>
  </table>
</form>

<table border=\"0\" cellpadding=\"10\" cellspacing=\"10\" id=\"results\">
<tr class=\"rline\"> <td colspan=\"2\"><h2>Localização Espaço-Temporal</h2></td>
<tr>
  <td colspan=\"2\">

    <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">
    <tr> <th align=\"left\">Sistema</th>       <th>Decimal</th>   <th>Sexagesimal</th>  </tr>
    <tr> <th align=\"left\">Latitude</th>      <td>$lat10</td>    <td>$lat60</td>       </tr>
    <tr> <th align=\"left\">Longitude</th>     <td>$long10</td>   <td>$long60</td>      </tr>
    <tr> <th align=\"left\">Dist. Zenital</th> <td>$zenith10</td> <td>$zenith60</td>    </tr>
    <tr> <th align=\"left\">Data</th>          <td colspan=\"2\">$day/$month/$year</td> </tr>
    </table>

  </td>
</tr>
<tr class=\"rline\"> <td colspan=\"2\"><h2>Efemérides<img src=\"$help\" onclick=\"ppopen('efemerides',0,event)\" /> Solares</h2></td>
</tr>
<tr>
  <td align=\"right\">

    <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
    <tr> <th>Crepúsculo<img src=\"$help\" onclick=\"ppopen('crepusculo',0,event)\" /></th>                  <th colspan=\"2\">Dist. Zenital</th> <th>Nascimento</th>                         <th>Ocaso<img src=\"$help\" onclick=\"ppopen('ocaso',0,event)\" /></th> <th>Duração</th>      </tr>
    ".($zenith10==""?"":"<tr> <td></td>                                                                     <td>{$zenith10}°</td> <td></td>      <td align=\"center\">{$t['p']['r'][1]}</td> <td align=\"center\">{$t['p']['s'][1]}</td>                             <td>{$l['p'][1]}</td> </tr>")."
    <tr> <td align=\"left\">Oficial<img src=\"$help\" onclick=\"ppopen('oficial',0,event)\" /></td>         <td>90° 50'</td>      <td>0.83°</td> <td align=\"center\">{$t['o']['r'][1]}</td> <td align=\"center\">{$t['o']['s'][1]}</td>                             <td>{$l['o'][1]}</td> </tr>
    <tr> <td align=\"left\">Civil<img src=\"$help\" onclick=\"ppopen('civil',0,event)\" /></td>             <td>96°</td>          <td>6°</td>    <td align=\"center\">{$t['c']['r'][1]}</td> <td align=\"center\">{$t['c']['s'][1]}</td>                             <td>{$l['c'][1]}</td> </tr>
    <tr> <td align=\"left\">Náutico<img src=\"$help\" onclick=\"ppopen('nautico',0,event)\" /></td>         <td>102°</td>         <td>12°</td>   <td align=\"center\">{$t['n']['r'][1]}</td> <td align=\"center\">{$t['n']['s'][1]}</td>                             <td>{$l['n'][1]}</td> </tr>
    <tr> <td align=\"left\">Astronômico<img src=\"$help\" onclick=\"ppopen('astronomico',0,event)\" /></td> <td>108°</td>         <td>18°</td>   <td align=\"center\">{$t['a']['r'][1]}</td> <td align=\"center\">{$t['a']['s'][1]}</td>                             <td>{$l['a'][1]}</td> </tr>
    </table>

  </td>
  <td>

    <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
    <tr> <th align=\"left\">Ano</th>                                                                               <td colspan=2>$year</td>                       </tr>
    <tr> <th align=\"left\">Periélio<img src=\"$help\"  onclick=\"ppopen('perielio',0,event)\" /></th>             <td>01</td> <td>{$ephemeris[$year]['pe']}</td> </tr>
    <tr> <th align=\"left\">Equinócio<img src=\"$help\" onclick=\"ppopen('equinocio',0,event)\" /> de Outono</th>  <td>03</td> <td>{$ephemeris[$year]['e1']}</td> </tr>
    <tr> <th align=\"left\">Solstício<img src=\"$help\" onclick=\"ppopen('solsticio',0,event)\" /> de Inverno</th> <td>06</td> <td>{$ephemeris[$year]['s1']}</td> </tr>
    <tr> <th align=\"left\">Afélio<img src=\"$help\"    onclick=\"ppopen('afelio',0,event)\" /></th>               <td>07</td> <td>{$ephemeris[$year]['ae']}</td> </tr>
    <tr> <th align=\"left\">Equinócio de Primavera</th>                                                            <td>09</td> <td>{$ephemeris[$year]['e2']}</td> </tr>
    <tr> <th align=\"left\">Solstício de Verão</th>                                                                <td>12</td> <td>{$ephemeris[$year]['s2']}</td> </tr>
    </table>

  </td>
</tr>
<tr class=\"rline\"> <td colspan=\"2\"><h2>Efemérides Lunares</h2></td>
<tr>
  <td align=\"right\">

    <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
    <tr> <th align=\"left\">Nascimento</th>     <td></td> </tr>
    <tr> <th align=\"left\">Meio-Dia Lunar</th> <td></td> </tr>
    <tr> <th align=\"left\">Ocaso</th>          <td></td> </tr>
    <tr> <th align=\"left\">Duração</th>        <td></td> </tr>
    <tr> <th align=\"left\">Aparência</th>      <td></td> </tr>
    </table>

  </td>
  <td>

    <table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
    <tr> <th align=\"left\">Mês</th>        <td colspan=2>$month</td> </tr>
    <tr> <th align=\"left\">Nova</th>       <td></td> <td></td>       </tr>
    <tr> <th align=\"left\">Crescente</th>  <td></td> <td></td>       </tr>
    <tr> <th align=\"left\">Cheia</th>      <td></td> <td></td>       </tr>
    <tr> <th align=\"left\">Minguante</th>  <td></td> <td></td>       </tr>
    <tr> <th align=\"left\">Perigeu<img src=\"$help\" onclick=\"ppopen('perigeu',0,event)\" /></th> <td></td> <td></td> </tr>
    <tr> <th align=\"left\">Apogeu<img  src=\"$help\" onclick=\"ppopen('apogeu' ,0,event)\" /></th> <td></td> <td></td> </tr>
    </table>

  </td>
</tr>
</table>

<img src=\"$help\" onclick=\"ppopen('bluetooth',0,event)\" />, porque <a href=\"http://www.youtube.com/watch?v=61a0qHFcQE4\" target=\"_blank\">tudo fica melhor com Bluetooth</a>...

$def_divs

</body>

</html>
";

?>
