
    <footer>
        <hr>
        <p>&copy; Maxmarrio Maxlev | BI23110264</p>
    </footer>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select all toggle buttons
        const dropdowns = document.querySelectorAll('.submenu-toggle');

        dropdowns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); // Stop default button behavior
                
                // Toggle the "show" class on the next sibling element (submenu-content)
                const content = this.nextElementSibling;
                content.classList.toggle('show');
                
                // Optional: Rotate the arrow icon if you have one
                this.classList.toggle('active');
            });
        });
    });
</script>