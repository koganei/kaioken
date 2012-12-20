<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Kaioken Manga Reader</title>
	<meta name="description" content="">
	<meta name="author" content="Marc Khoury">

	<meta name="viewport" content="width=device-width">

	<link rel="shortcut icon" href="favicon.ico" />

	<!--link rel="stylesheet" href="css/style.css"-->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/default.css">

	<script src="libs/modernizr-2.5.3.min.js"></script>
</head>
<body>
<div id="statusbar" class="statusbar alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
  	<span class="status">Welcome to Kaioken!</span>
</div>

<div class="row-fluid content">
  <div class="span4 navbar-inner sidebar" id="sidebar">

	<div class="btn-group headtags">
		<span class="label label-important" id="kaiokentag" rel="tooltip" data-original-title="v0.8 - Marc Khoury<br /><a href='http://www.mangaeden.com/api/'>MangaEden API</a>">Kaioken</span>
	</div>  	

	
	<div class="control-group">
	  	<label class="control-label" for="options"><h3>Options</h3></label>
	  	
	  	<select id="options">
	  		<option value="full-width">Full Width</option>
	  		<option value="full-height">Full Height</option>
	  		<option value="double-page">Double Page</option>
	  		<option value="scroll-right">Scroll Right</option>
	  		<option value="scroll-left">Scroll Left</option>
	  	</select>
	  
  	</div>
 
 	<div class="control-group">
	  	<label class="control-label" for="cacheoption">
	  		<input type="checkbox" id="cacheoption"> Cache to LocalStorage
	  	</label>
	  	<input type="button" id="ziplocalstorage" value="Download zip of LocalStorage">
	  
  	</div>
<div class="chapterlist-loading-holder"></div>
  	<div id="chapterlist-wrapper"></div>

   	<div id="mangalist-wrapper"></div>
  </div>
  <div class="span8 pagelist" id="page-wrapper">

  </div>
</div>
<div ></div>


<!--script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="libs/jquery-1.7.2.min.js"><\/script>')</script-->

<script data-main="libs/main" src="libs/require.js"></script>

<script>
	var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>

</body>
</html>
