<?php
/**
 * Update PHP Configuration for Large File Uploads
 * This script will automatically update your php.ini file
 */

$phpIniPath = php_ini_loaded_file();
$backupDir = dirname($phpIniPath) . DIRECTORY_SEPARATOR . 'backups';

echo "========================================\n";
echo "PHP Configuration Update Script\n";
echo "========================================\n\n";

// Check if php.ini exists
if (!file_exists($phpIniPath)) {
    die("ERROR: php.ini not found at: $phpIniPath\n");
}

echo "PHP ini file: $phpIniPath\n\n";

// Create backup directory
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0755, true)) {
        die("ERROR: Could not create backup directory\n");
    }
    echo "Created backup directory: $backupDir\n";
}

// Create backup
$backupFile = $backupDir . DIRECTORY_SEPARATOR . 'php.ini.backup.' . date('Y-m-d_H-i-s');
if (!copy($phpIniPath, $backupFile)) {
    die("ERROR: Could not create backup\n");
}
echo "Backup created: $backupFile\n\n";

// Read current php.ini
$content = file_get_contents($phpIniPath);

// Update values
$updates = [
    'upload_max_filesize' => '1024M',
    'post_max_size' => '1030M',
    'max_execution_time' => '600',
    'max_input_time' => '600',
    'memory_limit' => '1024M',
];

echo "Updating configuration values...\n\n";

foreach ($updates as $key => $value) {
    // Remove semicolon if commented out and update value
    $pattern = '/^;?\s*' . preg_quote($key, '/') . '\s*=.*/m';
    $replacement = $key . ' = ' . $value;

    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        echo "✓ Updated: $key = $value\n";
    } else {
        echo "✗ Not found: $key (you may need to add it manually)\n";
    }
}

// Write updated content
if (file_put_contents($phpIniPath, $content) === false) {
    die("\nERROR: Could not write to php.ini\n");
}

echo "\n========================================\n";
echo "Configuration Updated Successfully!\n";
echo "========================================\n\n";

echo "NEXT STEPS:\n";
echo "1. Restart Apache from XAMPP Control Panel\n";
echo "   - Stop Apache\n";
echo "   - Wait a few seconds\n";
echo "   - Start Apache\n\n";
echo "2. Run verification: php update_php_limits.php\n\n";
echo "If something goes wrong, restore from backup:\n";
echo "$backupFile\n\n";
