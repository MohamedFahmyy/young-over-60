<?php
$html = file_get_contents('http://127.0.0.1:8080/pages/about-us');
$start = strpos($html, '<main');
echo substr($html, $start, 600);
