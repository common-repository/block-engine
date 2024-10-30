document.addEventListener("DOMContentLoaded", function() {
	setTimeout(() => {
		Array.prototype.slice
			.call(document.getElementsByClassName("block_engine_progress-bar"))
			.forEach(instance => {
				instance.classList.add("block_engine_progress-bar-filled");
			});
	}, 500);
});
