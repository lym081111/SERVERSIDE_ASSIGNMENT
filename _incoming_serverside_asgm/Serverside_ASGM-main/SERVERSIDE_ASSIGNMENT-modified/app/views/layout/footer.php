<style>
    /* 1. Make the filter table fit in the box and add a horizontal scrollbar */
    .table-responsive-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 10px;
    }
    
    .table-responsive-wrapper table {
        min-width: 800px; 
        width: 100%;
    }

    /* 2. Pagination controls styling */
    .pagination-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
        margin-bottom: 25px;
        padding: 10px;
    }

    .pagination-btn {
        padding: 8px 16px;
        background-color: var(--primary, #2563eb);
        color: #ffffff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        transition: background-color 0.2s;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: var(--primary-color, #1e40af);
    }
    
    .pagination-btn:disabled {
        background-color: #94a3b8;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 14px;
        font-weight: 500;
        color: var(--muted, #334155);
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // ========================================================================
    // FEATURE 1: AUTO-FILL STUDENT EMAIL/ID FOR ADMIN FORMS
    // ========================================================================
    <?php if (!empty($_SESSION['isAdmin']) && class_exists('User')): ?>
        // Safely fetch all registered users to use for autofill matching
        const adminUsers = <?= json_encode(User::getAll()) ?>;

        // Find standard inputs used across the different admin forms
        const idInputs = document.querySelectorAll('input[name="studentId"], input[name="student_id"]');
        const nameSelects = document.querySelectorAll('select[name="selectedUserId"]');
        const emailInputs = document.querySelectorAll('input[name="studentEmail"], input[name="email"]');

        function autofillUserData(user, source) {
            if (!user) return;
            
            // Auto-fill the Email input
            emailInputs.forEach(el => {
                if (source !== 'email') el.value = user.email;
            });
            
            // Auto-fill the Student ID input
            idInputs.forEach(el => {
                if (source !== 'id') el.value = user.student_id;
            });

            // Auto-select the Student Name dropdown
            nameSelects.forEach(el => {
                if (source !== 'select') el.value = user.userID;
            });
        }

        // Trigger when typing a Student ID
        idInputs.forEach(input => {
            input.addEventListener('input', function() {
                const user = adminUsers.find(u => u.student_id === this.value.trim());
                if (user) autofillUserData(user, 'id');
            });
        });

        // Trigger when choosing a Student Name from a dropdown
        nameSelects.forEach(select => {
            select.addEventListener('change', function() {
                const user = adminUsers.find(u => u.userID == this.value);
                if (user) autofillUserData(user, 'select');
            });
        });
    <?php endif; ?>

    // ========================================================================
    // FEATURE 2: RESPONSIVE TABLES & PAGINATION
    // ========================================================================
    const tables = document.querySelectorAll("table");

    tables.forEach((table) => {
        // --- Responsive Wrapper for Horizontal Scroll ---
        if (!table.parentElement.classList.contains('table-responsive-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive-wrapper';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }

        // --- Pagination Logic ---
        const rowsPerPage = 5; 
        const allRows = Array.from(table.querySelectorAll('tr'));
        if (allRows.length === 0) return;

        // Verify it's a listing data table
        const firstRow = allRows[0];
        const isDataTable = firstRow.querySelectorAll('th').length > 1 && firstRow.querySelectorAll('td').length === 0;
        if (!isDataTable) return;

        let dataRows = allRows.slice(1);
        dataRows = dataRows.filter(row => !row.querySelector('td[colspan]')); // Ignore "No records found" row

        if (dataRows.length === 0) return;

        let currentPage = 1;
        const totalPages = Math.ceil(dataRows.length / rowsPerPage);

        // Create pagination DOM elements
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-controls';

        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '&laquo; Prev';
        prevBtn.className = 'pagination-btn';

        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = 'Next &raquo;';
        nextBtn.className = 'pagination-btn';

        const pageInfo = document.createElement('span');
        pageInfo.className = 'pagination-info';

        paginationContainer.appendChild(prevBtn);
        paginationContainer.appendChild(pageInfo);
        paginationContainer.appendChild(nextBtn);

        table.parentElement.parentNode.insertBefore(paginationContainer, table.parentElement.nextSibling);

        function updateTable() {
            dataRows.forEach(row => row.style.display = 'none');
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            for (let i = start; i < end && i < dataRows.length; i++) {
                dataRows[i].style.display = ''; 
            }
            pageInfo.innerText = `Page ${currentPage} of ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;
        }

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });

        updateTable();
    });
});
</script>
    </body>
</html>