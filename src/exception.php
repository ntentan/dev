<!--
Exception: <?= $exception->getMessage() ?>
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ntentan Exception - <?= $exception->getMessage() ?></title>
        <style>
            body {
                margin: 0px;
                font-family: sans-serif;
            }

            h1 {
                padding: 10px;
                background-color: #a0a0a0;
                color: #fff;
                margin: 0px;
            }

            h2 {
                padding: 10px;
                margin: 0px
            }

            p {
                padding: 10px;
                margin: 0px
            }

            table {
                width: 100%;
                margin: 10px;
                border-collapse: collapse;
                font-size: smaller
            }

            th, td {
                padding: 5px
            }
        </style>
    </head>
    <body>
        <header>
            <h1><?= get_class($exception) ?></h1>
        </header>
        <p><?= $exception->getMessage() ?></p> <p>This exception was thrown on line <?= $exception->getLine() ?> of <?= $exception->getFile() ?></p>
        <h2>Stack Trace</h2>
        <table>
            <thead>
                <tr><th>#</th><th>File</th><th>Operation</th></tr>
            </thead>
            <tbody>
            <?php foreach($exception->getTrace() as $i => $item): ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $item['file'] ?? "" ?> <?= isset($item['line']) ? "({$item['line']})" : '' ?></td>
                <td><?= $item['class'] ?? "" ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>