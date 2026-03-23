<?php

/**
 * Test script to verify the dashboard calendar displays doctor names correctly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request to the dashboard page
$request = Illuminate\Http\Request::create('/dashboard', 'GET');

try {
    echo "Testing dashboard calendar...\n";
    echo "=============================\n\n";

    // Process the request
    $response = $kernel->handle($request);

    // Check response status
    $statusCode = $response->getStatusCode();
    echo "Response Status Code: $statusCode\n";

    if ($statusCode === 200) {
        echo "✅ SUCCESS: Dashboard loaded successfully!\n\n";

        // Check if the response contains expected content
        $content = $response->getContent();

        // Check for key elements
        $checks = [
            'Dashboard title' => strpos($content, 'Tableau de Bord Médical') !== false,
            'Calendar div' => strpos($content, 'id="dashboardCalendar"') !== false,
            'FullCalendar script' => strpos($content, 'FullCalendar.Calendar') !== false,
            'API events call' => strpos($content, '/api/rendezvous') !== false,
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

        // Test the API endpoint that feeds the calendar
        echo "\nTesting API endpoint /api/rendezvous...\n";
        echo "==========================================\n";

        $apiRequest = Illuminate\Http\Request::create('/api/rendezvous?start=2026-01-01&end=2026-01-31', 'GET');
        $apiResponse = $kernel->handle($apiRequest);
        $apiStatusCode = $apiResponse->getStatusCode();

        echo "API Response Status Code: $apiStatusCode\n";

        if ($apiStatusCode === 200) {
            $apiContent = $apiResponse->getContent();
            $apiData = json_decode($apiContent, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                echo "✅ API returned valid JSON\n";
                echo "Number of events: " . count($apiData) . "\n";

                if (count($apiData) > 0) {
                    echo "\nSample event data:\n";
                    $sampleEvent = $apiData[0];
                    echo "- Title: " . ($sampleEvent['title'] ?? 'N/A') . "\n";
                    echo "- Start: " . ($sampleEvent['start'] ?? 'N/A') . "\n";
                    echo "- Patient: " . ($sampleEvent['extendedProps']['patient'] ?? 'N/A') . "\n";
                    echo "- Medecin: " . ($sampleEvent['extendedProps']['medecin'] ?? 'N/A') . "\n";

                    // Check for "ERR" or null values
                    $hasErrors = false;
                    foreach ($apiData as $event) {
                        $medecin = $event['extendedProps']['medecin'] ?? '';
                        if ($medecin === 'ERR' || $medecin === null || $medecin === '') {
                            echo "❌ ERROR: Found invalid medecin value: '$medecin'\n";
                            $hasErrors = true;
                            break;
                        }
                    }

                    if (!$hasErrors) {
                        echo "✅ All events have valid medecin names\n";
                    }
                } else {
                    echo "ℹ️  No events found for the test period\n";
                }
            } else {
                echo "❌ API returned invalid JSON\n";
            }
        } else {
            echo "❌ API request failed\n";
            echo "Response: " . substr($apiResponse->getContent(), 0, 200) . "...\n";
        }

    } elseif ($statusCode === 302) {
        echo "⚠️  REDIRECT: Dashboard redirected (possibly authentication required)\n";
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
}

$kernel->terminate($request, $response ?? null);

echo "\n=============================\n";
echo "Dashboard calendar test completed.\n";
