<?php

  header('Content-type: text/css');

  print "

    .selected
    {
      color: black;
      background: white;
    }

    .unselected
    {
      color: #7f7f7f;
      background: #dfdfdf;
    }

    .popup
    {
      position: absolute;
      padding: 10px;
      border: 1px solid #ffa200;
      background: #fffbcf;
      display: none;
      z-index: 99;
    }

    .tooltip
    {
      padding: 0 3px;
      background: #dfebff;
      border-color: #0057bf;
    }

    .definicao
    {
      text-decoration: none;
      display: block;
      font-weight: bold;
      border-bottom: 1px dotted #0057bf;
    }

    h2
    {
      margin: 0 0 5px 0;
      text-align: center;
    }

    .rline>td
    {
      background: #dfdfdf;
      padding: 5px;
    }

    td, th
    {
      vertical-align: top;
      padding: 0 5px;
    }

    img
    {
      cursor: pointer;
    }

    form
    {
      margin: 0;
    }

";

?>
