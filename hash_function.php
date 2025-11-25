<?php
// filepath: c:\xampp\htdocs\service24_today\hash_function.php

/**
 * Custom Hash Function (Educational Purpose)
 * 
 * Security Properties:
 * 1. One-way: Hard to find x from h(x)
 * 2. Second pre-image resistant: Hard to find y≠x where h(x)=h(y)
 * 3. Collision-resistant: Hard to find any x,y where h(x)=h(y)
 * 
 * @param string $input - Message to hash
 * @param string $salt - Optional salt (default: fixed)
 * @return string - 64-character hex hash
 */
function simple_custom_hash(string $input, string $salt = 'default_salt'): string
{
    // Step 1: Mix input with salt
    $mixed = $salt . $input . strlen($input);
    
    // Step 2: Initial state (4 x 32-bit integers)
    $state = [
        0x67452301,  // A
        0xEFCDAB89,  // B
        0x98BADCFE,  // C
        0x10325476   // D
    ];
    
    // Step 3: Process input in chunks
    $bytes = unpack('C*', $mixed);
    $length = count($bytes);
    
    // Process each byte
    for ($i = 1; $i <= $length; $i++) {
        $byte = $bytes[$i];
        
        // Rotate state
        $temp = $state[0];
        $state[0] = $state[1];
        $state[1] = $state[2];
        $state[2] = $state[3];
        $state[3] = $temp;
        
        // Mix byte into state
        $state[0] = ($state[0] + $byte) & 0xFFFFFFFF;
        $state[1] = ($state[1] ^ ($byte << 8)) & 0xFFFFFFFF;
        $state[2] = ($state[2] * ($byte | 1)) & 0xFFFFFFFF;
        $state[3] = (($state[3] << 5) | ($state[3] >> 27)) & 0xFFFFFFFF;
        
        // Add non-linearity
        $state[0] = ($state[0] ^ $state[2]) & 0xFFFFFFFF;
        $state[1] = ($state[1] + $state[3]) & 0xFFFFFFFF;
    }
    
    // Step 4: Final mixing (avalanche effect)
    for ($round = 0; $round < 3; $round++) {
        $state[0] = ($state[0] ^ ($state[1] >> 3)) & 0xFFFFFFFF;
        $state[1] = ($state[1] + ($state[2] << 7)) & 0xFFFFFFFF;
        $state[2] = ($state[2] ^ ($state[3] >> 11)) & 0xFFFFFFFF;
        $state[3] = ($state[3] + ($state[0] << 13)) & 0xFFFFFFFF;
    }
    
    // Step 5: Convert to hex string
    $hash = '';
    foreach ($state as $value) {
        $hash .= str_pad(dechex($value), 8, '0', STR_PAD_LEFT);
    }
    
    return $hash;
}

/**
 * Stronger version with iterations (like PBKDF2)
 */
function simple_custom_hash_strong(string $input, string $salt = 'default_salt', int $iterations = 1000): string
{
    $hash = simple_custom_hash($input, $salt);
    
    // Re-hash multiple times (increases computational cost)
    for ($i = 0; $i < $iterations; $i++) {
        $hash = simple_custom_hash($hash . $input, $salt);
    }
    
    return $hash;
}
?>

<?php
// filepath: c:\xampp\htdocs\service24_today\test_hash.php

require_once 'hash_function.php';

echo "=== Simple Custom Hash Tests ===\n\n";

// Test 1: Same input = Same hash
$hash1 = simple_custom_hash("hello");
$hash2 = simple_custom_hash("hello");
echo "[1] Deterministic Test:\n";
echo "Hash 1: $hash1\n";
echo "Hash 2: $hash2\n";
echo "Match: " . ($hash1 === $hash2 ? "YES" : "NO") . "\n\n";

// Test 2: Different input = Different hash
$hash3 = simple_custom_hash("hello!");
echo "[2] Collision Resistance:\n";
echo "hello  → $hash1\n";
echo "hello! → $hash3\n";
echo "Different: " . ($hash1 !== $hash3 ? "YES" : "NO") . "\n\n";

// Test 3: Different salt = Different hash
$hash4 = simple_custom_hash("hello", "salt1");
$hash5 = simple_custom_hash("hello", "salt2");
echo "[3] Salt Test:\n";
echo "Salt 1 → $hash4\n";
echo "Salt 2 → $hash5\n";
echo "Different: " . ($hash4 !== $hash5 ? "YES" : "NO") . "\n\n";

// Test 4: Strong version (1000 iterations)
$start = microtime(true);
$hashStrong = simple_custom_hash_strong("password123", "user_salt", 1000);
$time = round((microtime(true) - $start) * 1000, 2);
echo "[4] Strong Hash (1000 iterations):\n";
echo "Hash: $hashStrong\n";
echo "Time: {$time}ms\n\n";

// Test 5: Empty input
$hashEmpty = simple_custom_hash("");
echo "[5] Empty Input:\n";
echo "Hash: $hashEmpty\n\n";

// Test 6: Long input
$longInput = str_repeat("abc", 1000);
$hashLong = simple_custom_hash($longInput);
echo "[6] Long Input (3000 chars):\n";
echo "Hash: $hashLong\n\n";

echo "All tests completed!\n";
?>