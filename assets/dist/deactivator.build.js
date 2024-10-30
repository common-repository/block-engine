/* eslint-disable */

if (window.block_engine) {
	window.block_engine.forEach(block => {
		if (!block.active) {
			wp.blocks.unregisterBlockType(block.name);
		}
	});
}
