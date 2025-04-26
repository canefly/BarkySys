<?php 

include 'user-navigation.php';
include 'db.php'; // Include database connection

?>

<div class="container">
    <h2 style="text-align:center;">Our Pet Services</h2>
    <div class="services">
        <div class="service-card">
            <img src="img/doggy.png" alt="Dog Grooming">
            <h3>Dog Grooming</h3>
            <p>Keep your furry buddy clean and stylish with a professional trim, bath, and brush tailored for every dog.</p>
            <a href="dog-grooming.php" class="book-btn">View Now</a>
        </div>
        <div class="service-card">
            <img src="img/cat.png" alt="Cat Grooming">
            <h3>Cat Grooming</h3>
            <p>Gentle, stress-free grooming sessions made just for felines — because they deserve to be fabulous too.</p>
            <a href="cat-grooming.php" class="book-btn">View Now</a>
        </div>
    </div>
</div>
