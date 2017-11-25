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
            if (response.noData === true) {
                $('#chart-container-1').html('<div class="alert alert-info">No Data for Employee of the Month Pie Chart</div>');
            } else {
                report.createPieChart(response.data, 'chart-container-1', 'Employee of the Month - ' + year);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#chart-container-1').html('<div class="alert alert-danger">Error: Employee of the Month Pie Chart failed to load</div>');
        });


        var data2 = {
            "action": 'run-query',
            "group-by-1": 'award-type',
            "createChart": true,
            "chart-type": 'bar',
            "rules": '{ "condition": "AND", "rules": [ { "id": "AwardDate", "field": "AwardDate", "type": "date", "input": "text", "operator": "greater_or_equal", "value": "' + date + '" } ], "valid": true }'
        };
        $.ajax({
            url: '/admin/reports.php',
            method: "POST",
            data: data2
        }).done(function (response) {
            response = JSON.parse(response);
            if (response.noData === true) {
                $('#chart-container-2').html('<div class="alert alert-info">No Data for Employee of the Month Pie Chart</div>');
            } else {
                $('#chart-table-2').html(response.data);
                report.createBarChart('chart-container-2', 'Award Count By Type - ' + year, 'award-type');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            $('#chart-container-2').html('<div class="alert alert-danger">Error: Employee of the Month Pie Chart failed to load</div>');
        });
    }
};