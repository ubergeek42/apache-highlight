<?php
// Options
$theme = 'Eclipse';         // What ace theme to use('Eclipse', 'tomorrow' are good choices)
$fontsize = '12pt';         // Default font size to display
$wrap_lines = 'true';       // Wrap long lines?  (*MUST* be a string, 'true' or 'false')
$show_invisibles = 'false'; // show whitespace   (*MUST* be a string, 'true' or 'false')
$full_width = false;        // Use the full browser width?

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


// Set the domain root
//      eg. You placed your script here: example.com/scripts/highlight.php
//          $root = '../' to go up one level.
$root = '../';


// Any custom css you want applied goes here:
$custom_css = "
    /* style applied to line numbers in the gutter */
    .ace_gutter-cell {
        /* Custom color for the line number color */
        color: #999;

        /* If you change this font size for line numbers, you will need to set the line-height
           to the height of the row. */
        /* font-size: 10pt;    */
        /* line-height: 22px;  */ /* 22px is the height when using 14pt $fontsize */
    }
";

//====================================================================================
// No need to edit below this point

// Get the file we want to show
$filename = $_SERVER['REQUEST_URI'];

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
$filecontents = file_get_contents($root . $filename);
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
        html, body {
            height: 100%;
        }
        .container {
            position: relative;
            height: 100%;
            <?php if ($full_width): ?>
            width:100%;
            <?php endif;?>
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
        #header .title h1 {
            float:left;
            height: 50px;
            margin: 0px;
            margin-right: 20px;
        }
        #header .info {
            float:left;
            height: 50px;
        }
        #header .toolbox {
            height: 50px;
            float: right;
        }
        .toolbox .checkbox {
            margin: 0px;
            padding: 0px;
        }
        #footer p{
            margin-top: 25px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 12pt;
        }

        <?=$custom_css;?>
    </style>
</head>
<body>
<div class="container">
    <div id="header">
		<div class="title"><h1><?=basename($filename)?></h1></div>
		<div class="info">
        <a href="?raw">Download/View Raw File</a><br>
		Filetype: <span id="filetype">Unknown</span>
		</div>
        <div class="toolbox">
            <div class="checkbox">
                <label for="show_invisibles">Show hidden characters
                <input type="checkbox"
                       onchange="editor.setShowInvisibles(this.checked);"
                       id="show_invisibles" <?php echo ($show_invisibles=='true'?'CHECKED':'')?>></input>
                </label>
                <br>
                <label for="wrap_lines">Wrap long lines
                <input type="checkbox"
                       onchange="editor.getSession().setUseWrapMode(this.checked);"
                       id="wrap_lines" <?php echo ($wrap_lines=='true'?'CHECKED':'')?>></input>
                </label>
            </div>
        </div>
    </div>
    <div class="row content">
	<?php if (!$error): ?>
        <pre id="editor"><?php echo htmlspecialchars($filecontents); ?></pre>
	<?php else: ?>
		<pre class="error"><?=$error?></pre>
	<?php endif ?>
    </div>
    <div id="footer">
    <p>
        Syntax highlighting provided by <a href="http://ace.c9.io">Ace</a>.
    </p>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ext-modelist.js" type="text/javascript" charset="utf-8"></script>
<?php if (!$error): ?>
<script>
    editor = ace.edit("editor");
    editor.setTheme("ace/theme/<?=$theme?>");

    editor.getSession().setUseWrapMode(document.getElementById("wrap_lines").checked);
    editor.setReadOnly(true);

    // Make the editor as big as the file to prevent scrolling(or 50 lines min)
    editor.setOption("maxLines", Infinity);
    editor.setOption("minLines", 50);
    editor.setShowInvisibles(document.getElementById("show_invisibles").checked);

    // Autodetect mode based on file name
    var modelist = ace.require('ace/ext/modelist');
    var mode = modelist.getModeForPath("<?=basename($filename)?>");
    editor.getSession().setMode(mode.mode);
	document.getElementById("filetype").innerHTML = mode.caption;
</script>
<?php endif;?>
</body>
</html>
