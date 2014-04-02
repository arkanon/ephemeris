<?php

  # Google Maps API Quick Ref: <http://code.google.com/intl/en-US/apis/maps/documentation/reference.html>

  header('Content-type: application/javascript');

  session_start();
  $ctrl_size = $_SESSION['ctrl_size'];
  $lat10     = $_SESSION['lat10'];
  $long10    = $_SESSION['long10'];
  $zoom      = $_SESSION['zoom'];
  $maptype   = $_SESSION['maptype'];

  print "

  String.prototype.ltrim = function ltrim() { return this.replace(/^\s+/,'') }
  String.prototype.rtrim = function rtrim() { return this.replace(/\s+$/,'') }
  String.prototype.trim  = function  trim() { return this.ltrim().rtrim()    }



  function test60convertto10(event,prefix)
  {

    test  = document.getElementById(prefix+'60');
    conv  = document.getElementById(prefix+'10');
    hemis = prefix == 'lat' ? 'NS' : 'EW';

    e = event.which;
    document.getElementById('kc').value = e;

    ignore = new Array
    (
       8, // backspace
       9, // tab		control
      13, // enter		control
      16, // shift		modifier
      17, // ctrl		modifier
      18, // alt		modifier
      19, // pause/break
      20, // caps lock		modifier
      27, // escape
      33, // page up
      34, // page down
      35, // end
      36, // home
      37, // left  arrow
      38, // up    arrow
      39, // right arrow
      40, // down  arrow
      45, // insert
      46  // delete
    );

    ignore = ':'+ignore.join(':')+':';

    test.value = test.value.ltrim();
    test.value = test.value.replace(/n/g   ,'N');
    test.value = test.value.replace(/s/g   ,'S');
    test.value = test.value.replace(/[le]/g,'E');
    test.value = test.value.replace(/[ow]/g,'W');

    if ( ignore.search(':'+e+':') == -1 )
    {
      re = new RegExp('([' + hemis + ' .+-])+','g');
      test.value = test.value.replace(re,'$1');

      re = new RegExp('[^' + hemis + '0-9 .°\'\"+-]','g');
      test.value = test.value.replace(re,'');
    }

    test.value = test.value.replace(/ *([°\'\"]) *[°\'\"]* */g,'$1 ');
    test.value = test.value.replace(/ *\. */g,'.');
    test.value = test.value.replace(/([0-9]) ([0-9])/g,'$1$2');

    conv.value = coord60to10(test.value);

  }



  function coord10to60(coord10)
  //  input: (float)  45.555
  // output: (string) 45° 33\' 18\"
  {
    tmp     = coord10;
    deg     = parseInt(tmp);
    tmp     = parseDec(tmp) * 60;
    min     = parseInt(tmp);
    sec     = parseDec(tmp) * 60;
    coord60 = deg + '° ' + min + '\' ' + sec + '\"';
//  alert(coord10 + '\\n' + coord60);
    return coord60;
  }



  function coord60to10(coord60)
  //  input: (string) 45° 33\' 18\"
  // output: (float)  45.555
  {
    tmp = coord60;
    tmp = tmp.split(' ');
    if (tmp.length>0) coord10  = parseFloat(tmp[0].substr(0,tmp[0].length-1));
    if (tmp.length>1) coord10 += parseFloat(tmp[1].substr(0,tmp[1].length-1))/60;
    if (tmp.length>2) coord10 += parseFloat(tmp[2].substr(0,tmp[2].length-1))/60/60;
    return isNaN(coord10) ? 'erro' : coord10;
  }



  function coord60to10_detailed(coord60)
  //  input: (string) 45° 33\' 18\"
  // output: (float)  45.555
  {
    tmp     = coord60;
    tmp     = tmp.split(' ');
    deg     = tmp[0].substr(0,tmp[0].length-1);
    coord10 = parseFloat(deg);
    str     = deg + ' | ';
    if (tmp.length>1)
    {
      min      = tmp[1].substr(0,tmp[1].length-1);
      coord10 += parseFloat(min)/60;
      str     += min + ' | ';
    }
    if (tmp.length>2)
    {
      sec      = tmp[2].substr(0,tmp[2].length-1);
      coord10 += parseFloat(sec)/60/60;
      dec      = parseDec(sec);
      sec      = parseInt(sec);
      str     += sec + ' | ' + dec;
    }
    alert(coord60 + '\\n' + str + '\\n' + coord10);
    return coord10;
  }



  function parseDec(val)
  {
    val = (val.toString()+'.0').split('.');
    return parseFloat('.'+val[1]);
  }



  function select_field(field2enable, field2disable_name)
  {
    field2enable_name = field2enable.name;
    document.getElementById(field2enable_name).className  = 'selected';
    document.getElementById(field2disable_name).className = 'unselected';
  }



  function ppopen(id,tout,evt)
  {
    var offsetX =   0;
    var offsetY =  15;

    var offsetX = -10;
    var offsetY = -10;

    var x, y;

    if (document.all)
    { // IE
      x  = document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft;
      y  = document.documentElement && document.documentElement.scrollTop  ? document.documentElement.scrollTop  : document.body.scrollTop;
      x += window.event.clientX;
      y += window.event.clientY;
    }
    else
    { // browsers bem feitos
      x = evt.pageX;
      y = evt.pageY;
    }
    document.getElementById(id).style.left = (x + offsetX) + \"px\";
    document.getElementById(id).style.top  = (y + offsetY) + \"px\";

    document.getElementById(id).style.display = 'block';
    if (tout>0)
    {
      sto = setTimeout(\"ppclose('\"+id+\"',false)\", 1000*tout);
    }
    else
    {
      document.getElementById('address').value = document.getElementById('lat10').value+', '+document.getElementById('long10').value;
      document.getElementById('find').click();
      document.getElementById('address').value = '';
    }
  }



  function ppclose(id,tranfer)
  {
    if (tranfer)
    {
      document.getElementById('lat10').value  = document.getElementById('lat').innerHTML;
      document.getElementById('long10').value = document.getElementById('lng').innerHTML;
    }
    document.getElementById(id).style.display = 'none';
    clearTimeout(sto);
  }



  function processKey(e,submit)
  {
    if (e         == null) e = window.event;
    if (e.keyCode ==   13) submit.click();
  }



  function load()
  {

    if (GBrowserIsCompatible())
    {

      var map = new GMap2(document.getElementById(\"map\"));
      map.addControl(new G{$ctrl_size}MapControl());
      map.addControl(new GMapTypeControl());
//    var center = new GLatLng($lat10, $long10);
      var center = new GLatLng(document.getElementById('lat10').value, document.getElementById('long10').value);
      map.setCenter(center, $zoom);
      map.setMapType($maptype);
      geocoder = new GClientGeocoder();
      var marker = new GMarker(center, {draggable: true});
      map.addOverlay(marker);
//    document.getElementById(\"lat\").innerHTML = center.lat().toFixed(5);
//    document.getElementById(\"lng\").innerHTML = center.lng().toFixed(5);

      GEvent.addListener(marker, \"dragend\", function()
      {
        var point = marker.getPoint();
        map.panTo(point);
        document.getElementById(\"lat\").innerHTML = point.lat().toFixed(5);
        document.getElementById(\"lng\").innerHTML = point.lng().toFixed(5);

      } );


      GEvent.addListener(map, \"moveend\", function()
      {
        map.clearOverlays();
        var center = map.getCenter();
        var marker = new GMarker(center, {draggable: true});
        map.addOverlay(marker);
        document.getElementById(\"lat\").innerHTML = center.lat().toFixed(5);
        document.getElementById(\"lng\").innerHTML = center.lng().toFixed(5);
        GEvent.addListener(marker, \"dragend\", function()
        {
          var point = marker.getPoint();
          map.panTo(point);
          document.getElementById(\"lat\").innerHTML = point.lat().toFixed(5);
          document.getElementById(\"lng\").innerHTML = point.lng().toFixed(5);
        } );
      } );

    }

  }



  function showAddress(address)
  {
    var map = new GMap2(document.getElementById(\"map\"));
    map.addControl(new G{$ctrl_size}MapControl());
    map.addControl(new GMapTypeControl());
    if (geocoder)
    {
      geocoder.getLatLng(address, function(point)
      {
        if (!point)
        {
          alert(address + \" not found\");
        }
        else
        {
          document.getElementById(\"lat\").innerHTML = point.lat().toFixed(5);
          document.getElementById(\"lng\").innerHTML = point.lng().toFixed(5);
          map.clearOverlays()
          map.setCenter(point, $zoom);
          map.setMapType($maptype);
          var marker = new GMarker(point, {draggable: true});
          map.addOverlay(marker);
          GEvent.addListener(marker, \"dragend\", function()
          {
            var pt = marker.getPoint();
            map.panTo(pt);
            document.getElementById(\"lat\").innerHTML = pt.lat().toFixed(5);
            document.getElementById(\"lng\").innerHTML = pt.lng().toFixed(5);
          } );
          GEvent.addListener(map, \"moveend\", function()
          {
            map.clearOverlays();
            var center = map.getCenter();
            var marker = new GMarker(center, {draggable: true});
            map.addOverlay(marker);
            document.getElementById(\"lat\").innerHTML = center.lat().toFixed(5);
            document.getElementById(\"lng\").innerHTML = center.lng().toFixed(5);
            GEvent.addListener(marker, \"dragend\", function()
            {
              var pt = marker.getPoint();
              map.panTo(pt);
              document.getElementById(\"lat\").innerHTML = pt.lat().toFixed(5);
              document.getElementById(\"lng\").innerHTML = pt.lng().toFixed(5);
            } );
          } );
        }
      } );
    }
  }

";

?>
