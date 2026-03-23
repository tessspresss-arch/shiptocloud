<?php

/**
 * Test script to verify the agenda page loads without Blade errors
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request to the agenda page
$request = Illuminate\Http\Request::create('/agenda', 'GET');

try {
    echo "Testing agenda page...\n";
    echo "======================\n\n";
    
    // Process the request
    $response = $kernel->handle($request);
    
    // Check response status
    $statusCode = $response->getStatusCode();
    echo "Response Status Code: $statusCode\n";
    
    if ($statusCode === 200) {
        echo "✅ SUCCESS: Page loaded successfully!\n\n";
        
        // Check if the response contains expected content
        $content = $response->getContent();
        
        // Check for key elements
        $checks = [
            'Agenda Professionnel' => strpos($content, 'Agenda Professionnel') !== false,
            'FullCalendar CSS' => strpos($content, 'fullcalendar') !== false,
            'Calendar div' => strpos($content, 'id="calendar"') !== false,
            'Mini calendar div' => strpos($content, 'id="miniCalendar"') !== false,
            'Statistics cards' => strpos($content, 'todayCount') !== false,
        ];
        
        echo "Content Checks:\n";
        echo "---------------\n";
        foreach ($checks as $name => $result) {
            echo ($result ? "✅" : "❌") . " $name: " . ($result ? "Found" : "Not found") . "\n";
        }
        
        // Check for Blade errors
        if (strpos($content, 'InvalidArgumentException') !== false) {
            echo "\n❌ ERROR: Blade error detected in response!\n";
        } elseif (strpos($content, 'Cannot end a section') !== false) {
            echo "\n❌ ERROR: Section error detected in response!\n";
        } else {
            echo "\n✅ No Blade errors detected!\n";
        }
        
    } elseif ($statusCode === 302) {
        echo "⚠️  REDIRECT: Page redirected (possibly authentication required)\n";
        echo "Redirect Location: " . $response->headers->get('Location') . "\n";
    } else {
        echo "❌ ERROR: Unexpected status code\n";
        echo "Response: " . substr($response->getContent(), 0, 500) . "...\n";
    }
    
} catch (\Exception $e) {
    echo "❌ EXCEPTION CAUGHT:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    
    if (strpos($e->getMessage(), 'Cannot end a section') !== false) {
        echo "\n❌ BLADE ERROR STILL EXISTS: The section error is still present!\n";
    }
}

$kernel->terminate($request, $response ?? null);

echo "\n======================\n";
echo "Test completed.\n";
