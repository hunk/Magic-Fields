<?php
require_once(dirname(__FILE__)."/markdown.php");

$data = $_POST['data'];
if(get_magic_quotes_gpc())
{
	$data = stripslashes($data);
}

$parser_class = MARKDOWN_PARSER_CLASS;
$parser = new $parser_class;
$result = $parser->transform($data);
?>
<html>
	<body>
		<?php print $result; ?>
	</body>
</html>
