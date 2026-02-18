// script.js
window.addEventListener("load", function () {
  const params = new URLSearchParams(window.location.search);
  if (params.get("print") === "1") {
    window.print();
  }
});
