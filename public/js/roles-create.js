
  // Auto-generate slug from name
  document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value.toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
    document.getElementById('slug').value = slug;
  });
