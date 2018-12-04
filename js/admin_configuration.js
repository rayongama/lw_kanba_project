document.addEventListener("DOMContentLoaded", () => {
   const h = document.querySelector("input[type=hidden]");
   if (h && h.value === "true") {
       const snackbar = document.querySelector(".snackbar");
       snackbar.classList.add("snackbar-visible");
       setTimeout(function () {
           snackbar.classList.remove("snackbar-visible");
       }, 5000);
   }
});