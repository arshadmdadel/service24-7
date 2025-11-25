<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Cleaning Team</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="review.css">
</head>
<body>
    <section class="expert-team">
        <div class="heading">
            <h1>Top Service Providers</h1>
            <p class="subtitle">We have professional expert service providers ensuring top-notch service for you.</p>
        </div>

        <hr style="margin-bottom: 20px;">

        <div class="team-members">
            <div class="team-member">
                <img src="Electrician.jpg" alt="Team Member">
                <h3>Reynolds</h3>
                <p>He is an expert electrician staff member who provides thorough electrical fixes with precision.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="team-member">
                <img src="360_F_271389908_TeU2XysWbT1Lxlbum9IeLf9U4sH6Pz5j.jpg" alt="Team Member">
                <h3>Erick</h3>
                <p>He is an expert cleaning staff member who provides thorough cleaning with precision.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="team-member">
                <img src="Babysitter.jpeg" alt="Team Member">
                <h3>Sarah</h3>
                <p>She is an expert babysitter who provides thorough taking care of your baby with precision.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="client-reviews">
        <h2>Client Reviews</h2>
        <div class="review">
        <blockquote>
        <p>"My experience with Service 24/7 has been exceptional. The staff here is not only professional but also incredibly caring. They have made my father feel valued and respected, providing him with personalized care that meets his specific needs. I am grateful for the peace of mind knowing that he is in such good hands."</p>
        <footer>- Shafiqul Islam, Engineer</footer>
    </blockquote>

    <blockquote>
        <p>"Service 24/7 is a life-saver! Their team is incredible and always ready to help. The care and attention they provide are unmatched."</p>
        <footer>- Maria Johnson, Teacher</footer>
    </blockquote>

    <blockquote>
        <p>"Amazing service! Highly recommend Service 24/7 for anyone looking for reliable and compassionate care providers."</p>
        <footer>- David Brown, Entrepreneur</footer>
    </blockquote>
        </div>
    </section>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const slider = document.querySelector('.client-reviews'); 
    const slides = document.querySelectorAll('.client-reviews blockquote'); 
    const nextButton = document.createElement('button'); 
    const prevButton = document.createElement('button'); 
    let currentSlide = 0;

    nextButton.textContent = "\u25B6"; 
    prevButton.textContent = "\u25C0"; 
    nextButton.classList.add('slider-nav', 'next');
    prevButton.classList.add('slider-nav', 'prev');

    slider.appendChild(prevButton);
    slider.appendChild(nextButton);

    function updateSlide() {
        slides.forEach((slide, index) => {
            slide.style.display = index === currentSlide ? "block" : "none";
        });
    }

    nextButton.addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % slides.length; 
        updateSlide();
    });

    prevButton.addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length; 
        updateSlide();
    });

    updateSlide();
});
</script>
</body>
</html>
<!--footer -->
<?php include 'includes/footer.php'; ?>