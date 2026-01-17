
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.getElementById('lookupTable').getElementsByTagName('tr');
        for (let i = 1; i < rows.length; i++) {
            const category = rows[i].getAttribute('data-category');
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            if (category && category.includes(filter)) {
                found = true;
            } else {
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const text = cells[j].textContent.toLowerCase();
                        if (text.includes(filter)) {
                            found = true;
                            break;
                        }
                    }
                }
            }
            rows[i].style.display = found ? '' : 'none';
        }
    });

    // Modal helpers
    function openAddCategoryModal() {
        document.getElementById('addCategoryForm').reset();
        document.getElementById('addCategoryModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function openEditCategoryModal(categoryId, categoryName, isActive) {
        document.getElementById('editCategoryName').value = categoryName;
        document.getElementById('editCategoryActive').checked = isActive;
        document.getElementById('editCategoryForm').action = `/lookups/categories/${categoryId}`;
        document.getElementById('editCategoryModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function openAddValueModal(categoryId, categoryName) {
        document.getElementById('addValueCategoryName').textContent = categoryName;
        document.getElementById('addValueCategoryId').value = categoryId;
        document.getElementById('addValueForm').action = `/lookups/categories/${categoryId}/values`;
        document.getElementById('addValueForm').reset();
        document.getElementById('addValueModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function openEditValueModal(valueId, seq, name, description, code, isActive) {
        document.getElementById('editValueSeq').value = seq;
        document.getElementById('editValueName').value = name;
        document.getElementById('editValueDescription').value = description;
        document.getElementById('editValueCode').value = code;
        document.getElementById('editValueActive').checked = isActive;
        document.getElementById('editValueForm').action = `/lookups/values/${valueId}`;
        document.getElementById('editValueModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
        document.body.style.overflow = '';
    }
    // Close modal on backdrop click or ESC
    document.querySelectorAll('.modal').forEach(function(modal){
        modal.addEventListener('click', function(e){
            if(e.target === modal) closeModal(modal.id);
        });
    });
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
            document.querySelectorAll('.modal.show').forEach(function(modal){
                closeModal(modal.id);
            });
        }
    });
    // Form submission handlers
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) { });
    document.getElementById('editCategoryForm').addEventListener('submit', function(e) { });
    document.getElementById('addValueForm').addEventListener('submit', function(e) { });
    document.getElementById('editValueForm').addEventListener('submit', function(e) { });
