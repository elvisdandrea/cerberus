<!--
Arquivo Home.tpl
Arquivo template carregado no primeiro acesso ao site
Carrega os scripts e os css que serão usado para o site
E inicia as 'DIV' principais

-#HEADER
-Container-fluid
 >#SIDEBAR
 >#CENTER

O site então é carregado em cima dessar 'DIV' em outros arquivos templates

Todo o conteúdo do site, e também na troca de aba, são feito em html::replace em cima dessas 'DIV'

-->


<!DOCTYPE html>
<html lang="en">
<Head>

<meta content="text/html; charset=utf-8">
<meta name="Raptor" content="Raptor Reporter">

<!-- -------------------CSS----------------------- -->
<link rel="stylesheet" href="{$cssdir}/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="{$jsdir}/jquery-ui/jquery-ui.css">
<link rel="stylesheet" href="{$jsdir}/chosen/chosen.css">
<link rel="stylesheet" href="{$cssdir}/main.css">

<!-- ------------------JQuery--------------------- -->
<script type="text/javascript" src="{$jsdir}/jquery-1.9.1.js"></script>
</Head>

<body>
<!-- ----------------Div da Header----------------- -->
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="header">
   {$header}
  </div>

  <div class="container-fluid">
    <div class="row" id="main">

<!-- ----------------Div da Sidebar---------------- -->
     <div class="col-sm-3 col-md-2 sidebar" id="sidebar">
      {$sidebar}
     </div>

<!-- ----------------Div central------------------- -->
     <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="center">
      {$content}
     </div>

    </div>
  </div>

</body>

<!-- ------------------JavaScripts----------------- -->
<script type="text/javascript" src="{$jsdir}/md5.js"></script>
<script type="text/javascript" src="{$jsdir}/html.js"></script>
<script type="text/javascript" src="{$jsdir}/forms.js"></script>
<script type="text/javascript" src="{$jsdir}/main.js"></script>
<script type="text/javascript" src="{$jsdir}/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="{$jsdir}/chosen/chosen.jquery.js"></script>
</html>