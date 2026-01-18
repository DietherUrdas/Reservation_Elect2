<?php
// Process reservation form submission
session_start();

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ReservationUrdasSaromo.php');
    exit;
}

// Get form data
$customerName = isset($_POST['customerName']) ? trim($_POST['customerName']) : '';
$contactNumber = isset($_POST['contactNumber']) ? trim($_POST['contactNumber']) : '';
$fromMonth = isset($_POST['fromMonth']) ? $_POST['fromMonth'] : '';
$fromDay = isset($_POST['fromDay']) ? $_POST['fromDay'] : '';
$fromYear = isset($_POST['fromYear']) ? $_POST['fromYear'] : '';
$toMonth = isset($_POST['toMonth']) ? $_POST['toMonth'] : '';
$toDay = isset($_POST['toDay']) ? $_POST['toDay'] : '';
$toYear = isset($_POST['toYear']) ? $_POST['toYear'] : '';
$roomType = isset($_POST['roomType']) ? $_POST['roomType'] : '';
$roomCapacity = isset($_POST['roomCapacity']) ? $_POST['roomCapacity'] : '';
$paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : '';

// Validate required fields
$errors = [];
if (empty($customerName)) $errors[] = 'Customer Name';
if (empty($contactNumber)) $errors[] = 'Contact Number';
if (empty($fromMonth) || empty($fromDay) || empty($fromYear)) $errors[] = 'Check-in Date';
if (empty($toMonth) || empty($toDay) || empty($toYear)) $errors[] = 'Check-out Date';
if (empty($roomType)) $errors[] = 'Room Type';
if (empty($roomCapacity)) $errors[] = 'Room Capacity';
if (empty($paymentType)) $errors[] = 'Payment Type';

if (!empty($errors)) {
    $_SESSION['error'] = 'Please supply all necessary information.\n\nMissing fields:\n- ' . implode('\n- ', $errors);
    header('Location: ReservationUrdasSaromo.php');
    exit;
}

// Validate dates
$checkInDate = strtotime("$fromMonth $fromDay, $fromYear");
$checkOutDate = strtotime("$toMonth $toDay, $toYear");

if ($checkOutDate <= $checkInDate) {
    $_SESSION['error'] = 'Check-out date must be after check-in date.';
    header('Location: ReservationUrdasSaromo.php');
    exit;
}

// Room pricing per night (Rate/day)
$roomPrices = [
    'Single' => [
        'Regular' => 100.00,
        'De Luxe' => 300.00,
        'Suite' => 500.00
    ],
    'Double' => [
        'Regular' => 200.00,
        'De Luxe' => 500.00,
        'Suite' => 800.00
    ],
    'Family' => [
        'Regular' => 500.00,
        'De Luxe' => 750.00,
        'Suite' => 1000.00
    ]
];

// Calculate number of nights
$timeDiff = $checkOutDate - $checkInDate;
$nights = ceil($timeDiff / (60 * 60 * 24));
$totalNights = $nights > 0 ? $nights : 1;

// Get price per night
$pricePerNight = isset($roomPrices[$roomCapacity][$roomType]) 
    ? $roomPrices[$roomCapacity][$roomType] 
    : 0;

// Calculate base total bill (before discounts/charges)
$baseTotalBill = $pricePerNight * $totalNights;

// Apply payment type charges or cash discounts
$additionalCharge = 0;
$discount = 0;
$discountPercent = 0;

if ($paymentType === 'Cash') {
    // Cash discounts based on number of nights
    if ($totalNights >= 6) {
        $discountPercent = 15; // 15% discount for 6 days and above
    } else if ($totalNights >= 3) {
        $discountPercent = 10; // 10% discount for 3-5 days
    }
    $discount = $baseTotalBill * ($discountPercent / 100);
} else if ($paymentType === 'Cheque') {
    $additionalCharge = $baseTotalBill * 0.05; // +5% for Check
} else if ($paymentType === 'Credit Card') {
    $additionalCharge = $baseTotalBill * 0.10; // +10% for Credit Card
}

// Calculate final total bill
$totalBill = $baseTotalBill - $discount + $additionalCharge;

// Store reservation data in session
$_SESSION['reservation'] = [
    'customerName' => $customerName,
    'contactNumber' => $contactNumber,
    'fromMonth' => $fromMonth,
    'fromDay' => $fromDay,
    'fromYear' => $fromYear,
    'toMonth' => $toMonth,
    'toDay' => $toDay,
    'toYear' => $toYear,
    'roomType' => $roomType,
    'roomCapacity' => $roomCapacity,
    'paymentType' => $paymentType,
    'totalNights' => $totalNights,
    'pricePerNight' => $pricePerNight,
    'baseTotalBill' => $baseTotalBill,
    'discount' => $discount,
    'discountPercent' => $discountPercent,
    'additionalCharge' => $additionalCharge,
    'totalBill' => $totalBill,
    'dateReserved' => date('F j, Y'),
    'timeReserved' => date('h:i:s A')
];

// Redirect to result page
header('Location: result.php');
exit;
?>
