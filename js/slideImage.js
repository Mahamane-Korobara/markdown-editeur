document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.slider-container .preview-image');
    let currentIndex = 0;

    function switchImage() {
        images[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].classList.add('active');
    }

    setInterval(switchImage, 2000);
});