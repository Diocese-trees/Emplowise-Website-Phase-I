/* GitHub Pages adapter: no PHP runtime here. Chat answers from its local
   knowledge base; scoring and booking need the Laravel app. */
(function () {
  var REPO = "https://github.com/Diocese-Trees/Emplowise-Website-Phase-I";
  var msg = "This is the static GitHub Pages preview. The assistant still answers, but scoring and booking run in the full Laravel app: " + REPO;
  function payload() {
    return new Response(JSON.stringify({ message: msg, reference: null, live: REPO }),
      { status: 503, headers: { "Content-Type": "application/json" } });
  }
  var nativeFetch = window.fetch ? window.fetch.bind(window) : null;
  window.fetch = function (input, init) {
    var url = typeof input === "string" ? input : (input && input.url) || "";
    if (url.indexOf("/api/") === 0 || url.indexOf(location.origin + "/api/") === 0) {
      return Promise.resolve(payload());
    }
    return nativeFetch ? nativeFetch(input, init) : Promise.reject(new Error("fetch unavailable"));
  };
  document.addEventListener("DOMContentLoaded", function () {
    var note = document.createElement("div");
    note.className = "demo-bar";
    note.setAttribute("role", "note");
    note.innerHTML = 'Static Pages preview of Emplowise \u2014 scoring and booking run in the ' +
      '<a href="' + REPO + '">full Laravel app</a>.';
    document.body.appendChild(note);
    document.body.classList.add("has-demo-bar");
  });
})();
