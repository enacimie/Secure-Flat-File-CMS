<?php

use PHPUnit\Framework\TestCase;
use App\Core\Security;

class SecurityTest extends TestCase
{
    public function testEncryptionAndDecryption()
    {
        $original = "Hello World! This is a secret.";
        $encrypted = Security::encrypt($original);
        
        $this->assertNotEquals($original, $encrypted);
        $this->assertStringStartsWith('GCM|', $encrypted);
        
        $decrypted = Security::decrypt($encrypted);
        $this->assertEquals($original, $decrypted);
    }

    public function testDecryptionFailsWithTamperedData()
    {
        $encrypted = Security::encrypt("Sensitive Data");
        
        // Tamper the payload (change a char in the middle)
        $len = strlen($encrypted);
        $mid = intval($len / 2);
        $tampered = substr($encrypted, 0, $mid) . ($encrypted[$mid] === 'A' ? 'B' : 'A') . substr($encrypted, $mid + 1);
        
        // GCM should fail to authenticate
        $this->assertNull(Security::decrypt($tampered));
    }
}
