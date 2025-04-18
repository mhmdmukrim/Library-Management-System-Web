<?php
require 'dbconn.php';
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function create_id_card($membershipNo, $name, $email)
{
    // Create an image with a white background
    $width = 400;
    $height = 250;
    $image = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);

    // Load and resize logo
    $logo_path = 'images/rbg.png'; // Update the path
    if (!file_exists($logo_path)) {
        die("Logo file not found.");
    }
    $logo = imagecreatefrompng($logo_path);
    if ($logo === false) {
        die("Failed to load logo image.");
    }

    // Resize logo
    $logo_width = imagesx($logo);
    $logo_height = imagesy($logo);
    $new_logo_width = 100; // New width
    $new_logo_height = ($logo_height / $logo_width) * $new_logo_width;
    $resized_logo = imagecreatetruecolor($new_logo_width, $new_logo_height);
    imagefill($resized_logo, 0, 0, $white);
    imagecopyresampled($resized_logo, $logo, 0, 0, 0, 0, $new_logo_width, $new_logo_height, $logo_width, $logo_height);

    // Copy the resized logo to the ID card (top-right)
    $logo_x = $width - $new_logo_width - 10; // 10 pixels padding from right
    $logo_y = 10; // 10 pixels padding from top
    imagecopy($image, $resized_logo, $logo_x, $logo_y, 0, 0, $new_logo_width, $new_logo_height);

    // Generate QR code
    $qrCode = new QrCode($membershipNo);
    $qrCode->setSize(100);
    $qrCode->setMargin(10);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $qrCodeImage = imagecreatefromstring($result->getString());

    // Generate barcode
    $barcode_generator = new BarcodeGeneratorPNG();
    $barcodeImage = imagecreatefromstring($barcode_generator->getBarcode($membershipNo, $barcode_generator::TYPE_CODE_128));

    // Load QR code and barcode images
    $qr_code_width = imagesx($qrCodeImage);
    $qr_code_height = imagesy($qrCodeImage);
    $barcode_width = imagesx($barcodeImage);
    $barcode_height = imagesy($barcodeImage);

    // Place QR code and barcode on the ID card
    $qr_code_x = 20;
    $qr_code_y = $height - $qr_code_height - -12;
    $barcode_x = $width - $barcode_width - 20;
    $barcode_y = $height - $barcode_height - 20;

    imagecopy($image, $qrCodeImage, $qr_code_x, $qr_code_y, 0, 0, $qr_code_width, $qr_code_height);
    imagecopy($image, $barcodeImage, $barcode_x, $barcode_y, 0, 0, $barcode_width, $barcode_height);

    // Set colors
    $black = imagecolorallocate($image, 0, 0, 0);
    $blue = imagecolorallocate($image, 0, 0, 255); // For title

    // Use built-in GD fonts (e.g., 1 to 5 for various sizes) or a custom font
    $font = 'font/Mondapick.ttf'; // Path to a TTF font file

    // Add a title to the ID card using TrueType fonts
    imagettftext($image, 14, 0, 20, 30, $blue, $font, "Akurana Public Library");

    // Add text to the ID card using TrueType fonts
    imagettftext($image, 10, 0, 20, 60, $black, $font, "Name: $name");
    imagettftext($image, 10, 0, 20, 100, $black, $font, "Membership No: $membershipNo");
    imagettftext($image, 10, 0, 20, 140, $black, $font, "Email: $email");

    // Output the image
    header('Content-Type: image/png');
    imagepng($image);

    // Free memory
    imagedestroy($image);
    imagedestroy($logo);
    imagedestroy($resized_logo);
    imagedestroy($qrCodeImage);
    imagedestroy($barcodeImage);
}

// Fetch user data from the database
$membershipNo = $_GET['id'];
$sql = "SELECT Name, EmailId FROM LMS.user WHERE MembershipNo='$membershipNo'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$name = $row['Name'];
$email = $row['EmailId'];

// Generate ID card
create_id_card($membershipNo, $name, $email);
?>
