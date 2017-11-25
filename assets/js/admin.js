var admin = {
    adminCharts: function (date, year) {
        var data = {
            "action": 'run-query',
            "group-by-1": 'awardee',
            "createChart": true,
            "chart-type": 'pie',
            "rules": '{ "condition": "AND", "rules": [ { "id": "AwardDate", "field": "AwardDate", "type": "date", "input": "text", "operator": "greater_or_equal", "value": "' + date + '" }, { "id": "AwardLabel", "field": "AwardLabel", "type": "string", "input": "select", "operator": "equal", "value": "Employee of the Month" } ], "valid": true }'
        };
        $.ajax({
            url: '/admin/reports.php',
            method: "POST",
            data: data
        }).done(function (response) {
            response = JSON.parse(response);
            report.createPieChart(response.data, 'chart-container-1', 'Employee of the Month - ' + year);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#chart-container-1').html('<div class="alert alert-danger">Error: Employee of the Month Pie Chart failed to load</div>');
        });
    }
};