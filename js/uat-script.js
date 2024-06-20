jQuery(document).ready(function($) {
    var currentPage = 1;

    function fetchActivities() {
        var year = $('#uat-year-filter').val();
        var month = $('#uat-month-filter').val();
        var date = $('#uat-date-filter').val();
        var searchTerm = $('#uat-search').val();

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'uat_fetch_activities',
                year: year,
                month: month,
                security: uat_ajax_object.security,
                date: date,
                page: currentPage,
                search: searchTerm
            },
            success: function(response) {
                displayActivities(response.activities);
                updatePagination(response.total, response.per_page, response.page);
            }
        });
    }

    function displayActivities(activities) {
        var $tableBody = $('#uat-activity-table tbody');
        $tableBody.empty();

        activities.forEach(function(activity) {
            var row = '<tr>' +
                '<td>' + activity.user_id + '</td>' +
                '<td>' + activity.user_name + '</td>' +
                '<td>' + activity.post_id + '</td>' +
                '<td>' + activity.post_title + '</td>' +
                '<td>' + activity.post_type + '</td>' +
                '<td>' + activity.action + '</td>' +
                '<td>' + activity.page_reference + '</td>' +
                '<td>' + activity.timestamp + '</td>' +
                '</tr>';
            $tableBody.append(row);
        });
    }

    function updatePagination(total, perPage, page) {
        $('#uat-current-page').text(page);
        $('#uat-prev-page').prop('disabled', page <= 1);
        $('#uat-next-page').prop('disabled', page >= Math.ceil(total / perPage));
    }

    $('#uat-search').on('input', function() {
        fetchActivities();
    });

    $('#uat-year-filter, #uat-month-filter, #uat-date-filter').change(function() {
        currentPage = 1; // Reset to the first page when filters change
        fetchActivities();
    });

    $('#uat-prev-page').click(function() {
        if (currentPage > 1) {
            currentPage--;
            fetchActivities();
        }
    });

    $('#uat-next-page').click(function() {
        currentPage++;
        fetchActivities();
    });

    // Populate the year filter with the last 10 years
    var currentYear = new Date().getFullYear();
    for (var y = currentYear; y >= currentYear - 10; y--) {
        $('#uat-year-filter').append('<option value="' + y + '">' + y + '</option>');
    }
    $('#uat-year-filter').val(currentYear);

    // Populate the month and date filters
    var currentMonth = new Date().getMonth() + 1;
    if(currentMonth<10){
        currentMonth = '0'+currentMonth;
    }
    console.log(currentMonth);
    $('#uat-month-filter').val(currentMonth);

    var currentDate = new Date().getDate();
    $('#uat-date-filter').val(currentDate);

    // Fetch activities for the first time
    fetchActivities();
});
