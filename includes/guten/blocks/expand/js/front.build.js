"use strict";

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);

    return null;
  };
}

function block_engine_getSiblings(element, criteria) {
  var children = Array.prototype.slice.call(element.parentNode.children).filter(function (child) {
    return child !== element;
  });
  return criteria ? children.filter(criteria) : children;
}

Array.prototype.slice.call(document.getElementsByClassName("block-engine-expand-toggle-button")).forEach(function (instance) {
  instance.addEventListener("click", function () {
    var blockRoot = instance.closest(".block-engine-expand");
    blockRoot.querySelector(".block-engine-expand-partial .block-engine-expand-toggle-button").classList.toggle("block-engine-hide");
    var expandingPart = Array.prototype.slice.call(blockRoot.children).filter(function (child) {
      return child.classList.contains("block-engine-expand-full");
    })[0];
    expandingPart.classList.toggle("block-engine-hide");
    var flickityInstances = Array.prototype.slice.call(expandingPart.querySelectorAll(".block_engine_image_slider"));
    flickityInstances.forEach(function (instance) {
      var slider = Flickity.data(instance.querySelector("[data-flickity]"));
      slider.resize();
    });
    Array.prototype.slice.call(expandingPart.querySelectorAll(".wp-block-embed iframe")).forEach(function (embeddedContent) {
      embeddedContent.style.removeProperty("width");
      embeddedContent.style.removeProperty("height");
    });
  });
});