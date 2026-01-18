<?php
session_start();

// Check if reservation data exists in session
if (!isset($_SESSION['reservation'])) {
    header('Location: ReservationUrdasSaromo.php');
    exit;
}

$reservation = $_SESSION['reservation'];

// Generate random reservation ID
$reservationId = 'RES' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 9));

// Format currency
function formatCurrency($amount) {
    return '₱' . number_format($amount, 2, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Confirmation - Darrel & Ayien's Five Star Hotel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="top-bar">
        <div class="lab-exercise">MACHINE PROBLEM </div>
        <div class="datetime" id="currentDateTime">Loading...</div>
    </div>

    <div class="container fade-in">
        <header class="main-header">
            <div class="header-content">
                <h1 class="fade-in-up">DARREL & AYIEN'S</h1>
                <h2 class="fade-in-up delay-1">FIVE STAR HOTEL</h2>
                <p class="subtitle fade-in-up delay-2">RESERVATION CONFIRMATION</p>
            </div>
            <div class="header-decoration"></div>
        </header>

        <nav class="top-nav">
            <a href="home.html" class="nav-item">Home</a>
            <a href="company-profile.html" class="nav-item">Company's Profile</a>
            <a href="ReservationUrdasSaromo.php" class="nav-item active">Reservation</a>
            <a href="contacts.html" class="nav-item">Contacts</a>
        </nav>

        <div class="content-wrapper">
            <div class="form-container">
                <div class="result-section">
                <div class="success-icon">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                
                <h2 class="success-title">Reservation Submitted Successfully!</h2>
                <p class="success-message">Thank you for choosing Darrel & Ayien's Five Star Hotel. Your reservation has been confirmed.</p>

                <div class="confirmation-card">
                    <div class="card-header">
                        <h3>Reservation Details</h3>
                        <div class="reservation-id">Reservation ID: <span><?php echo htmlspecialchars($reservationId); ?></span></div>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Customer Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['customerName']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Contact Number</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['contactNumber']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date Reserved</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['dateReserved']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Time Reserved</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['timeReserved']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-in Date</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['fromMonth'] . ' ' . $reservation['fromDay'] . ', ' . $reservation['fromYear']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Check-out Date</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['toMonth'] . ' ' . $reservation['toDay'] . ', ' . $reservation['toYear']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Room Type</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['roomType']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Room Capacity</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['roomCapacity']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Payment Type</div>
                            <div class="detail-value"><?php echo htmlspecialchars($reservation['paymentType']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total Nights</div>
                            <div class="detail-value"><?php echo $reservation['totalNights']; ?> night(s)</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Price Per Night</div>
                            <div class="detail-value"><?php echo formatCurrency($reservation['pricePerNight']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Base Total</div>
                            <div class="detail-value"><?php echo formatCurrency($reservation['baseTotalBill']); ?></div>
                        </div>
                        <?php if ($reservation['paymentType'] === 'Cash' && $reservation['discount'] > 0): ?>
                        <div class="detail-item">
                            <div class="detail-label">Cash Discount</div>
                            <div class="detail-value discount-value"><?php echo $reservation['discountPercent']; ?>% - <?php echo formatCurrency($reservation['discount']); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if (($reservation['paymentType'] === 'Cheque' || $reservation['paymentType'] === 'Credit Card') && $reservation['additionalCharge'] > 0): ?>
                        <div class="detail-item">
                            <div class="detail-label">Additional Charge</div>
                            <div class="detail-value charge-value">
                                <?php 
                                $chargePercent = $reservation['paymentType'] === 'Cheque' ? '5%' : '10%';
                                echo '+' . $chargePercent . ' - ' . formatCurrency($reservation['additionalCharge']); 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="detail-item highlight-bill">
                            <div class="detail-label">Total Bill</div>
                            <div class="detail-value"><?php echo formatCurrency($reservation['totalBill']); ?></div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="ReservationUrdasSaromo.php" class="btn btn-primary">Make Another Reservation</a>
                    <button onclick="window.print()" class="btn btn-secondary">Print Confirmation</button>
                </div>

                <div class="info-box">
                    <p><strong>Important:</strong> Please arrive at the hotel on your check-in date. A confirmation email has been sent to your contact number. For any changes or cancellations, please contact us at least 24 hours before your check-in date.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update datetime in real-time
        function updateDateTime() {
            const now = new Date();
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const month = months[now.getMonth()];
            const day = now.getDate();
            const year = now.getFullYear();
            const hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const timeString = `${displayHours.toString().padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
            const dateTimeString = `${month} ${day}, ${year} @ ${timeString}`;
            document.getElementById('currentDateTime').textContent = dateTimeString;
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>
</html>
<?php
// Clear reservation data from session after display
unset($_SESSION['reservation']);
?>
