<?php

// Create PDF folder
if (!is_dir('pdfs')) mkdir('pdfs');

// Get webpage
$html = file_get_contents('https://www.cbn.gov.ng/Documents/circulars.html');

preg_match_all('/<a[^>]+href="([^"]*\.pdf)"[^>]*>([^<]+)<\/a>/i', $html, $matches);

$circulars = [];
$count = 0;

echo "Found " . count($matches[0]) . " PDFs\n";

// Process each PDF link
for ($i = 0; $i < count($matches[0]); $i++) {
    $url = $matches[1][$i];
    $title = trim(strip_tags($matches[2][$i]));
    
    if (empty($title)) continue;
    
    // Fix URL if relative
    if (strpos($url, 'http') !== 0) {
        $url = 'https://www.cbn.gov.ng' . $url;
    }
    
    // Clean filename
    $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $title) . '.pdf';
    
    $circulars[] = [
        'id' => ++$count,
        'title' => $title,
        'url' => $url,
        'filename' => $filename
    ];
    
    echo "[$count] $title\n";
    
    // Download PDF
    $pdf = file_get_contents($url);
    if ($pdf) {
        file_put_contents('pdfs/' . $filename, $pdf);
        echo "Downloaded\n";
    } else {
        echo "Failed\n";
    }
}

// Save JSON
$json = [
    'date' => date('Y-m-d H:i:s'),
    'total' => count($circulars),
    'circulars' => $circulars
];

file_put_contents('cbn_circulars.json', json_encode($json, JSON_PRETTY_PRINT));

echo "\nDone! Check 'pdfs/' folder and 'cbn_circulars.json'\n";
?>