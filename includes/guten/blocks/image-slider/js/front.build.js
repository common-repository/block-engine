"use strict";

document.addEventListener("DOMContentLoaded", function () {
  Array.prototype.slice.call(document.getElementsByClassName("block_engine_image_slider")).forEach(function (instance) {
    if (instance.querySelector("[data-flickity]")) {
      Flickity.data(instance.querySelector("[data-flickity]")).resize();
    }
  });
});