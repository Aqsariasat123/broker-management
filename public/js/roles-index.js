
  function switchTab(roleId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('tab-content-' + roleId);
    if (selectedContent) {
      selectedContent.classList.add('active');
    }
    
    // Add active class to selected tab button
    const selectedButton = document.getElementById('tab-btn-' + roleId);
    if (selectedButton) {
      selectedButton.classList.add('active');
    }
  }

  function toggleModule(moduleId) {
    const checkboxes = document.querySelectorAll(`input[data-module="${moduleId}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
  }
