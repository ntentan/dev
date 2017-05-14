<?=
$stream = defined('STDERR') ? STDERR : fopen("php://output", w);
fputs($stream, "Ntentan Error!\n");
fputs($stream, strip_tags(unescape($message) . "\n\n"));
foreach ($stack_trace as $trace_item) {
    fputs($stream, "{$trace_item["file"]} ({$trace_item["line"]})\t\t\t\t"
        . $trace_item["class"] . unescape($trace_item["type"])
        . $trace_item["function"] . "\n"
    );
}
