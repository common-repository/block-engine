if (!Element.prototype.matches) {
	Element.prototype.matches =
		Element.prototype.msMatchesSelector ||
		Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
	Element.prototype.closest = function (s) {
		let el = this;

		do {
			if (el.matches(s)) return el;
			el = el.parentElement || el.parentNode;
		} while (el !== null && el.nodeType === 1);
		return null;
	};
}

function block_engine_getSiblings(element, criteria) {
	const children = Array.prototype.slice
		.call(element.parentNode.children)
		.filter((child) => child !== element);
	return criteria ? children.filter(criteria) : children;
}

Array.prototype.slice
	.call(document.getElementsByClassName("block-engine-expand-toggle-button"))
	.forEach((instance) => {
		instance.addEventListener("click", () => {
			const blockRoot = instance.closest(".block-engine-expand");
			blockRoot
				.querySelector(".block-engine-expand-partial .block-engine-expand-toggle-button")
				.classList.toggle("block-engine-hide");

			const expandingPart = Array.prototype.slice
				.call(blockRoot.children)
				.filter((child) => child.classList.contains("block-engine-expand-full"))[0];

			expandingPart.classList.toggle("block-engine-hide");

			let flickityInstances = Array.prototype.slice.call(
				expandingPart.querySelectorAll(".block_engine_image_slider")
			);

			flickityInstances.forEach((instance) => {
				let slider = Flickity.data(instance.querySelector("[data-flickity]"));
				slider.resize();
			});

			Array.prototype.slice
				.call(expandingPart.querySelectorAll(".wp-block-embed iframe"))
				.forEach((embeddedContent) => {
					embeddedContent.style.removeProperty("width");
					embeddedContent.style.removeProperty("height");
				});
		});
	});
