$("#selectAll").click(function () {
    $('input:checkbox').not(this).prop('checked', this.checked);
});

$('.selectGroup').on('click', function() {
    var isChecked = $(this).prop('checked');
    $(this).closest('tr').find('.permissionCheckbox').prop('checked', isChecked);

    var totalGroups = $('.selectGroup').length;
    var checkedGroups = $('.selectGroup:checked').length;
    $('#selectAll').prop('checked', totalGroups === checkedGroups);
});

// Select all sub-permissions checkboxes
var permissionCheckboxes = document.querySelectorAll('.permissionCheckbox');

// Add event listener to each checkbox
permissionCheckboxes.forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        var group = checkbox.dataset.group; // Get the group of the changed checkbox
        var groupCheckboxes = document.querySelectorAll('.permissionCheckbox[data-group="' + group + '"]'); // Select all checkboxes in the same group

        // Check if all checkboxes in the group are checked
        var allChecked = true;
        groupCheckboxes.forEach(function(cb) {
            if (!cb.checked) {
                allChecked = false;
            }
        });

        // Update the corresponding selectGroup checkbox based on allChecked
        document.getElementById('selectGroup-' + group).checked = allChecked;
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Define a function to update the group checkbox state based on sub-permissions
    function updateGroupCheckbox(group) {
        var groupCheckboxes = document.querySelectorAll('.permissionCheckbox[data-group="' + group + '"]'); // Select all checkboxes in the group

        // Check if all checkboxes in the group are checked
        var allChecked = true;
        groupCheckboxes.forEach(function(cb) {
            if (!cb.checked) {
                allChecked = false;
            }
        });

        // Update the corresponding group checkbox based on allChecked
        document.getElementById('selectGroup-' + group).checked = allChecked;

        // Count the number of checked groups
        var checkedGroups = document.querySelectorAll('.permissionCheckbox[data-group="' + group + '"]:checked').length;

        // Count the total number of groups
        var totalGroups = document.querySelectorAll('.permissionCheckbox[data-group="' + group + '"]').length;

        // Update the "Select All" checkbox based on allGroupsChecked
        document.getElementById('selectAll').checked = (checkedGroups === totalGroups);
    }

    // Add event listeners for checkbox change
    var checkboxes = document.querySelectorAll('.permissionCheckbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var group = checkbox.dataset.group; // Get the group of the changed checkbox
            updateGroupCheckbox(group);
        });
    });

    // Call the updateGroupCheckbox function for each group on page load
    var groups = document.querySelectorAll('.permissionCheckbox');
    groups.forEach(function(checkbox) {
        var group = checkbox.dataset.group;
        updateGroupCheckbox(group);
    });
});