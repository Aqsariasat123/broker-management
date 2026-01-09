
let currentValueId = null;

// Filter functions
function applyFilters() {
  const categoryId = document.getElementById('categoryFilter').value;
  const search = document.getElementById('searchInput').value;
  const code = document.getElementById('codeInput').value;
  const type = document.getElementById('typeInput').value;
  const active = document.getElementById('activeFilter').value;
  const url = new URL(window.location.href);

  if (categoryId) {
    url.searchParams.set('category_id', categoryId);
  } else {
    url.searchParams.delete('category_id');
  }

  if (search) {
    url.searchParams.set('search', search);
  } else {
    url.searchParams.delete('search');
  }

  if (code) {
    url.searchParams.set('code', code);
  } else {
    url.searchParams.delete('code');
  }

  if (type) {
    url.searchParams.set('type', type);
  } else {
    url.searchParams.delete('type');
  }

  if (active) {
    url.searchParams.set('active', active);
  } else {
    url.searchParams.delete('active');
  }

  url.searchParams.delete('page'); // Reset to first page
  window.location.href = url.toString();
}

function clearFilters() {
  window.location.href = lookupValuesIndexRoute;
}

// Allow Enter key to trigger filter
document.getElementById('searchInput').addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    applyFilters();
  }
});

document.getElementById('codeInput').addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    applyFilters();
  }
});

document.getElementById('typeInput').addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    applyFilters();
  }
});

function openValueDialog() {
  currentValueId = null;
  document.getElementById('valueModalTitle').textContent = 'Add Value';
  document.getElementById('valueForm').reset();
  document.getElementById('value_id').value = '';
  document.getElementById('value_active').checked = true;
  document.getElementById('deleteValueBtn').style.display = 'none';

  // Set default category if filtered
  const categoryFilter = document.getElementById('categoryFilter').value;
  if (categoryFilter) {
    document.getElementById('value_category_id').value = categoryFilter;
  }
  const modal = document.getElementById('valueModal');
  modal.style.display = 'flex';
  modal.classList.add('show');
}

function closeValueDialog() {
  const modal = document.getElementById('valueModal');
  modal.style.display = 'none';
  modal.classList.remove('show');
  document.getElementById('valueForm').reset();
  currentValueId = null;
}

function deleteValue() {
  if (!currentValueId) return;

  if (!confirm('Are you sure you want to delete this value?')) return;

  const url = deleteUrlTemplate.replace(':id', currentValueId);

  fetch(url, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
    .then(async res => {
      // If response is not OK, read the error JSON for debugging
      if (!res.ok) {
        const errorData = await res.json().catch(() => ({}));
        throw new Error(errorData.message || 'Delete failed');
      }
      return res.json();
    })
    .then(data => {
      // Optional: show backend message
      alert(data.message || 'Value deleted successfully');

      // Close modal
      closeValueDialog();

      // Reload the list or page
      window.location.reload();
    })
    .catch(err => {
      console.error('Delete error:', err);

      // Close modal
      closeValueDialog();
      window.location.reload();


    });
}

async function editValue(id) {
  try {
    const response = await fetch(`/lookups/values/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!response.ok) throw new Error('Network error');
    const value = await response.json();

    currentValueId = id;
    document.getElementById('valueModalTitle').textContent = 'Edit Value';
    document.getElementById('value_id').value = id;
    document.getElementById('value_category_id').value = value.lookup_category_id || '';
    document.getElementById('value_seq').value = value.seq || '';
    document.getElementById('value_name').value = value.name || '';
    document.getElementById('value_code').value = value.code || '';
    document.getElementById('value_type').value = value.type || '';
    document.getElementById('value_description').value = value.description || '';
    document.getElementById('value_active').checked = value.active ? true : false;
    document.getElementById('deleteValueBtn').style.display = 'inline-block';

    const modal = document.getElementById('valueModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
  } catch (error) {
    console.error('Error loading value:', error);
    alert('Error loading value details');
  }
}

async function saveValue() {
  const form = document.getElementById('valueForm');

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData();
  formData.append('lookup_category_id', document.getElementById('value_category_id').value);
  formData.append('seq', document.getElementById('value_seq').value);
  formData.append('name', document.getElementById('value_name').value);
  formData.append('code', document.getElementById('value_code').value);
  formData.append('type', document.getElementById('value_type').value);
  formData.append('description', document.getElementById('value_description').value);
  formData.append('active', document.getElementById('value_active').checked ? '1' : '0');

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  formData.append('_token', csrfToken);

  const url = currentValueId
    ? `/lookups/values/${currentValueId}`
    : lookupValuesStoreRoute;

  if (currentValueId) {
    formData.append('_method', 'PUT');
  }

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    });

    const data = await response.json();

    if (response.ok && data.success) {
      closeValueDialog();
      window.location.reload();
    } else {
      alert(data.message || 'Error saving value');
    }
  } catch (error) {
    console.error('Error saving value:', error);
    alert('Error saving value');
  }
}

// Close modal on backdrop click
document.getElementById('valueModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeValueDialog();
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeValueDialog();
  }
});
