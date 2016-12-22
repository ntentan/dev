<!doctype html>
<html lang="en">
    <head>
        <title>Exception : <?= $type ?></title>
        <style>
            body{
                margin: 0px;
                font-family: sans-serif;
            }
            
            header{
                background-color: #0099ff;
                color:#fff;
                padding:20px;
            }
            
            div#body{
                padding:20px;
            }
            
            header > h1{
                margin:0px;
                padding:0px;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Uncaught <?= $type ?> thrown</h1>
        </header>
        <div id="body">
            Uncaught exception <code><?= $type ?></code> with message "<?= $message ?>" thrown on line <?= $line ?> of <?= $file ?>.
            <h2>Stack Trace</h2>
            <table>
            <thead>
                <tr><th>File</th><th>Line</th><th>Function</th></tr>
            </thead>
            <tbody>
            <?php foreach($stack_trace as $trace_item):?>
                <tr>
                    <td><code><?php echo $trace_item["file"]?></code></td>
                    <td><code><?php echo $trace_item["line"]?></code></td>
                    <td>
                        <code>
                            <b><?php echo $trace_item["class"].$trace_item["type"].$trace_item["function"]; ?></b>
                        </code>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
            </table>
            
        </div>
    </body>
</html>