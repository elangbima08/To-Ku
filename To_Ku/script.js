document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const addTaskModal = document.getElementById('addTaskModal');
    const editTaskModal = document.getElementById('editTaskModal');
    const deleteTaskModal = document.getElementById('deleteTaskModal');
    const tambahTugasBtn = document.getElementById('tambahTugasBtn');
    const closeBtns = document.querySelectorAll('.close-btn');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const searchInput = document.getElementById('searchInput');
    
    // Open Add Task modal
    tambahTugasBtn.addEventListener('click', function() {
        openModal('addTaskModal');
        
        // Set default date values to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_mulai').value = today;
        document.getElementById('tanggal_selesai').value = today;
    });
    
    // Add event listeners to all edit buttons
    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const judul = this.getAttribute('data-judul');
            const deskripsi = this.getAttribute('data-deskripsi');
            const status = this.getAttribute('data-status');
            const tanggalMulai = this.getAttribute('data-tanggal-mulai');
            const tanggalSelesai = this.getAttribute('data-tanggal-selesai');
            
            document.getElementById('edit_task_id').value = id;
            document.getElementById('edit_judul').value = judul;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_tanggal_mulai').value = tanggalMulai;
            document.getElementById('edit_tanggal_selesai').value = tanggalSelesai;
            
            openModal('editTaskModal');
        });
    });
    
    // Add event listeners to all delete buttons
    document.querySelectorAll('.hapus-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('delete_task_id').value = id;
            openModal('deleteTaskModal');
        });
    });
    
    // Close buttons
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalId = this.closest('.modal').id;
            closeModal(modalId);
        });
    });
    
    // Cancel delete button
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            closeModal('deleteTaskModal');
        });
    }
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.task-table tbody tr');
            
            tableRows.forEach(function(row) {
                const judul = row.cells[1].textContent.toLowerCase();
                const deskripsi = row.cells[2].textContent.toLowerCase();
                
                if (judul.includes(searchValue) || deskripsi.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(function(modal) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    };
});