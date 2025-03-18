// Mobile menu logic
const pressMenuMobile = document.querySelector("header .menu-mobile");
const closeMenuMobile = [
    document.querySelector("header .menu-mobile-overlay"),
    document.querySelector("header .menu-mobile-content .menu-header .menu-mobile-close")
];

pressMenuMobile.addEventListener("click", function() {
    document.querySelector("header .menu-mobile-overlay").classList.add("opened");
    document.querySelector("header .menu-mobile-content").classList.add("opened");
})

closeMenuMobile.forEach(button => {
    button.addEventListener("click", function() {
        document.querySelector("header .menu-mobile-overlay").classList.remove("opened");
        document.querySelector("header .menu-mobile-content").classList.remove("opened");
    })
})

