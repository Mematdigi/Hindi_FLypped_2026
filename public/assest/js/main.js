document.addEventListener('DOMContentLoaded', function () {
    const blogContent = document.getElementById('blogContent');
    const readMoreBtn = document.getElementById('readMoreBtn');

    // Initially limit the height to 50%
    blogContent.style.maxHeight = '500px';
    blogContent.style.overflow = 'hidden';

    // Toggle full blog content
    readMoreBtn.addEventListener('click', function () {
        if (blogContent.style.maxHeight === '500px') {
            blogContent.style.maxHeight = 'none'; // Show the full content
            blogContent.style.overflow = 'visible';
            readMoreBtn.textContent = 'Show Less'; // Change button text
        } else {
            blogContent.style.maxHeight = '500px'; // Collapse the content again
            blogContent.style.overflow = 'hidden';
            readMoreBtn.textContent = 'Read Full Blog'; // Reset button text
        }
    });
});