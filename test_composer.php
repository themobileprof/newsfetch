<?php
require 'vendor/autoload.php';
//////////////////////////////
$myXMLContent = <<<XML
<xml>
    <foo>
        <bar awesome="true">this is a example</bar>
    </foo>
</xml>
XML;

$myContent = (new DMX\SimpleXML\Parser($myXMLContent))->toArray();
print_r($myContent);
