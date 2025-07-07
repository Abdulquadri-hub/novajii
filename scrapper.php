<?php

$url = "https://www.cbn.gov.ng/Documents/circulars.html";
$html = file_get_contents($url);

// Create download folder
$downloadDir = __DIR__ . '/downloads/';
if (!is_dir($downloadDir)) {
    mkdir($downloadDir, 0755, true);
}

preg_match_all('/<a[^>]+href=["\']([^"\']+\.pdf)["\'][^>]*>(.*?)<\/a>/i', $html, $matches);

$data = [];

for ($i = 0; $i < count($matches[0]); $i++) {
    $fileUrl = $matches[1][$i];
    $title = strip_tags(trim($matches[2][$i]));

    // Fix relative links
    if (strpos($fileUrl, 'http') !== 0) {
        $fileUrl = 'https://www.cbn.gov.ng' . $fileUrl;
    }

    // Generate safe filename
    $safeFileName = preg_replace('/\s+/', '-', strtolower($title)) . '.pdf';
    $safeFileName = preg_replace('/[^a-zA-Z0-9\-\.]/', '', $safeFileName);

    // Download PDF
    $pdfContent = file_get_contents($fileUrl);
    file_put_contents($downloadDir . $safeFileName, $pdfContent);

    // Append to JSON data
    $data[] = [
        'title' => $title,
        'file_name' => $safeFileName,
        'file_url' => $fileUrl
    ];
}

// Save to cbn_circulars.json
file_put_contents('cbn_circulars.json', json_encode($data, JSON_PRETTY_PRINT));

echo "Done. Circulars saved in cbn_circulars.json\n";
