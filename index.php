<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$page_title = "Home";

// Get total number of flights
$sql = "SELECT COUNT(*) AS total FROM flights";
$result = $pdo->query($sql);
$totalFlights = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Get total number of destinations
$sql = "SELECT COUNT(DISTINCT destination) AS total FROM flights";
$result = $pdo->query($sql);
$totalDestinations = $result->fetch(PDO::FETCH_ASSOC)['total'];

include 'includes/header.php';
?>

<section class="hero">
    <div class="hero-wrap">
        <span class="plane-tag">Book Your Flight Easily</span>

        <h1>Welcome to GetAir</h1>

        <p class="lead">
            Search available flights, book your ticket, and manage your bookings from one place.
            You can also check your PNR or cancel your ticket whenever required.
        </p>

        <div class="hero-buttons">
            <a href="book_tickets.php" class="btn btn-amber">Book Flight</a>
        </div>
    </div>
</section>

<div class="container">

    <div class="section-title">
        <h2>Our Services</h2>
        <p>Everything you need for a smooth flight booking experience.</p>
    </div>

    <div class="grid-3">

        <div class="card feature-card">
            <div class="icon">1</div>
            <h3>Search Flights</h3>
            <p class="muted">
                Browse <?php echo (int)$totalFlights; ?> available flights across
                <?php echo (int)$totalDestinations; ?> destinations.
            </p>
        </div>

        <div class="card feature-card">
            <div class="icon">2</div>
            <h3>Book Ticket</h3>
            <p class="muted">
                Reserve your seat securely and receive your PNR instantly after booking.
            </p>
        </div>

        <div class="card feature-card">
            <div class="icon">3</div>
            <h3>Cancel Booking</h3>
            <p class="muted">
                Cancel your ticket online and check your booking status anytime.
            </p>
        </div>

    </div>

</div>

<div class="container">

    <div class="card pnr-card">

        <div>
            <h3>Already Booked?</h3>
            <p class="muted">
                Use your PNR to check the booking status or cancel your ticket.
            </p>
        </div>

        <div class="action-buttons">
            <a href="check_pnr.php" class="btn btn-primary">Check PNR</a>
            <a href="cancel_ticket.php" class="btn btn-outline">Cancel Ticket</a>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>