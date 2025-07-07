<?php

// API endpoint
$api_url = "https://www.cbn.gov.ng/api/GetAllCirculars?format=json";
$base_url = "https://www.cbn.gov.ng";


// Task 1: Get data from API and save as JSON
$json_data = file_get_contents($api_url);

if ($json_data === false) {
    die("Error: Could not fetch data from API\n");
}

$circulars = json_decode($json_data, true);

if ($circulars === null) {
    die("Error: Could not decode JSON data\n");
}

echo "Found " . count($circulars) . " circulars\n";

file_put_contents('cbn_circulars.json', json_encode($circulars, JSON_PRETTY_PRINT));
echo "Saved all circulars to cbn_circulars.json\n";

// Task 2: Download PDFs to sub-directory
echo "\nTask 2: Downloading PDFs...\n";

// Create sub-directory
$pdf_dir = 'cbn_pdfs';
if (!file_exists($pdf_dir)) {
    mkdir($pdf_dir, 0755, true);
    echo "Created directory: $pdf_dir\n";
}

$downloaded = 0;
$failed = 0;

foreach ($circulars as &$circular) {
    if (empty($circular['link'])) {
        continue;
    }
    
    // Build full PDF URL
    $pdf_url = $base_url . $circular['link'];
    
    // Get original filename from link
    $original_filename = basename($circular['link']);
    
    // Remove spaces from filename
    $clean_filename = str_replace(' ', '_', $original_filename);
    
    // Full path for saving
    $save_path = $pdf_dir . '/' . $clean_filename;
    
    echo "Downloading: $original_filename -> $clean_filename\n";
    
    // Download PDF
    $pdf_content = file_get_contents($pdf_url);
    
    if ($pdf_content !== false) {
        file_put_contents($save_path, $pdf_content);
        
        // Add the local file link to the circular data
        $circular['local_file'] = $save_path;
        $circular['original_filename'] = $original_filename;
        $circular['clean_filename'] = $clean_filename;
        
        $downloaded++;
        echo "Downloaded successfully\n";
    } else {
        echo "Failed to download\n";
        $failed++;
    }
    
    // Small delay to be respectful to the server
    sleep(1);
}

file_put_contents('cbn_circulars.json', json_encode($circulars, JSON_PRETTY_PRINT));

echo "\n=== SUMMARY ===\n";
echo "Total circulars: " . count($circulars) . "\n";
echo "Successfully downloaded: $downloaded\n";
echo "Failed downloads: $failed\n";
echo "JSON file: cbn_circulars.json\n";
echo "PDF directory: $pdf_dir\n";
echo "Done!\n";
?>