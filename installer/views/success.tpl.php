<?php 
function make_messages ($directory) {
    return [
        "Your application directory is ready for a new <code>$directory</code> directory.", 
        "A <code>$directory</code> directory already exists in your application directory"
    ];
}

$messages = [
    "home_writeable" => [
        "Your application directory <code>" . getcwd() . "</code> is writeable.", 
        "Your application directory <code>" . getcwd() . "</code> is not writeable"
    ],
    "no_public_directory" => make_messages("public"),
    "no_config_directory" => make_messages("config"),
    "no_src_directory" => make_messages("src"),
    "no_view_directory" => make_messages("views")
];
if($success): ?>
    <h2>Awesome!</h2>
    <p>Your project is successfuly initialized</p>
    <a href="./">Click here to get started</a>
<?php else: ?>
    <h2>Something went wrong!</h2>
    <p>
    <ul id="preflight-checklist">
    <?php foreach($results as $key => $success): ?>
        <li class="<?= $success ? "success" : "failure" ?>"><?= $messages[$key][$success ? 0 : 1] ?></li>
    <?php endforeach; ?>
    </ul>
    </p>
    <p>Please fix the failures listed above to proceed</p>
<?php endif; ?>