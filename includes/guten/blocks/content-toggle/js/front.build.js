"use strict";

Array.prototype.slice.call(document.getElementsByClassName("wp-block-block-engine-content-toggle")).forEach(function (toggleContainer) {
  if (!toggleContainer.hasAttribute("data-preventcollapse")) {
    var parentIsHidden = false;
    var parentClassIsHidden = false;
    var targetElement = toggleContainer;

    while (!(parentIsHidden || parentClassIsHidden) && targetElement.parentElement.tagName !== "BODY") {
      targetElement = targetElement.parentElement;

      if (targetElement.style.display === "none") {
        parentIsHidden = true;
      }

      if (getComputedStyle(targetElement).display === "none") {
        parentClassIsHidden = true;
      }
    }

    if (parentClassIsHidden || parentIsHidden) {
      toggleContainer.parentElement.style.setProperty("display", "block", //make the parent block display to give way for height measurements
      "important" //just in case blocks from other plugins use !important
      );
    }

    Array.prototype.slice.call(toggleContainer.getElementsByClassName("wp-block-block-engine-content-toggle-accordion-title-wrap")).forEach(function (instance) {
      var indicator = instance.querySelector(".wp-block-block-engine-content-toggle-accordion-state-indicator");
      var panelContent = instance.nextElementSibling;
      var panelHeight = 0;
      var initialHide = panelContent.style.height === "0px" || panelContent.classList.contains("block-engine-hide") || getComputedStyle(panelContent).display === "none";

      if (initialHide) {
        //temporarily show panel contents to enable taking panel height measurements
        panelContent.classList.remove("block-engine-hide");
        panelContent.style.height = "";
        panelContent.style.paddingTop = "";
        panelContent.style.paddingBottom = "";
        panelContent.style.display = "";
      }

      panelHeight = panelContent.offsetHeight;
      panelContent.removeAttribute("style");

      if (initialHide) {
        setTimeout(function () {
          return panelContent.classList.add("block-engine-hide");
        }, 10);
      }

      instance.addEventListener("click", function (e) {
        e.stopImmediatePropagation();

        if (indicator.classList.contains("open")) {
          if (panelHeight !== panelContent.offsetHeight) {
            panelHeight = panelContent.offsetHeight;
          }

          panelContent.style.height = "".concat(panelHeight, "px");
        } else {
          panelContent.classList.remove("block-engine-hide");
          panelContent.classList.add("block-engine-hiding");
        }

        panelContent.classList.add("block-engine-toggle-transition");
        indicator.classList.toggle("open");
        setTimeout(function () {
          //delay is needed for the animation to run properly
          if (indicator.classList.contains("open")) {
            panelContent.classList.remove("block-engine-hiding");
            panelContent.style.height = "".concat(panelHeight, "px");
          } else {
            panelContent.classList.add("block-engine-hiding");
            panelContent.style.height = "";
          }
        }, 20);
        var flickityInstances = Array.prototype.slice.call(panelContent.querySelectorAll(".block_engine_image_slider"));
        flickityInstances.forEach(function (instance) {
          var slider = Flickity.data(instance.querySelector("[data-flickity]"));
          slider.resize();
        });
        Array.prototype.slice.call(panelContent.querySelectorAll(".wp-block-embed iframe")).forEach(function (embeddedContent) {
          embeddedContent.style.removeProperty("width");
          embeddedContent.style.removeProperty("height");
        });
      });
      panelContent.addEventListener("transitionend", function () {
        panelContent.classList.remove("block-engine-toggle-transition");

        if (indicator.classList.contains("open")) {
          panelContent.style.height = "";
        } else {
          panelContent.classList.remove("block-engine-hiding");
          panelContent.classList.add("block-engine-hide");
        }
      });
      panelContent.removeAttribute("style");
    }); //hide the parent element again;

    if (parentIsHidden) {
      toggleContainer.parentElement.style.display = "none";
    }

    if (parentClassIsHidden) {
      toggleContainer.parentElement.style.display = "";
    }
  }
});