var swiper = new Swiper(".creative-section-swiper", {
  slidesPerView: 1,
  loop: true,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  navigation: {
    nextEl: ".next-btn",
    prevEl: ".prev-btn",
  },
});

var swiper = new Swiper(".notes", {
  slidesPerView: 3,
  spaceBetween: 20,
  pagination: false,
  loop: true,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },

  navigation: {
    nextEl: ".custom-next",
    prevEl: ".custom-prev",
  },
  breakpoints: {
    0: { slidesPerView: 1 },
    768: { slidesPerView: 3 },
  },
});

const founderSwiper = new Swiper(".founderSwiper", {
  slidesPerView: 3,
  spaceBetween: 20,
  loop: true,
  autoplay: {
    delay: 2500, // 2.5 seconds delay
    disableOnInteraction: false, // keeps autoplay even after user interaction
  },
  navigation: {
    nextEl: ".next-btn",
    prevEl: ".prev-btn",
  },
  breakpoints: {
    0: { slidesPerView: 1 },
    768: { slidesPerView: 2 },
    1024: { slidesPerView: 3 },
  },
});

var swiper = new Swiper(".news-two", {
  slidesPerView: 1,
  spaceBetween: 20,

  autoplay: {
    delay: 2000, // 2 seconds
    disableOnInteraction: false,
  },

  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

  navigation: {
    nextEl: ".next-btn",
    prevEl: ".prev-btn",
  },

  breakpoints: {
    576: { slidesPerView: 1 },
    768: { slidesPerView: 3 },
    992: { slidesPerView: 4 },
  },
});
