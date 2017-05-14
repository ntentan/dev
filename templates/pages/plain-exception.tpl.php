<?php
$stream = defined('STDERR') ? STDERR : fopen("php://output", w);
fputs($stream, "Ntentan Error!\n");
fputs($stream, strip_tags($message . "\n\n"));
$max = 0;
$formated_trace = [];
foreach ($stack_trace as $trace_item) {
    $new_item = [
        'file' => "{$trace_item["file"]}:{$trace_item["line"]}",
        'call' => $trace_item["class"] . unescape($trace_item["type"]) . $trace_item["function"]
    ];
    if(strlen($new_item['file']) > $max) {
        $max = strlen($new_item['file']);
    }
    $formated_trace[] = $new_item;
}
foreach ($formated_trace as $trace_item) {
    fputs($stream, sprintf("%-{$max}s\t%s\n", $trace_item['file'], $trace_item['call']));
}
