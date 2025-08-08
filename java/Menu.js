document.addEventListener("DOMContentLoaded", () => {
    const testimonials = document.querySelectorAll(".testimonial");
    const dots = document.querySelectorAll(".dot");

    let current = 0;

    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.classList.toggle("active", i === index);
            dots[i].classList.toggle("active", i === index);
        });
    }

    dots.forEach((dot, i) => {
        dot.addEventListener("click", () => {
            current = i;
            showTestimonial(current);
        });
    });

    setInterval(() => {
        current = (current + 1) % testimonials.length;
        showTestimonial(current);
    }, 5000); // Change testimonial every 5 seconds
});




document.addEventListener("DOMContentLoaded", () => {
    const chefCard = document.getElementById("chefCard");
    chefCard.addEventListener("click", () => {
        chefCard.classList.toggle("flip");
    });
});



document.addEventListener("DOMContentLoaded", () => {
    // Optional: Add smooth scroll or other effects here
});

function flipCard(card) {
    card.classList.toggle("flip");
}
