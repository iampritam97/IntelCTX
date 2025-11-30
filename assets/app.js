let selectedAPTs = JSON.parse(localStorage.getItem('selectedAPTs') || '[]');

function toggleSelectAPT(id) {
  if (selectedAPTs.includes(id)) {
    selectedAPTs = selectedAPTs.filter(x => x !== id);
  } else {
    selectedAPTs.push(id);
  }
  localStorage.setItem('selectedAPTs', JSON.stringify(selectedAPTs));
  renderCompareBar();
}

function renderCompareBar() {
  const bar = document.getElementById('compare_bar');
  if (!bar) return;
  if (selectedAPTs.length >= 2) {
    bar.classList.remove('hidden');
    bar.innerHTML = `
      <div class="fixed bottom-0 left-0 w-full bg-white border-t border-border p-3 flex justify-between text-xs">
        <span class="font-medium text-primary">${selectedAPTs.length} groups selected</span>
        <a href="compare.php?ids=${selectedAPTs.join(',')}" class="bg-accent text-white px-4 py-2 rounded-md">Compare Now</a>
      </div>`;
  } else {
    bar.classList.add('hidden');
  }
}
