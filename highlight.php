<?php
// Options
$theme = 'tomorrow'; // What ace theme to use
$fontsize = '12px';  // Default font size to display

/**
Your .htaccess file should look something like this(You will need mod_actions enabled)

    Options +Indexes
    IndexOptions FancyIndexing HTMLTable

    # Two options to select what will get highlighted by this:
    # 1. Highlight everything
    ForceType text/plain
    Action text/plain /path/to/highlight.php

    # 2. Highlight only certain file extensions
    Action highlight-code /path/to/highlight.php
    AddHandler highlight-code .py
    AddHandler highlight-code .java
*/


//====================================================================================
// No need to edit below this point

// Get the file we want to show(PATH_TRANSLATED comes from apache's Action/AddHandler)
$filename = $_SERVER['PATH_TRANSLATED'];

// Default to showing ourself if there is no argument
if ($filename == '') {
    $filename = basename(__FILE__);
}

// Allow viewing the raw file
if (isset($_GET['raw'])) {
    header('Content-Type: text/plain');
    readfile($filename);
    exit();
}

$error = FALSE;
$filecontents = file_get_contents($filename);
if ($filecontents === FALSE) {
	$error = "Error loading file.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?=basename($filename)?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <style type="text/css" media="screen">
        .container {
            position:relative;
            height:100%;
        }
        #editor { 
            position: relative !important;
            border: 1px solid lightgray;
            margin: auto;
            height: 200px;
            width: 100%;
            font-size: <?=$fontsize?>;
        }
        #header {
            margin-bottom: 10px;
            margin-top: 10px;
        }
        #header div {
			float:left;
			height: 50px;
        }
		#header .title h1 {
			margin: 0px;
		}
		#header .info {
			margin-left: 20px;
		}
        #footer {
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 12px;
        }

        .ace_gutter-cell {
            color: #ccc;
        }
    </style>
</head>
<body>
<div class="container">
    <div id="header" class="row">
		<div class="title"><h1><?=basename($filename)?></h1></div>
		<div class="info">
        <a href="?raw">Download/View Raw File</a><br>
		Filetype: <span id="filetype">Unknown</span>
		</div>
    </div>
    <div class="row">
	<?php if (!$error): ?>
        <pre id="editor"><?php echo htmlspecialchars($filecontents); ?></pre>
	<?php else: ?>
		<pre class="error"><?=$error?></pre>
	<?php endif ?>
    </div>
    <div class="row">
    <p id="footer">
        Syntax highlighting provided by <a href="http://ace.c9.io">Ace</a>.
    </p>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
<?php if (!$error): ?>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/<?=$theme?>");

    editor.getSession().setUseWrapMode(true);
    editor.setReadOnly(true);

    // Make the editor as big as the file to prevent scrolling(or 50 lines min)
    editor.setAutoScrollEditorIntoView();
    editor.setOption("maxLines", Infinity);
    editor.setOption("minLines", 50);

    // Autodetect mode based on file name
    var modelist = ace.require('ace/ext/modelist');
    var mode = modelist.getModeForPath("<?=basename($filename)?>");
    editor.getSession().setMode(mode.mode);
	document.getElementById("filetype").innerHTML = mode.caption;
</script>
<?php endif;?>
</body>
</html>
