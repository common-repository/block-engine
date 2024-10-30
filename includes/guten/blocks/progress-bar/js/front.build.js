"use strict";

document.addEventListener("DOMContentLoaded", function () {
  setTimeout(function () {
    Array.prototype.slice.call(document.getElementsByClassName("block_engine_progress-bar")).forEach(function (instance) {
      instance.classList.add("block_engine_progress-bar-filled");
    });
  }, 500);
});