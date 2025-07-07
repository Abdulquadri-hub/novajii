<?php

// Create PDF folder
if (!is_dir('pdfs')) mkdir('pdfs');

// Get webpage
$html = file_get_contents('https://www.cbn.gov.ng/Documents/circulars.html');
echo $html;
exit;

// preg_match_all('/<a[^>]+href="([^"]*\.pdf)"[^>]*>([^<]+)<\/a>/i', $html, $matches);

$circulars = [];
$count = 0;

echo "Found " . count($matches[0]) . " PDFs\n";

