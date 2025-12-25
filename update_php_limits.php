<?php
/**
 * PHP Upload Limits Configuration Helper
 *
 * This script will show you what needs to be changed in your php.ini file
 * to support larger video file uploads (up to 500MB).
 */

echo "=== Current PHP Configuration ===\n\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "max_input_time: " . ini_get('max_input_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

echo "\n=== PHP Configuration File Location ===\n\n";
echo php_ini_loaded_file() . "\n";

echo "\n=== REQUIRED CHANGES ===\n\n";
echo "To support video uploads up to 1024MB (1GB), you need to update your php.ini file:\n\n";
echo "1. Open: " . php_ini_loaded_file() . "\n";
echo "2. Find and update these values:\n\n";
echo "   upload_max_filesize = 1024M\n";
echo "   post_max_size = 1030M\n";
echo "   max_execution_time = 600\n";
echo "   max_input_time = 600\n";
echo "   memory_limit = 1024M\n\n";
echo "3. Save the file\n";
echo "4. Restart Apache from XAMPP Control Panel\n";
echo "5. Run this script again to verify the changes\n\n";

echo "=== NOTES ===\n\n";
echo "- post_max_size should be slightly larger than upload_max_filesize\n";
echo "- If you see a semicolon (;) at the start of any line, remove it\n";
echo "- Execution times are in seconds (300 = 5 minutes)\n";
echo "- After restarting Apache, refresh your browser and try uploading again\n";
