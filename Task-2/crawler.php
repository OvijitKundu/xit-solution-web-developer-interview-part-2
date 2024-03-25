<?php

// Function to make a GET request using cURL
function curl_get_contents($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// Function to crawl a single product page
function parse_product_page($url) {
    $html = curl_get_contents($url);
    @$dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Example XPath queries, adjust based on actual HTML structure
    $title = $xpath->query('//h1[@class="product-title"]')->item(0)->nodeValue ?? '';
    $description = $xpath->query('//div[@class="product-description"]')->item(0)->nodeValue ?? '';
    $category = $xpath->query('//a[@class="product-category"]')->item(0)->nodeValue ?? '';
    $price = $xpath->query('//span[@class="product-price"]')->item(0)->nodeValue ?? '';
    $imageUrl = $xpath->query('//img[@class="product-image"]')->item(0)->getAttribute('src') ?? '';

    return [
        'title' => trim($title),
        'description' => trim($description),
        'category' => trim($category),
        'price' => trim($price),
        'url' => $url,
        'image_url' => $imageUrl,
    ];
}

$productUrls = [
    'https://yourpetpa.com.au/collections/dog',
    'https://yourpetpa.com.au/collections/cat',
    'https://yourpetpa.com.au/collections/shop-other',
    'https://yourpetpa.com.au/pages/online-pet-pharmacy',  
    'https://yourpetpa.com.au/collections/shop-all/Sale',  
];

$products = [];

foreach ($productUrls as $url) {
    $products[] = parse_product_page($url);
}

// CSV Generation
$csvFile = fopen('products.csv', 'w');
fputcsv($csvFile, ['Title', 'Description', 'Category', 'Price', 'Product URL', 'Image URL']); // Header

foreach ($products as $product) {
    fputcsv($csvFile, $product);
}

fclose($csvFile);

echo "Crawling completed and CSV generated.\n";
