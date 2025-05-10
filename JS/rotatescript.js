// script.js
document.addEventListener('DOMContentLoaded', () => {
    const images = ['../IMG/M1.webp', '../IMG/M2.webp', '../IMG/M3.webp', '../IMG/M4.webp'];
    let currentIndex = 0;
    const imgElement = document.getElementById('rotating-image');

    function rotateImage() {
        currentIndex = (currentIndex + 1) % images.length;
        console.log('Rotating to:', images[currentIndex]); 
        imgElement.src = images[currentIndex];
    }

    // Rotate every 3 seconds (3000 milliseconds)
    setInterval(rotateImage, 3000);
});
