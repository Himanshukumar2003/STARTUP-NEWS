const el = document.querySelector("#typeTextTwo");

if (el) {
  var typed = new Typed("#typeTextTwo", {
    strings: [
      "India’s Best Startup News Website ^3000",
      "India’s Most Trusted Startup News Portal ^3000",
      "#1 Hub for Indian Startup News & Updates ^3000",
    ],
    typeSpeed: 50,
    backSpeed: 100,
    startDelay: 300,
    backDelay: 3000,
    loop: true,
    showCursor: false,
  });
}

var textWrapper = document.querySelector(".ml10 .letters");

document.querySelectorAll(".ml10 .letters").forEach((textWrapper, index) => {
  textWrapper.innerHTML = textWrapper.textContent.replace(
    /\S/g,
    "<span class='letter'>$&</span>"
  );

  anime
    .timeline({ loop: false })
    .add({
      targets: textWrapper.querySelectorAll(".letter"),
      rotateY: [-90, 0],
      opacity: [0, 1],
      duration: 100,
      delay: (el, i) => 70 * i,
    })
    .add({
      targets: textWrapper,
      opacity: 1,
      loop: false,
      duration: 3000,
      easing: "easeOutExpo",
      delay: 1000,
    });
});
