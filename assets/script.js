window.onload = function () {
	// Get the callsign from local storage if it exists
	var callsign = localStorage.getItem('callsign');
	if (callsign) {
		// If it exists, set it as the value of the #callsign_input input
		document.getElementById('current_callsign').innerText = callsign;
		console.info('Callsign found in local storage:', callsign);
	} else {
		console.warn('Callsign not found in local storage');
	}
}

document.addEventListener('DOMContentLoaded', () => {

	// Make each column sortable by alphabecial order
	const table = document.querySelector('table');
	const headers = table.querySelectorAll('th');
	headers.forEach(header => {
		header.addEventListener('click', () => {
			const index = Array.from(headers).indexOf(header);
			const rows = Array.from(table.querySelectorAll('tr')).slice(1);
			const sorted = rows.sort((a, b) => {
				const aText = a.querySelectorAll('td')[index].textContent;
				const bText = b.querySelectorAll('td')[index].textContent;
				return aText.localeCompare(bText);
			});
			table.innerHTML = '';
			table.appendChild(headers[0].parentElement);
			sorted.forEach(row => table.appendChild(row));
		});
	});
});
// Make table #results scrollable
const results = document.querySelector('#results');
results.style.overflow = 'auto';
results.style.height = '50vh';
