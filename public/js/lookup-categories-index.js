
let currentCategoryId = null;

function openCategoryDialog() {
  currentCategoryId = null;
  document.getElementById('categoryModalTitle').textContent = 'Add Category';
  document.getElementById('categoryForm').reset();
  document.getElementById('category_id').value = '';
  document.getElementById('category_active').checked = true;
  const modal = document.getElementById('categoryModal');
  document.getElementById('deleteValueBtn').style.display = 'none';

  modal.style.display = 'flex';
  modal.classList.add('show');
}

function closeCategoryDialog() {
  const modal = document.getElementById('categoryModal');
  modal.style.display = 'none';
  modal.classList.remove('show');
  document.getElementById('categoryForm').reset();
  currentCategoryId = null;
}
function deleteCategory() {
  if (!currentCategoryId) return;

  if (!confirm('Are you sure you want to delete this Category?')) return;

  const url = deleteUrlTemplate.replace(':id', currentCategoryId);

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
      closeCategoryDialog();

      // Reload the list or page
      window.location.reload();
    })
    .catch(err => {
      console.error('Delete error:', err);

      // Close modal
      closeCategoryDialog();
      window.location.reload();


    });
}
async function editCategory(id) {
  try {
    const response = await fetch(`/lookups/categories/${id}/edit`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    if (!response.ok) throw new Error('Network error');
    const category = await response.json();

    currentCategoryId = id;
    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = category.name || '';
    document.getElementById('category_active').checked = category.active ? true : false;
    document.getElementById('deleteValueBtn').style.display = 'inline-block';

    const modal = document.getElementById('categoryModal');
    modal.style.display = 'flex';
    modal.classList.add('show');
  } catch (error) {
    console.error('Error loading category:', error);
    alert('Error loading category details');
  }
}

async function saveCategory() {
  const form = document.getElementById('categoryForm');

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData();
  formData.append('name', document.getElementById('category_name').value);
  formData.append('active', document.getElementById('category_active').checked ? '1' : '0');

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  formData.append('_token', csrfToken);

  const url = currentCategoryId
    ? `/lookups/categories/${currentCategoryId}`
    : '/lookups/categories';

  if (currentCategoryId) {
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
      closeCategoryDialog();
      window.location.reload();
    } else {
      alert(data.message || 'Error saving category');
    }
  } catch (error) {
    console.error('Error saving category:', error);
    alert('Error saving category');
  }
}

// Close modal on backdrop click
document.getElementById('categoryModal').addEventListener('click', function (e) {
  if (e.target === this) {
    closeCategoryDialog();
  }
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeCategoryDialog();
  }
});

// Filter functions
function applyFilters() {
  const search = document.getElementById('searchInput').value;
  const active = document.getElementById('activeFilter').value;
  const url = new URL(window.location.href);

  if (search) {
    url.searchParams.set('search', search);
  } else {
    url.searchParams.delete('search');
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
  window.location.href = lookupCategoriesIndexRoute;
}

// Allow Enter key to trigger filter
document.getElementById('searchInput').addEventListener('keypress', function (e) {
  if (e.key === 'Enter') {
    applyFilters();
  }
});
