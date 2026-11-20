/* Static Netlify adapter.
   The Netlify build is a static snapshot: there is no PHP runtime, so the
   JSON endpoints that power the audit form, the assistant and the three AI
   student tools cannot execute here. Rather than failing silently, requests
   to /api/* are answered with a clear message and a link to the live app. */
(function () {
  var LIVE = "https://plug-surveys-conclude-calculation.trycloudflare.com";
  var LIVE_URL = LIVE + "/free-hr-audit";
  var msg = "This is the static Netlify copy. Booking, the assistant and the AI tools run on the live app at " + LIVE;

  function payload() {
    return new Response(JSON.stringify({
      message: msg,
      reference: null,
      next_step: "Open " + LIVE_URL + " to book the audit.",
      live: LIVE
    }), { status: 503, headers: { "Content-Type": "application/json" } });
  }

  var nativeFetch = window.fetch ? window.fetch.bind(window) : null;
  window.fetch = function (input, init) {
    var url = typeof input === "string" ? input : (input && input.url) || "";
    if (url.indexOf("/api/") === 0 || url.indexOf(location.origin + "/api/") === 0) {
      return Promise.resolve(payload());
    }
    return nativeFetch ? nativeFetch(input, init) : Promise.reject(new Error("fetch unavailable"));
  };

  // Banner styling lives in /css/static-banner.css so the page needs no inline
  // styles and can run under a strict style-src 'self' policy.
  document.addEventListener("DOMContentLoaded", function () {
    var note = document.createElement("div");
    note.className = "demo-bar";
    note.setAttribute("role", "note");
    note.innerHTML = 'Static Netlify preview — forms and AI run on the ' +
      '<a href="' + LIVE_URL + '">live app</a>.';
    document.body.appendChild(note);
    document.body.classList.add("has-demo-bar");
  });
})();
