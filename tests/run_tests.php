<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Security;
use App\Core\Parser;
use App\Core\Store;
use App\Core\Indexer;

class Tester {
    private static $passes = 0;
    private static $fails = 0;

    public static function run($name, $callback) {
        try {
            $callback();
            echo "✅ [PASS] $name\n";
            self::$passes++;
        } catch (Exception $e) {
            echo "❌ [FAIL] $name: " . $e->getMessage() . "\n";
            self::$fails++;
        }
    }

    public static function assertEqual($a, $b) {
        if ($a !== $b) throw new Exception("Expected '$b', got '$a'");
    }

    public static function assertTrue($a) {
        if (!$a) throw new Exception("Expected TRUE, got FALSE");
    }

    public static function summary() {
        echo "\nTests Completed. Passes: " . self::$passes . ", Fails: " . self::$fails . "\n";
        exit(self::$fails > 0 ? 1 : 0);
    }
}

echo "Running System Tests...\n\n";

// 1. Security Tests
Tester::run('Security: Encryption & Decryption (GCM)', function() {
    $original = "Hello World Secret Data";
    $encrypted = Security::encrypt($original);
    
    // Check Format
    Tester::assertTrue(strpos($encrypted, 'GCM|') === 0);
    
    // Check Roundtrip
    $decrypted = Security::decrypt($encrypted);
    Tester::assertEqual($decrypted, $original);
});

// 2. Parser Tests
Tester::run('Parser: Frontmatter Extraction', function() {
    $raw = "---\ntitle: Test\nstatus: draft\n---\n# Content";
    $parsed = Parser::parse($raw);
    
    Tester::assertEqual($parsed['meta']['title'], 'Test');
    Tester::assertEqual($parsed['meta']['status'], 'draft');
    Tester::assertEqual(trim($parsed['content']), '# Content');
});

// 3. Store Tests (Mocking file system via temp files in storage/tests)
Tester::run('Store: Save & Load', function() {
    $file = 'test_file.txt';
    $content = 'Test Content';
    
    // Ensure dir
    if (!is_dir(__DIR__ . '/../storage/tests')) mkdir(__DIR__ . '/../storage/tests');
    
    // Save
    Store::save($file, $content, 'tests');
    
    // Check file exists physically
    Tester::assertTrue(file_exists(__DIR__ . '/../storage/tests/' . $file));
    
    // Load
    $loaded = Store::load($file, 'tests');
    Tester::assertEqual($loaded, $content);
    
    // Cleanup
    Store::delete($file, 'tests');
});

// 4. Indexer Tests
Tester::run('Indexer: Rebuild & Query', function() {
    // Create dummy content
    $slug = 'test-index.md';
    $content = "---\ntitle: Indexed Page\ndate: 2025-01-01\n---\nBody";
    Store::save($slug, $content, 'content');
    
    // Force rebuild
    $index = Indexer::rebuild();
    
    // Check if in index
    $found = false;
    foreach ($index as $item) {
        if ($item['file'] === $slug && $item['title'] === 'Indexed Page') {
            $found = true; 
            break;
        }
    }
    
    Tester::assertTrue($found);
    
    // Cleanup
    Store::delete($slug, 'content');
    Indexer::delete($slug);
});

Tester::summary();
