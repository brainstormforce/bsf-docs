function addAnchorLink( heading ) {
	const link = heading.innerText.replace(/([^A-Za-z0-9[\]{}_.:-])\s?/g, '-');
	heading.innerHTML = `
		<a href="#${link.toLowerCase()}" id="${link.toLowerCase()}" class="bsf-automatic-anchors">
			<i class="dashicons dashicons-paperclip"></i>
			${heading.innerHTML}
		</a>
	`;
}

function astraDocsScrollToView(hash = '') {
	if ( '' == hash ) return; // Return if hast is empty.

	element = document.querySelector( hash );
	if ( null === element) return; // return if element does not exist on the page.
	element.scrollIntoView(true);
}

const h2s = Array.from(document.querySelectorAll('#content h2'));
h2s.forEach(h2 => addAnchorLink(h2));
const h3s = Array.from(document.querySelectorAll('#content h3'));
h3s.forEach(h3 => addAnchorLink(h3));
const h4s = Array.from(document.querySelectorAll('#content h4'));
h4s.forEach(h4 => addAnchorLink(h4));
const h5s = Array.from(document.querySelectorAll('#content h5'));
h5s.forEach(h5 => addAnchorLink(h5));

hash = window.location.hash;
astraDocsScrollToView(hash);