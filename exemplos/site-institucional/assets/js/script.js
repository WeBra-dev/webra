const toggle=document.querySelector(".menu-toggle");
const menu=document.querySelector(".menu");
toggle?.addEventListener("click",()=>menu.classList.toggle("open"));
document.querySelectorAll(".menu a").forEach(a=>a.addEventListener("click",()=>menu.classList.remove("open")));