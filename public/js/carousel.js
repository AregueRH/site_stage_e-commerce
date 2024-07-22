document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.image');
    let currentIndex = 0;

    function showNextImage() {
        images[currentIndex].style.opacity = 0;
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].style.opacity = 1;
    }

    setInterval(showNextImage, 5000);

    // Initial state
    images[currentIndex].style.opacity = 1;
});
